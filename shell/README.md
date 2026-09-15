# UnicAccountingShell — desktop wrapper

A thin WPF window that boots the Laravel app with PHP's built-in server on a
free local port and displays it in a WebView2 control — the same shape as
the plan's original "web app in a desktop shell" decision.

## Run it (dev)

Requires `php` on PATH and the [WebView2 Runtime](https://developer.microsoft.com/microsoft-edge/webview2/)
(pre-installed on current Windows 10/11).

```
dotnet run --project shell
```

It walks up from its own folder looking for `artisan` to find the Laravel
root, so running from `shell/bin/Debug/...` during development works as-is.

## How it works

- `MainWindow.xaml.cs` picks a free TCP port, starts
  `php artisan serve --host=127.0.0.1 --port=<port>` with the Laravel root
  as its working directory, polls it until it answers, then points the
  WebView2 control at it.
- **Per-user database, not the repo's dev one.** The real database always
  lives at `%LOCALAPPDATA%\UnicAccounting\database.sqlite`, passed to PHP via
  the `DB_DATABASE` environment variable (never the app folder itself, which
  is read-only once installed under Program Files). On first run — that file
  not existing yet — it runs `php artisan migrate --seed --force` against it
  before starting the server, so a fresh install bootstraps its own schema
  and default admin user with no manual setup step.
- **Both PHP processes' output is drained continuously**, not read only once
  they exit. `php artisan serve` runs indefinitely and logs every request;
  reading stdout only after `WaitForExitAsync()` (or reading stderr but not
  stdout at all) deadlocks the child process once its pipe buffer fills —
  it blocks trying to write, the shell blocks waiting for it to finish.
  Found by actually running this end-to-end, not by inspection: the first
  build hung forever on "در حال آماده‌سازی پایگاه داده (اولین اجرا)..." the
  very first time a clean `%LOCALAPPDATA%` triggered the migrate step. The
  server's output is drained into `%LOCALAPPDATA%\UnicAccounting\server.log`
  for the same reason, and so a "the app won't start" report has something
  to check.
- The WPF window itself stays `FlowDirection=LeftToRight` — the web content
  handles its own `dir="rtl"` HTML, and mirroring the WPF container on top
  of that double-flips every glyph. Only the transient native loading
  overlay opts into RTL for its own Persian text.
- Closing the window kills the PHP server process tree.

## Not done yet (packaging)

This still runs against a `php` on PATH — it is not yet a self-contained
installer a user can double-click with nothing preinstalled. `FindPhpExecutable()`
already prefers a bundled `shell/php/php.exe` next to the exe if one exists,
so what's left:

1. Bundle a portable PHP build (e.g. from windows.php.net) into
   `shell/php/` at publish time, or switch to a compiled runtime like
   static-php-cli — nothing in the shell code needs to change for this.
2. Copy the Laravel app (minus `node_modules`, `tests`, `.git`) into
   `shell/app_root/` alongside it — `FindLaravelRoot()` already checks
   there first. Run `composer install --no-dev --optimize-autoloader`
   inside that copy so `vendor/` ships too (a portable install has no
   Composer available to do this at runtime).
3. Don't run `php artisan config:cache` when assembling that bundle — a
   cached config bakes in whatever `.env` values were present at cache time,
   which would silently override the `DB_DATABASE` environment variable this
   shell sets at runtime and point every install back at one shared/baked-in
   database path.
4. `dotnet publish -c Release -r win-x64 --self-contained` and wire up an
   update mechanism, mirroring `HSG-desktop/publish-shell-update.ps1`
   (referenced in the original plan) once that pattern is available to copy.
