<?php

namespace Tests\Unit\Serve;

use Tests\TestCase;
use App\Models\User;
use App\Models\Playlist;
use App\Models\Property;
use App\Models\Organization;
use App\Services\Serve\PlaylistImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaylistImageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $thumbnail_image_path;
    protected $default_image_path;

    public function setUp()
    {
        parent::setUp();

        // Seed Data
        $this->org = factory(Organization::class)->create();
        $this->user = factory(User::class)->create();
        $this->cp = factory(Property::class)->states('cp')->create(['organization_id' => $this->org->id]);
        $this->thumbnail_image_path = 'images/2017/08/01/18/z9zUAMXwfk6NhqPG2caXDWckwFeo5FPCb62jIZMX.png';
        $this->default_image_path = url('/images/playlist-default.jpg');
    }

    public function testGetImageUrl()
    {
        $playlist = factory(Playlist::class)->create(['property_id' => $this->cp->id]);
        $url = PlaylistImageService::getImageUrl($playlist, $playlist->property_id, $playlist->updated_at->timestamp, 300);
        $url2 = route('serve.image.playlist', [
                    'prop_id' => $playlist->property_id,
                    'playlist' => $playlist->id,
                    'timestamp' => $playlist->updated_at->timestamp,
                    'width' => 300,
                ]);
        $this->assertEquals($url, $url2);
    }

    /**
     * test get image with CP Id with no thumnail path.
     */
    public function test_get_image_with_cp_id_with_no_thumbnail_path()
    {
        $playlist = factory(Playlist::class)->create(['property_id' => $this->cp->id]);
        $image = PlaylistImageService::getImage($playlist, $playlist->property_id, 300);

        $image2 = $this->default_image_path;

        $this->assertEquals($image, $image2);
    }

    public function test_get_image_with_invalid_cp_id()
    {
        $playlist = factory(Playlist::class)->create(['property_id' => $this->cp->id]);
        $image = PlaylistImageService::getImage($playlist, 123, 300);
        $image2 = $this->default_image_path;

        $this->assertEquals($image, $image2);
    }
}
