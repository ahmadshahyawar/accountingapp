<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Covers بک آپ اطلاعات (backup/restore) — another old-app screen flagged as
 * not built. The whole database is one SQLite file, so this exercises the
 * download/restore endpoints against a throwaway copy, never the real
 * testing database connection SQLite keeps open.
 */
class BackupTest extends TestCase
{
    use RefreshDatabase;

    private string $tempDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Point the backup controller at a standalone file so this test never
        // touches the live :memory:/testing connection sqlite normally uses.
        $this->tempDbPath = storage_path('framework/testing/backup-test-'.uniqid().'.sqlite');
        touch($this->tempDbPath);
        file_put_contents($this->tempDbPath, 'original-db-bytes');
        Config::set('database.connections.sqlite.database', $this->tempDbPath);
    }

    protected function tearDown(): void
    {
        foreach (glob(storage_path('framework/testing/backup-test-*.sqlite')) as $file) {
            @unlink($file);
        }
        foreach (glob(storage_path('framework/testing/pre-restore-*.sqlite')) as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function test_an_admin_can_download_the_current_database_file(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('backup.download'))
            ->assertOk()
            ->assertDownload();
    }

    public function test_a_regular_user_cannot_reach_backup_routes(): void
    {
        $regular = User::create([
            'name' => 'Regular User', 'email' => 'regular-backup@example.com',
            'password' => bcrypt('password'), 'role' => 'user', 'is_active' => true,
        ]);

        $this->actingAs($regular)->get(route('backup.download'))->assertForbidden();
    }

    public function test_restoring_replaces_the_database_file_and_keeps_a_safety_copy(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $newContent = 'restored-db-bytes';
        $upload = UploadedFile::fake()->createWithContent('restored.sqlite', $newContent);

        $this->actingAs($admin)
            ->post(route('backup.restore'), ['backup' => $upload])
            ->assertRedirect();

        $this->assertSame($newContent, file_get_contents($this->tempDbPath));

        $safetyCopies = glob(storage_path('framework/testing/pre-restore-*.sqlite'));
        $this->assertNotEmpty($safetyCopies, 'expected a pre-restore safety copy to be created');
        $this->assertSame('original-db-bytes', file_get_contents($safetyCopies[0]));
    }
}
