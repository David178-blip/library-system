<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
{
    $query = User::query();

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    if ($request->filled('course')) {
    $query->where('course', $request->course);
}


    $users = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.users.index', compact('users'));
}

    public function create()
    {
        return view('admin.users.create');
    }

public function store(Request $request)
{
    $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@holychild\.edu\.ph$/i',
        'password' => 'required|string|min:6|confirmed',
        'role'     => 'required|in:admin,student,faculty',
    ];

    // Only require course if the user is a student
    if ($request->role === 'student') {
        $rules['course'] = 'required|in:BSIT,BSBA,BSCRIM,BEED,BSED';
    }

    $validated = $request->validate($rules, [
         'email.regex' => 'Use the Holy Child account',
     ]);

    User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role'     => $validated['role'],
        'course'   => $validated['role'] === 'student' ? $validated['course'] : null,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
}


 public function edit(User $user)
{
    // Pass $user to the view
    return view('admin.users.edit', ['user' => $user]);
}

public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|regex:/^[a-zA-Z0-9._%+-]+@holychild\.edu\.ph$/i|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,student,faculty',
        'course' => 'nullable|string|in:BSIT,BSBA,BSCRIM,BEED,BSED',
        'password' => 'nullable|string|min:6|confirmed',
    ], [
         'email.regex' => 'Use the Holy Child account',
     ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];
    $user->course = $validated['role'] === 'student' ? $validated['course'] : null;

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
}


}
