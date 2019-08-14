<?php

use App\Models\PropertyCP;
use Illuminate\Database\Seeder;
use App\Models\TermsOfMarketplace;

class SetPlaylistBelongsToTermsMarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $properties = PropertyCP::whereHas('playlists', function ($query) {
            $query->published()->whereNull('tom_id');
        })->with('playlists', 'termsOfMarketplaces', 'marketplaceTerm')->get();

        foreach ($properties as $property) {
            if ($property->termsOfMarketplaces->count() < 1) {
                $tom = TermsOfMarketplace::create([
                    'user_id' => $property->marketplaceTerm->created_by,
                    'property_id' => $property->id,
                    'name' => $property->name.'聚合分发平台条款',
                    'region_allowed' => $property->marketplaceTerm->allowed_regions,
                    'region_excepted' => $property->marketplaceTerm->excepted_regions,
                    'payment_mode' => 'free-download',
                    'api_share_to' => 'qq,headlines',
                    'download_resolution' => 'hd,low',
                ]);
            } else {
                $tom = $property->termsOfMarketplaces->first();
            }

            $property->playlists->each(function ($playlist) use ($tom) {
                $playlist->tom_id = $tom->id;
                $playlist->save();
            });
        }
    }
}
