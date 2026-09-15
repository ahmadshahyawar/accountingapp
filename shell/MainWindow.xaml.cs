using System.Diagnostics;
using System.IO;
using System.Net.Http;
using System.Net.Sockets;
using System.Windows;
using Microsoft.Web.WebView2.Core;

namespace UnicAccountingShell;

/// <summary>
/// Boots the bundled Laravel app with PHP's built-in server on a free local
/// port, waits for it to answer, then points the WebView2 control at it.
/// Mirrors the HSG-desktop shell pattern: a thin native window whose whole
/// job is "run a local web app and show it" — no UI logic lives here.
/// </summary>
public partial class MainWindow : Window
{
    private Process? _phpProcess;
    private int _port;

    public MainWindow()
    {
        InitializeComponent();
        Loaded += MainWindow_Loaded;
        Closing += MainWindow_Closing;
    }

    private async void MainWindow_Loaded(object sender, RoutedEventArgs e)
    {
        try
        {
            StatusText.Text = "در حال یافتن مسیر برنامه...";
            var appRoot = FindLaravelRoot();
            var phpExe = FindPhpExecutable(appRoot);

            // The install folder is read-only once packaged (Program Files-style),
            // so the real, writable database always lives per-user — never inside
            // the app folder itself, and never the dev database.sqlite committed
            // to the repo for `dotnet run`/tests.
            var dataDir = Path.Combine(
                Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
                "UnicAccounting");
            Directory.CreateDirectory(dataDir);
            var dbPath = Path.Combine(dataDir, "database.sqlite");
            var isFirstRun = ! File.Exists(dbPath);

            var env = new Dictionary<string, string>
            {
                ["DB_CONNECTION"] = "sqlite",
                ["DB_DATABASE"] = dbPath,
            };

            if (isFirstRun)
            {
                StatusText.Text = "در حال آماده‌سازی پایگاه داده (اولین اجرا)...";
                File.WriteAllBytes(dbPath, []);
                await RunArtisanAsync(phpExe, appRoot, "migrate --seed --force", env);
            }

            StatusText.Text = "در حال راه‌اندازی سرور محلی...";
            _port = FindFreeTcpPort();
            var serverLogPath = Path.Combine(dataDir, "server.log");
            _phpProcess = StartPhpServer(phpExe, appRoot, _port, env, serverLogPath);

            StatusText.Text = "در انتظار آماده شدن سرور...";
            var baseUrl = $"http://127.0.0.1:{_port}";
            await WaitForServerAsync(baseUrl);

            StatusText.Text = "در حال بارگذاری برنامه...";
            var webViewDataFolder = Path.Combine(dataDir, "WebView2");
            Directory.CreateDirectory(webViewDataFolder);

            var webViewEnv = await CoreWebView2Environment.CreateAsync(userDataFolder: webViewDataFolder);
            await Browser.EnsureCoreWebView2Async(webViewEnv);
            Browser.CoreWebView2.NavigationCompleted += (_, args) =>
            {
                if (args.IsSuccess) LoadingOverlay.Visibility = Visibility.Collapsed;
            };
            Browser.Source = new Uri(baseUrl);
        }
        catch (Exception ex)
        {
            StatusText.Text = $"خطا در راه‌اندازی: {ex.Message}";
            MessageBox.Show(
                $"برنامه راه‌اندازی نشد.\n\n{ex}",
                "خطا", MessageBoxButton.OK, MessageBoxImage.Error);
        }
    }

    /// <summary>
    /// Walks up from the executable's folder looking for the Laravel app root
    /// (identified by its "artisan" CLI script) — works both in dev (exe sits
    /// under shell/bin/Debug/...) and once packaged, if the published app is
    /// copied alongside the exe under an "app_root" subfolder.
    /// </summary>
    private static string FindLaravelRoot()
    {
        var bundled = Path.Combine(AppContext.BaseDirectory, "app_root");
        if (File.Exists(Path.Combine(bundled, "artisan"))) return bundled;

        var dir = new DirectoryInfo(AppContext.BaseDirectory);
        while (dir != null)
        {
            if (File.Exists(Path.Combine(dir.FullName, "artisan"))) return dir.FullName;
            dir = dir.Parent;
        }

        throw new FileNotFoundException(
            "پوشه اصلی برنامه (Laravel) پیدا نشد — فایل artisan یافت نشد.");
    }

    /// <summary>
    /// Prefers a portable PHP bundled next to the exe (shell/php/php.exe once
    /// published — see shell/README.md) so an end user needs nothing
    /// preinstalled; falls back to "php" on PATH for local development.
    /// </summary>
    private static string FindPhpExecutable(string appRoot)
    {
        var bundled = Path.Combine(AppContext.BaseDirectory, "php", "php.exe");
        return File.Exists(bundled) ? bundled : "php";
    }

    private static int FindFreeTcpPort()
    {
        var listener = new TcpListener(System.Net.IPAddress.Loopback, 0);
        listener.Start();
        var port = ((System.Net.IPEndPoint)listener.LocalEndpoint).Port;
        listener.Stop();
        return port;
    }

    private static void ApplyEnvironment(ProcessStartInfo psi, IReadOnlyDictionary<string, string> env)
    {
        foreach (var (key, value) in env)
        {
            psi.EnvironmentVariables[key] = value;
        }
    }

    private static Process StartPhpServer(string phpExe, string appRoot, int port, IReadOnlyDictionary<string, string> env, string logPath)
    {
        var psi = new ProcessStartInfo
        {
            FileName = phpExe,
            Arguments = $"artisan serve --host=127.0.0.1 --port={port}",
            WorkingDirectory = appRoot,
            UseShellExecute = false,
            CreateNoWindow = true,
            RedirectStandardOutput = true,
            RedirectStandardError = true,
        };
        ApplyEnvironment(psi, env);

        var process = Process.Start(psi)
            ?? throw new InvalidOperationException("سرور PHP اجرا نشد.");

        // Drain continuously via the event-based reader, not ReadToEndAsync -
        // this process runs indefinitely, so anything that only reads once
        // the process exits would deadlock the same way the first-run migrate
        // step did (see RunArtisanAsync's comment) once enough request-log
        // output fills the pipe buffer. Logged to disk so a "it won't start"
        // report from a packaged install has something to look at.
        var logWriter = new StreamWriter(logPath, append: false) { AutoFlush = true };
        process.OutputDataReceived += (_, args) => { if (args.Data != null) logWriter.WriteLine(args.Data); };
        process.ErrorDataReceived += (_, args) => { if (args.Data != null) logWriter.WriteLine(args.Data); };
        process.BeginOutputReadLine();
        process.BeginErrorReadLine();
        process.Exited += (_, _) => logWriter.Dispose();
        process.EnableRaisingEvents = true;

        return process;
    }

    /// <summary>
    /// Runs a one-shot artisan command (e.g. first-run migrate+seed) and waits
    /// for it to finish, throwing with its stderr output if it fails — a
    /// silent failure here would otherwise surface later as a confusing
    /// "table not found" once the server starts against an empty database.
    /// </summary>
    private static async Task RunArtisanAsync(string phpExe, string appRoot, string arguments, IReadOnlyDictionary<string, string> env)
    {
        var psi = new ProcessStartInfo
        {
            FileName = phpExe,
            Arguments = $"artisan {arguments}",
            WorkingDirectory = appRoot,
            UseShellExecute = false,
            CreateNoWindow = true,
            RedirectStandardOutput = true,
            RedirectStandardError = true,
        };
        ApplyEnvironment(psi, env);

        using var process = Process.Start(psi)
            ?? throw new InvalidOperationException("دستور artisan اجرا نشد.");

        // Read both streams concurrently, not sequentially: artisan migrate/seed
        // writes plenty to stdout, and reading only stderr while stdout's pipe
        // buffer fills up deadlocks the child process — it blocks trying to
        // write, we block in WaitForExitAsync waiting for it to finish. Found
        // by actually running this, not by inspection: the app hung forever on
        // "در حال آماده‌سازی پایگاه داده (اولین اجرا)..." on first launch.
        var stdoutTask = process.StandardOutput.ReadToEndAsync();
        var stderrTask = process.StandardError.ReadToEndAsync();
        await Task.WhenAll(stdoutTask, stderrTask, process.WaitForExitAsync());

        if (process.ExitCode != 0)
        {
            throw new InvalidOperationException(
                $"آماده‌سازی پایگاه داده ناموفق بود (artisan {arguments}):\n{await stderrTask}\n{await stdoutTask}");
        }
    }

    private static async Task WaitForServerAsync(string baseUrl, int timeoutMs = 15000)
    {
        using var client = new HttpClient { Timeout = TimeSpan.FromSeconds(2) };
        var deadline = DateTime.UtcNow.AddMilliseconds(timeoutMs);

        while (DateTime.UtcNow < deadline)
        {
            try
            {
                var response = await client.GetAsync(baseUrl);
                return; // any HTTP response (even an error page) means the server is up
            }
            catch
            {
                await Task.Delay(200);
            }
        }

        throw new TimeoutException("سرور محلی در زمان تعیین شده پاسخ نداد.");
    }

    private void MainWindow_Closing(object? sender, System.ComponentModel.CancelEventArgs e)
    {
        try
        {
            if (_phpProcess is { HasExited: false })
            {
                _phpProcess.Kill(entireProcessTree: true);
            }
        }
        catch
        {
            // best-effort cleanup — nothing to do if it's already gone
        }
    }
}
