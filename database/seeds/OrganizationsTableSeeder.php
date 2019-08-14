<?php

use App\Models\Property;
use App\Models\Organization;
use App\Models\PropertyMeta;
use Illuminate\Support\Facades\App;

class OrganizationsTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $allows = [
            'allow_ivideomobile' => 1,
            'allow_livestream' => 1,
            'allow_marketplace' => 1,
        ];

        factory(Organization::class)->states('superadmin')->create(array_merge($allows, [
            'id' => 0,
        ]));

        factory(Property::class)->states('superadmin')->create(array_merge($allows, [
            'organization_id' => self::ORG_ID_FOR_SUPER_ADMIN,
        ]));

        if (!App::environment(['production', 'staging'])) {
            $orgs = [
                '湖北卫视' => ['secret' => '1b0fe895238226a6df3189e8ddffdf67', 'kaltura_user' => 'batchUser,template,hbtvadmin@ivideocloud.com,'],
                '天津卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'tjtvadmin'],
                '华媒传播' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'hmcbadmin'],
                '江苏卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'jstvadmin'],
                '杭州日报' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'hzdadmin'],
                '浙江卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'zjtvadmin'],
                '河南卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'henantvadmin'],
                '湖南卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'hntvadmin'],
                '山西卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'shanxitvadmin'],
                '安徽卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'anhuitvadmin'],
                '江西卫视' => ['secret' => 'c05fcd13a5541be3b1a8c9015e82213e', 'kaltura_user' => 'jxtvadmin'],
                'ICS' => ['secret' => 'd62158a4a4602053c85601c0b9d24ca8', 'kaltura_user' => 'icsadmin,batchUser,shanghaieye@ics.smg.cn,icsadmin@ivideocloud.com'],
            ];
            foreach ($orgs as $name => $org) {
                $organization = Organization::where('name', $name)->first();
                if (null === $organization) {
                    $organization = factory(Organization::class)->create(array_merge($allows, ['name' => $name]));
                    $cp = $organization->properties()->create(['name' => $name, 'type' => Property::TYPE_CP]);
                    $cp->metadatas()->create(['meta_name' => 'kaltura_user', 'meta_value' => $org['kaltura_user']]);
                    $sp = $organization->properties()->create(['name' => $name, 'type' => Property::TYPE_SP]);
                    $sp->metadatas()->create(['meta_name' => PropertyMeta::META_NAME_SECRET, 'meta_value' => $org['secret']]);
                }
            }
        }
    }
}
