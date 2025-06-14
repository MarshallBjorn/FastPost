<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Warehouse;

class UserController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::query();

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }

        if ($request->filled('firstname')) {
            $query->where('first_name', 'like', '%' . $request->input('firstname') . '%');
        }

        if ($request->filled('lastname')) {
            $query->where('last_name', 'like', '%' . $request->input('lastname') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('staff_type')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('staff_type', $request->input('staff_type'));
            });
        }

        $users = $query->with('staff')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('admin.users.create', compact('warehouses'));
    }

    public function store(UserRequest $request)
    {
        $user = $request->validated();
        $user['password'] = bcrypt($user['password']);

        $user = User::create($user);

        $staffData = $request->input('staff');

        if (!empty($staffData) && $staffData['staff_type'] != null) {
            $staffData['user_id'] = $user->id;
            $user->staff()->create($staffData);
        }

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $warehouses = Warehouse::all();
        return view('admin.users.edit', compact('user', 'warehouses'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Handle staff create/update
        $staffData = $request->input('staff');

        if (!empty($staffData) && is_array($staffData)) {
            $staffData['user_id'] = $user->id;

            if ($user->staff) {
                $user->staff->update($staffData);
            } else {
                $user->staff()->create($staffData);
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
