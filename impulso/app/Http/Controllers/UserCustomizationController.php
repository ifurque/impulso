<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserCustomizationController extends Controller
{
    public const DEFAULTS = [
        'font_family' => 'dm',
        'theme_mode' => 'day',
        'screen_background' => 'paper',
        'navbar_color' => '#eef1ea',
        'text_color' => '#182522',
    ];

    public function edit(Request $request)
    {
        $preferences = array_merge(self::DEFAULTS, $request->user()->ui_preferences ?? []);

        return view('settings.customization', ['preferences' => $preferences]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'font_family' => ['required', 'in:dm,space,serif,mono'],
            'theme_mode' => ['required', 'in:day,night'],
            'screen_background' => ['required', 'in:paper,soft,grid,dots,gradient'],
            'navbar_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'text_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $request->user()->update(['ui_preferences' => $data]);

        return redirect()->route('user.customization')->with('success', 'Personalización guardada.');
    }
}
