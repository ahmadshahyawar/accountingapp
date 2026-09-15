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
- The WPF window itself stays `FlowDirection=LeftToRight` — the web content
  handles its own `dir="rtl"` HTML, and mirroring the WPF container on top
  of that double-flips every glyph. Only the transient native loading
  overlay opts into RTL for its own Persian text.
- Closing the window kills the PHP server process tree.

## Not done yet (packaging)

This still runs against a `php` on PATH and the dev SQLite file checked out
in the repo — it is not yet a self-contained installer. To ship a real
`.exe` a user can double-click with nothing else installed:

1. Bundle a portable PHP build (e.g. from windows.php.net) under
   `shell/app_root/php/` alongside a copy of the Laravel app, or switch to
   a compiled PHP runtime like static-php-cli.
2. Point `FindLaravelRoot()`'s `app_root` candidate at that bundled copy
   (already wired up — just needs the files to exist at publish time) and
   `StartPhpServer` at the bundled `php.exe` instead of relying on PATH.
3. Move the SQLite database to a writable per-user location
   (`%LOCALAPPDATA%\UnicAccounting\database.sqlite`) instead of shipping it
   inside the read-only install folder.
4. `dotnet publish -c Release -r win-x64 --self-contained` and wire up an
   update mechanism, mirroring `HSG-desktop/publish-shell-update.ps1`
   (referenced in the original plan) once that pattern is available to copy.
