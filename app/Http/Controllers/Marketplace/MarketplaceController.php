<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        return view('marketplace.index');
    }
}
