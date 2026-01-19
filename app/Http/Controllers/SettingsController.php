<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the user profile settings page.
     */
    public function editProfile(Request $request): View
    {
        if ($request->user()->isAdmin()) {
            return view('settings.profile-admin');
        }

        return view('settings.profile-member');
    }

    /**
     * Show the member security settings page.
     */
    public function security(Request $request): View
    {
        if ($request->user()->isAdmin()) {
            return view('settings.profile-admin'); // Or redirect to admin specific settings
        }

        return view('settings.security-member');
    }
}
