<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::guard('admin')->user();

        return view('admin.profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::guard('admin')->user();

        $payload = [
            'email' => $request->validated('email'),
        ];

        $password = (string) ($request->validated('password') ?? '');
        if ($password !== '') {
            $payload['password'] = Hash::make($password);
        }

        $user->update($payload);

        return back()->with('success', 'Profile updated successfully.');
    }
}

