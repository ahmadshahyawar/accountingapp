<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Covers the two things added to match the old app's تعریف جنس form:
 * the عکس (photo) uploader and the باکرد (barcode) display, neither of
 * which existed in this app before.
 */
class ItemFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
        Storage::fake('public');
    }

    public function test_create_and_edit_pages_render_including_the_barcode_script(): void
    {
        $this->get(route('items.create'))->assertOk();

        $item = Item::create([
            'code' => 'ITM-BC', 'name' => 'Barcoded Thing', 'unit_id' => Unit::first()->id,
            'cost_price' => 10, 'sale_price' => 20,
        ]);

        $this->get(route('items.edit', $item))
            ->assertOk()
            ->assertSee('item-barcode', false)
            ->assertSee('چاپ بارکد');
    }

    public function test_uploading_a_photo_stores_it_and_shows_it_on_the_form(): void
    {
        $unit = Unit::first();
        $photo = UploadedFile::fake()->image('item.jpg');

        $this->post(route('items.store'), [
            'code' => 'ITM-PH', 'name' => 'Photographed Thing', 'unit_id' => $unit->id,
            'cost_price' => 10, 'sale_price' => 20, 'photo' => $photo,
        ])->assertRedirect(route('items.index'));

        $item = Item::where('code', 'ITM-PH')->firstOrFail();
        $this->assertNotNull($item->photo_path);
        Storage::disk('public')->assertExists($item->photo_path);

        // @js() JSON-escapes forward slashes ("items/x.jpg" -> "items\/x.jpg") when
        // embedding the path into the x-data attribute — correct for Alpine to parse
        // client-side, just not a literal substring match, so assert on the escaped form.
        $this->get(route('items.edit', $item))
            ->assertOk()
            ->assertSee(addcslashes($item->photo_path, '/'), false);
    }

    public function test_removing_a_photo_deletes_the_file_and_clears_the_path(): void
    {
        $unit = Unit::first();
        $item = Item::create([
            'code' => 'ITM-RM', 'name' => 'Removable Photo Thing', 'unit_id' => $unit->id,
            'cost_price' => 10, 'sale_price' => 20,
            'photo_path' => UploadedFile::fake()->image('old.jpg')->store('items', 'public'),
        ]);
        Storage::disk('public')->assertExists($item->photo_path);
        $oldPath = $item->photo_path;

        $this->put(route('items.update', $item), [
            'code' => $item->code, 'name' => $item->name, 'unit_id' => $unit->id,
            'cost_price' => 10, 'sale_price' => 20, 'remove_photo' => '1',
        ])->assertRedirect(route('items.index'));

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertNull($item->fresh()->photo_path);
    }
}
