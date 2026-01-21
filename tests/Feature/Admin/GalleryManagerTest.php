<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class GalleryManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_image()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $file = UploadedFile::fake()->image('photo.jpg');

        Volt::test('admin.gallery-manager')
            ->call('create')
            ->set('photo', $file)
            ->set('order', 1)
            ->call('save')
            ->assertHasNoErrors();

        // Assert file was stored
        $this->assertDatabaseCount('gallery_images', 1);
        $image = GalleryImage::first();
        
        // The image_url should contain /storage/gallery/ and the hashed filename
        $this->assertStringContainsString('/storage/gallery/', $image->image_url);
        
        // Extract filename to verify storage
        $filename = basename($image->image_url);
        
        // We can check if any file exists in the gallery directory
        $files = Storage::disk('public')->files('gallery');
        $this->assertCount(1, $files);
    }
    
    public function test_admin_can_use_external_url()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Volt::test('admin.gallery-manager')
            ->call('create')
            ->set('image_url', 'https://example.com/image.jpg')
            ->set('order', 2)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('gallery_images', [
            'image_url' => 'https://example.com/image.jpg',
        ]);
    }
}