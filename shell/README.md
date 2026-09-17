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

## Build the real standalone installer-ready folder

```
.\shell\publish.ps1 -PhpZip <path to a downloaded *-nts-Win32-*-x64.zip>
```

(The zip is only needed the first time — it's cached under
`shell/.publish-cache/` after that, so re-running the script with no
`-PhpZip` reuses it.) Produces `shell/dist/` — `UnicAccountingShell.exe`, a
bundled PHP, and a production copy of the app — a folder a user can copy
anywhere and double-click, with **nothing preinstalled**: no PHP, no
Composer, no Node, nothing but the WebView2 Runtime. Verified end-to-end:
cleared `%LOCALAPPDATA%`, ran the built `dist\UnicAccountingShell.exe`
directly, it reached the login page, and a curl login against the
dynamically-chosen port confirmed the seeded admin account authenticates
and reaches the real dashboard.

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

## Bundled-PHP pitfalls found by actually running the published build

Three real bugs surfaced only by running `dist\UnicAccountingShell.exe`
end-to-end after a clean `%LOCALAPPDATA%`, not by reading the code — each
is now guarded against, and worth knowing about if this ever needs
touching again:

1. **php.ini's `extension_dir = "ext"` is relative to the process's
   *working directory*, not to php.ini's own folder** — despite living
   right next to php.exe. Since every artisan call runs with
   `WorkingDirectory` set to the Laravel app root (not the php folder),
   that relative path silently resolved to nothing and every extension
   failed to load, no error, just a missing module. A `-d extension_dir=`
   command-line override does *not* fix this either: `php artisan serve`
   re-spawns its own inner worker via Laravel's `ServeCommand` using just
   `[php_binary(), '-S', ...]` (see
   `vendor/laravel/framework/.../ServeCommand.php`), which carries over
   none of the original process's command-line arguments — only the
   environment and whatever php.ini that re-spawned php.exe finds next to
   itself. So the fix has to live in the ini file: `MainWindow.xaml.cs`'s
   `EnsureBundledPhpIniIsPortable()` rewrites `extension_dir` to an
   absolute path, recomputed on every launch (not baked in once at publish
   time), so it keeps working even if the whole `dist` folder is moved or
   reinstalled elsewhere after publishing.
2. **`php.ini-production`'s CRLF line endings silently broke the regex
   that uncomments each `extension=` line** — a `(?m)^;extension=curl$`
   pattern doesn't match right before `\r\n` in .NET regex (only right
   before the `\n`), so PowerShell reported no error while zero extensions
   ever got enabled. `publish.ps1` now uses `\r?$` and explicitly verifies
   every required extension actually got uncommented, throwing if not, so
   this can't silently ship broken again.
3. **`storage/framework/{views,sessions,cache/data}` and
   `bootstrap/cache` need to physically exist**, not just be writable —
   they're `.gitignore`'d runtime state with nothing to copy, but Laravel
   doesn't create missing parents for them; the app 500'd with "Please
   provide a valid cache path" the moment the first Blade view tried to
   compile. `publish.ps1` creates these directories explicitly after
   copying the app.

Also worth remembering: never run `php artisan config:cache` when
assembling this bundle — a cached config bakes in whatever `.env` values
were present at cache time, which would silently override the
`DB_DATABASE` environment variable the shell sets at runtime and point
every install back at one shared/baked-in database path.

## Build the one-click installer

```
.\shell\build-installer.ps1
```

Runs `publish.ps1` first (so the payload can never go stale), zips its
`dist/` output, and appends that zip plus a small footer onto a
self-contained WPF setup wizard (`UnicAccounting.Setup`). The result is a
single file — `shell/dist-installer/UnicAccounting-Setup.exe` — with
**nothing preinstalled needed to build or run it**: no third-party installer
tooling (no Inno Setup/WiX), just `dotnet publish`. Copy that one exe
anywhere and double-click it.

Mirrors the HSG-desktop installer pattern (`HSG.Setup`/`HSG.Uninstall`/
`build-installer.ps1`) exactly, minus HSG's sync-specific `.env` templating
(this app has no sync feature to configure), with its own distinct 16-byte
footer magic string (`UNICPAYLOADEND!!`) so the two installers' payloads can
never be confused for each other.

What the wizard does on "نصب" (Install):
1. Reads the zip payload appended to its own running exe (via
   `Environment.ProcessPath` + the footer), extracts it into the chosen
   folder (default `C:\UnicAccounting`) with a progress bar.
2. Creates a `حسابداری یونیک.lnk` shortcut on the Desktop pointing at the
   extracted `UnicAccountingShell.exe`.
3. Writes an HKCU "Apps & Features" uninstall entry pointing at
   `UnicAccounting-Uninstall.exe` (also part of the payload, published
   alongside the shell by `publish.ps1`) — no admin elevation needed, since
   everything installs into a user-writable folder.

The per-user database, first-run migrate+seed, and local web server all
still work exactly as described above — the installer only extracts files
and registers the shortcut/uninstall entry; it does no Laravel-specific
setup of its own.

**Verified end-to-end**: ran `build-installer.ps1` to completion, confirmed
the produced exe's footer bytes and embedded zip parse correctly (the exact
logic `UnicAccounting.Setup/MainWindow.xaml.cs` runs at install time), then
launched the extracted `UnicAccountingShell.exe` from a clean temp folder
standalone (not from `shell/dist`) and confirmed via `server.log` that it
served `/login` and its built CSS/JS assets successfully.

## Not done yet

- **Update mechanism** — mirroring `HSG-desktop/publish-shell-update.ps1`
  (referenced in the original plan) once that pattern is available to copy.
- **Code signing** — the produced exe is unsigned, so Windows SmartScreen
  will show an "unknown publisher" warning on first run until it accrues
  enough reputation or is signed with a code-signing certificate.
