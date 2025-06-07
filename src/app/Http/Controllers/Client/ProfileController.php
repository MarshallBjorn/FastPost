<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('public.client.profile.edit', compact('user'));
    }

    public function update(UserRequest $request)
    {
        $request->merge(['_profile_update' => true]);

        $user = Auth::user();
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully!');
    }
}
