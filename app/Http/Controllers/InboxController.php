<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $inquiries = $request->user()
            ->inquiries()
            ->with('business')
            ->latest()
            ->paginate(15);

        return view('inbox.index', [
            'inquiries' => $inquiries,
        ]);
    }
}
