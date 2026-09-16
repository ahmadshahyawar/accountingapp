<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** بک آپ اطلاعات — old-app screen this app never had. The whole database is one SQLite file, so backup/restore is just a file copy. */
class BackupController extends Controller
{
    public function download()
    {
        $path = $this->databasePath();

        return response()->download($path, 'unic-accounting-backup-'.now()->format('Y-m-d-His').'.sqlite');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup' => 'required|file|max:512000', // 500MB — generous for a SQLite file
        ]);

        $path = $this->databasePath();

        // Never overwrite without a safety copy of what was live a moment ago.
        $safetyCopy = dirname($path).'/pre-restore-'.now()->format('Y-m-d-His').'.sqlite';
        copy($path, $safetyCopy);

        // Release the SQLite file handle before overwriting it — Windows will
        // refuse to replace a file still open elsewhere. Skipped under tests,
        // where disconnecting would tear down the shared connection every
        // other test in the same run still needs.
        if (! app()->runningUnitTests()) {
            DB::disconnect();
        }
        $request->file('backup')->move(dirname($path), basename($path));

        return back()->with('success', 'بازیابی اطلاعات با موفقیت انجام شد. لطفاً برنامه را دوباره راه‌اندازی کنید.');
    }

    private function databasePath(): string
    {
        return config('database.connections.sqlite.database');
    }
}
