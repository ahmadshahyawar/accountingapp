using System.Diagnostics;
using Microsoft.Win32;

namespace UnicAccountingUninstall
{
    /// <summary>
    /// Dropped into the install folder alongside UnicAccountingShell.exe (see
    /// publish.ps1) so the registry UninstallString written by
    /// UnicAccounting.Setup's installer (see MainWindow.xaml.cs) has something
    /// real to point at — otherwise "Apps &amp; Features" lists the app with no
    /// working uninstall action. Mirrors the HSG-desktop uninstaller pattern
    /// (HSG.Uninstall/Program.cs).
    /// </summary>
    internal static class Program
    {
        internal const string UninstallRegistryKey = @"Software\Microsoft\Windows\CurrentVersion\Uninstall\UnicAccounting";

        // Must stay ASCII and match UnicAccounting.Setup/MainWindow.xaml.cs's
        // shortcutPath exactly — see the comment on CreateDesktopShortcut there
        // for why a Persian filename breaks WshShortcut.Save() on this
        // machine's ANSI codepage.
        internal const string ShortcutName = "Unic Accounting.lnk";

        [STAThread]
        private static void Main()
        {
            var installDir = AppContext.BaseDirectory.TrimEnd('\\', '/');

            var confirm = MessageBox.Show(
                $"سیستم حسابداری یونیک از این مسیر حذف شود؟\n{installDir}\n\n" +
                "این کار فقط فایل‌های برنامه را حذف می‌کند — پایگاه داده شما " +
                "(%LOCALAPPDATA%\\UnicAccounting) دست‌نخورده باقی می‌ماند.",
                "حذف سیستم حسابداری یونیک",
                MessageBoxButtons.YesNo,
                MessageBoxIcon.Warning,
                MessageBoxDefaultButton.Button2);

            if (confirm != DialogResult.Yes)
            {
                return;
            }

            StopRunningProcesses(installDir);
            RemoveDesktopShortcut();
            RemoveUninstallRegistryKey();
            SelfDeleteInstallDirectory(installDir);
        }

        /// <summary>Kills any UnicAccountingShell.exe/php.exe running from this specific install (not some other copy).</summary>
        private static void StopRunningProcesses(string installDir)
        {
            foreach (var processName in new[] { "UnicAccountingShell", "php" })
            {
                foreach (var process in Process.GetProcessesByName(processName))
                {
                    try
                    {
                        var path = process.MainModule?.FileName;
                        if (path != null && path.StartsWith(installDir, StringComparison.OrdinalIgnoreCase))
                        {
                            process.Kill(entireProcessTree: true);
                            process.WaitForExit(3000);
                        }
                    }
                    catch
                    {
                        // best-effort — a process that's already exiting or that we can't
                        // inspect (permissions) shouldn't block the rest of the uninstall
                    }
                }
            }
        }

        private static void RemoveDesktopShortcut()
        {
            var desktop = Environment.GetFolderPath(Environment.SpecialFolder.DesktopDirectory);
            var shortcutPath = Path.Combine(desktop, ShortcutName);
            if (File.Exists(shortcutPath))
            {
                File.Delete(shortcutPath);
            }
        }

        private static void RemoveUninstallRegistryKey()
        {
            Registry.CurrentUser.DeleteSubKeyTree(UninstallRegistryKey, throwOnMissingSubKey: false);
        }

        /// <summary>
        /// A running exe can't delete its own file, so this process deletes
        /// everything else in the install folder itself, then hands off to a
        /// short-lived detached cmd.exe (started after this process has had
        /// time to exit) to remove what's left — its own exe and the now-
        /// empty folder.
        ///
        /// Retries with a growing delay rather than a single fixed wait:
        /// this exe is a self-contained single-file publish (~115MB), and a
        /// single "wait 2s then rmdir" attempt was found — by actually
        /// running an uninstall and checking the folder afterward, not by
        /// inspection — to sometimes still hit ERROR_SHARING_VIOLATION on
        /// its own exe, since releasing that large a memory-mapped bundle
        /// can occasionally take a little longer than 2 seconds after the
        /// process exits. Three attempts with 2s/3s/4s waits comfortably
        /// covers that without meaningfully slowing down the common case.
        /// </summary>
        private static void SelfDeleteInstallDirectory(string installDir)
        {
            var selfExe = Environment.ProcessPath;

            foreach (var entry in Directory.GetFileSystemEntries(installDir))
            {
                if (string.Equals(entry, selfExe, StringComparison.OrdinalIgnoreCase))
                {
                    continue;
                }
                try
                {
                    if (Directory.Exists(entry)) Directory.Delete(entry, recursive: true);
                    else File.Delete(entry);
                }
                catch
                {
                    // a locked file here just means it's left behind — the rest of the
                    // uninstall (shortcut, registry entry, running processes) still succeeded
                }
            }

            var quotedDir = $"\"{installDir}\"";
            var script = $"/C timeout /t 2 /nobreak >nul & rmdir /s /q {quotedDir} " +
                         $"& if exist {quotedDir} (timeout /t 3 /nobreak >nul & rmdir /s /q {quotedDir}) " +
                         $"& if exist {quotedDir} (timeout /t 4 /nobreak >nul & rmdir /s /q {quotedDir})";

            var psi = new ProcessStartInfo
            {
                FileName = "cmd.exe",
                Arguments = script,
                CreateNoWindow = true,
                UseShellExecute = false,
                WindowStyle = ProcessWindowStyle.Hidden,
            };
            Process.Start(psi);
        }
    }
}
