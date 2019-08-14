<?php

namespace App\Http\Controllers\Admin;

use App\Models\Entry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $entries = Entry::with('content_provider_property.organization')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(10);

        return view('admin.pages.entry.index', compact('entries', 'search'));
    }
}
