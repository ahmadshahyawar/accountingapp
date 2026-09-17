using System.IO;
using System.IO.Compression;
using System.Reflection;
using System.Windows;
using Microsoft.Win32;

namespace UnicAccountingSetup
{
    public partial class MainWindow : Window
    {
        // Written at the very end of the compiled exe by build-installer.ps1,
        // after appending the payload zip: [original exe][zip bytes][this footer].
        // Lets the running exe find its own embedded payload without needing
        // any external installer tooling. Mirrors the HSG-desktop pattern
        // (HSG.Setup/MainWindow.xaml.cs) with its own distinct magic string.
        private const string FooterMagic = "UNICPAYLOADEND!!"; // 16 bytes
        private string _installDir = "";

        public MainWindow()
        {
            InitializeComponent();
        }

        private void BtnBrowse_Click(object sender, RoutedEventArgs e)
        {
            var dialog = new OpenFolderDialog { Title = "پوشه نصب را انتخاب کنید" };
            if (dialog.ShowDialog() == true)
            {
                TxtInstallDir.Text = Path.Combine(dialog.FolderName, "UnicAccounting");
            }
        }

        private async void BtnInstall_Click(object sender, RoutedEventArgs e)
        {
            TxtError.Text = "";
            _installDir = TxtInstallDir.Text.Trim();

            if (string.IsNullOrWhiteSpace(_installDir))
            {
                TxtError.Text = "یک پوشه برای نصب انتخاب کنید.";
                return;
            }
            if (Directory.Exists(_installDir) && Directory.GetFileSystemEntries(_installDir).Length > 0)
            {
                TxtError.Text = "این پوشه از قبل خالی نیست. پوشه دیگری یا یک پوشه جدید انتخاب کنید.";
                return;
            }

            FormPanel.Visibility = Visibility.Collapsed;
            ProgressPanel.Visibility = Visibility.Visible;
            BtnInstall.Visibility = Visibility.Collapsed;

            try
            {
                await Task.Run(() => ExtractPayload(_installDir, ReportProgress));
                CreateDesktopShortcut(_installDir);
                WriteUninstallRegistryEntry(_installDir);

                ProgressPanel.Visibility = Visibility.Collapsed;
                DonePanel.Visibility = Visibility.Visible;
                BtnFinish.Visibility = Visibility.Visible;
            }
            catch (Exception ex)
            {
                ProgressPanel.Visibility = Visibility.Collapsed;
                FormPanel.Visibility = Visibility.Visible;
                BtnInstall.Visibility = Visibility.Visible;
                TxtError.Text = "نصب ناموفق بود: " + ex.Message;
            }
        }

        private void ReportProgress(int percent, string status)
        {
            Dispatcher.Invoke(() =>
            {
                ProgressBar.Value = percent;
                TxtProgressStatus.Text = status;
            });
        }

        /// <summary>Reads the zip payload appended to this exe's own file and extracts it.</summary>
        private static void ExtractPayload(string destDir, Action<int, string> progress)
        {
            var exePath = Environment.ProcessPath
                ?? throw new InvalidOperationException("مسیر فایل اجرایی در حال اجرا پیدا نشد.");

            using var fs = new FileStream(exePath, FileMode.Open, FileAccess.Read, FileShare.Read);

            var footerBytes = new byte[24];
            fs.Seek(-24, SeekOrigin.End);
            fs.ReadExactly(footerBytes);

            var magic = System.Text.Encoding.ASCII.GetString(footerBytes, 8, 16);
            if (magic != FooterMagic)
            {
                throw new InvalidOperationException(
                    "بسته نصب این فایل ناقص یا خراب است. لطفاً دوباره دانلود کنید.");
            }
            var originalExeSize = BitConverter.ToInt64(footerBytes, 0);
            var zipStart = originalExeSize;
            var zipLength = fs.Length - 24 - zipStart;

            progress(2, "در حال آماده‌سازی...");

            var tempZip = Path.Combine(Path.GetTempPath(), $"unic-setup-{Guid.NewGuid():N}.zip");
            fs.Seek(zipStart, SeekOrigin.Begin);
            using (var zipOut = File.Create(tempZip))
            {
                var buffer = new byte[1024 * 1024];
                long remaining = zipLength;
                long copied = 0;
                while (remaining > 0)
                {
                    var toRead = (int) Math.Min(buffer.Length, remaining);
                    var read = fs.Read(buffer, 0, toRead);
                    if (read <= 0) break;
                    zipOut.Write(buffer, 0, read);
                    remaining -= read;
                    copied += read;
                    progress((int) (5 + (copied * 30.0 / zipLength)), "در حال آماده‌سازی...");
                }
            }

            Directory.CreateDirectory(destDir);

            using (var archive = ZipFile.OpenRead(tempZip))
            {
                var total = archive.Entries.Count;
                var done = 0;
                foreach (var entry in archive.Entries)
                {
                    var destPath = Path.Combine(destDir, entry.FullName);
                    if (string.IsNullOrEmpty(entry.Name))
                    {
                        Directory.CreateDirectory(destPath);
                    }
                    else
                    {
                        Directory.CreateDirectory(Path.GetDirectoryName(destPath)!);
                        entry.ExtractToFile(destPath, overwrite: true);
                    }
                    done++;
                    if (done % 50 == 0 || done == total)
                    {
                        progress(35 + (int) (done * 65.0 / total), $"در حال نصب فایل‌ها... ({done}/{total})");
                    }
                }
            }

            try { File.Delete(tempZip); } catch { /* best-effort cleanup */ }
        }

        /// <summary>
        /// Creates a real .lnk via WScript.Shell, using late-bound COM
        /// automation (Type.GetTypeFromProgID + InvokeMember) rather than a
        /// compiled interop reference — ResolveComReference (needed for a
        /// <COMReference> item / IWshRuntimeLibrary) only works under the
        /// full .NET Framework MSBuild, not the `dotnet build`/`publish`
        /// toolchain this project uses. Late binding needs no interop
        /// assembly at all, just the COM component already on every
        /// Windows machine (wshom.ocx). Mirrors HSG.Setup's shortcut step.
        /// </summary>
        private void CreateDesktopShortcut(string installDir)
        {
            var exePath = Path.Combine(installDir, "UnicAccountingShell.exe");
            var desktop = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
            var shortcutPath = Path.Combine(desktop, "حسابداری یونیک.lnk");

            var shellType = Type.GetTypeFromProgID("WScript.Shell")
                ?? throw new InvalidOperationException("مؤلفه WScript.Shell در دسترس نیست.");
            var shell = Activator.CreateInstance(shellType)!;

            var shortcut = shellType.InvokeMember("CreateShortcut", BindingFlags.InvokeMethod, null, shell,
                new object[] { shortcutPath })!;
            var shortcutType = shortcut.GetType();

            shortcutType.InvokeMember("TargetPath", BindingFlags.SetProperty, null, shortcut, new object[] { exePath });
            shortcutType.InvokeMember("WorkingDirectory", BindingFlags.SetProperty, null, shortcut, new object[] { installDir });
            shortcutType.InvokeMember("Description", BindingFlags.SetProperty, null, shortcut,
                new object[] { "سیستم حسابداری یونیک" });
            shortcutType.InvokeMember("Save", BindingFlags.InvokeMethod, null, shortcut, null);
        }

        /// <summary>
        /// Writes the standard "Apps &amp; Features" / Control Panel uninstall
        /// entry, pointing at UnicAccounting-Uninstall.exe (dropped into the
        /// install folder alongside UnicAccountingShell.exe — see publish.ps1).
        /// Uses HKEY_CURRENT_USER, not HKLM: this installer never requests admin
        /// elevation (it installs to a user-writable folder like C:\UnicAccounting),
        /// and HKCU's uninstall key is exactly what Windows checks for a per-user
        /// listing — no elevation needed to write it either. The registry key
        /// name must match UnicAccounting.Uninstall's own copy exactly, or the
        /// uninstaller can't find and remove this same entry later.
        /// </summary>
        private void WriteUninstallRegistryEntry(string installDir)
        {
            const string keyPath = @"Software\Microsoft\Windows\CurrentVersion\Uninstall\UnicAccounting";
            using var key = Registry.CurrentUser.CreateSubKey(keyPath);

            var exePath = Path.Combine(installDir, "UnicAccountingShell.exe");
            var uninstallExePath = Path.Combine(installDir, "UnicAccounting-Uninstall.exe");

            key.SetValue("DisplayName", "سیستم حسابداری یونیک");
            key.SetValue("Publisher", "Unic Accounting");
            key.SetValue("InstallLocation", installDir);
            key.SetValue("UninstallString", $"\"{uninstallExePath}\"");
            key.SetValue("DisplayIcon", exePath);
            key.SetValue("NoModify", 1, RegistryValueKind.DWord);
            key.SetValue("NoRepair", 1, RegistryValueKind.DWord);

            long sizeBytes = 0;
            try
            {
                foreach (var file in Directory.GetFiles(installDir, "*", SearchOption.AllDirectories))
                {
                    sizeBytes += new FileInfo(file).Length;
                }
            }
            catch
            {
                // EstimatedSize is cosmetic (shown in Control Panel) — not worth failing install over.
            }
            key.SetValue("EstimatedSize", (int) (sizeBytes / 1024), RegistryValueKind.DWord);
        }

        private void BtnFinish_Click(object sender, RoutedEventArgs e)
        {
            if (ChkLaunchNow.IsChecked == true)
            {
                var exePath = Path.Combine(_installDir, "UnicAccountingShell.exe");
                if (File.Exists(exePath))
                {
                    System.Diagnostics.Process.Start(new System.Diagnostics.ProcessStartInfo(exePath)
                    {
                        WorkingDirectory = _installDir,
                        UseShellExecute = true,
                    });
                }
            }
            Close();
        }
    }
}
