<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->intended();
        }

        return view('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = false;
        if (isset($request['remember'])) {
            $remember = true;
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function user()
    {
        if (Auth::user()->role != 'admin') {
            return back();
        }

        if (request()->ajax()) {
            $user = User::query();
            return DataTables::of($user)->make();
        }

        return view('user');
    }

    public function formUser($id)
    {
        $data = ['user' => null];

        if ($id) {
            $data['user'] = User::find($id);
        }

        return view('form-user', $data);
    }

    public function saveUser(Request $request): RedirectResponse
    {
        $data = $request->all();
        $user = new User;
        $validation = [
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ];

        if ($data['id']) {
            $user = User::find($data['id']);
        } else {
            $validation['email'] = 'required|unique:users,email';
            $validation['password'] = 'required|min:6';
        }

        $request->validate($validation);

        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        if ($data['password']) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect('/user')->with('status', 'User ' . $data['name'] . ($data['id'] ? ' updated.' : ' created.'));
    }

    public function deleteUser($id)
    {
        User::find($id)->delete();
        return redirect('/user')->with('status', 'User deleted.');
    }
}
