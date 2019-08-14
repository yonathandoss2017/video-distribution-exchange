<?php

use App\Models\Property;
use App\Models\MarketplaceTerm;
use Illuminate\Database\Seeder;

class AddMarketplaceTermToCpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Property::where('type', Property::TYPE_CP)->get()->map(function ($item, $key) {
            MarketplaceTerm::create([
                'property_id' => $item->id,
                'allowed_regions' => '001',
                'excepted_regions' => null,
                'platforms' => 'pc,smart-tv,theatre,mobile,ott-devices,dvd,tablet,tv,blu-ray',
                'exclusivity' => 'exclusive',
                'supported_models' => 'ad-supported',
                'revenue_share' => 'exclusive',
                'license_fee' => 'exclusive',
                'minimun_guarantee' => 'exclusive',
                'footnote' => 'Revenue Share and Licensing Fees are to be discussed.',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        });
    }
}
