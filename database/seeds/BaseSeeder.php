<?php

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    const ORG_ID_FOR_SUPER_ADMIN = 0;
    const PROPERTY_ID_FOR_ADMIN = 0;

    // user and Role-Level id is same
    const USER_ID_BY_ROLE = [
        'super_admin' => 1,
        'organization_admin' => 2,
        'property_manager' => 3,
        'dpp_admin' => 4,
    ];
}
