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

            StatusText.Text = "در حال راه‌اندازی سرور محلی...";
            _port = FindFreeTcpPort();
            _phpProcess = StartPhpServer(appRoot, _port);

            StatusText.Text = "در انتظار آماده شدن سرور...";
            var baseUrl = $"http://127.0.0.1:{_port}";
            await WaitForServerAsync(baseUrl);

            StatusText.Text = "در حال بارگذاری برنامه...";
            var userDataFolder = Path.Combine(
                Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
                "UnicAccountingShell", "WebView2");
            Directory.CreateDirectory(userDataFolder);

            var env = await CoreWebView2Environment.CreateAsync(userDataFolder: userDataFolder);
            await Browser.EnsureCoreWebView2Async(env);
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
    /// copied alongside the exe under an "app" subfolder.
    /// </summary>
    private static string FindLaravelRoot()
    {
        var candidates = new[]
        {
            Path.Combine(AppContext.BaseDirectory, "app_root"),
        };
        foreach (var c in candidates)
        {
            if (File.Exists(Path.Combine(c, "artisan"))) return c;
        }

        var dir = new DirectoryInfo(AppContext.BaseDirectory);
        while (dir != null)
        {
            if (File.Exists(Path.Combine(dir.FullName, "artisan"))) return dir.FullName;
            dir = dir.Parent;
        }

        throw new FileNotFoundException(
            "پوشه اصلی برنامه (Laravel) پیدا نشد — فایل artisan یافت نشد.");
    }

    private static int FindFreeTcpPort()
    {
        var listener = new TcpListener(System.Net.IPAddress.Loopback, 0);
        listener.Start();
        var port = ((System.Net.IPEndPoint)listener.LocalEndpoint).Port;
        listener.Stop();
        return port;
    }

    private static Process StartPhpServer(string appRoot, int port)
    {
        var psi = new ProcessStartInfo
        {
            FileName = "php",
            Arguments = $"artisan serve --host=127.0.0.1 --port={port}",
            WorkingDirectory = appRoot,
            UseShellExecute = false,
            CreateNoWindow = true,
            RedirectStandardOutput = true,
            RedirectStandardError = true,
        };

        var process = Process.Start(psi)
            ?? throw new InvalidOperationException("سرور PHP اجرا نشد.");
        return process;
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
