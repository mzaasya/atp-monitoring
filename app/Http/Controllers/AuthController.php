<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class AuthController extends Controller
{
    protected $oneSignalUrl;

    public function __construct()
    {
        $this->oneSignalUrl = env('ONESIGNAL_URL') . '/apps/' . env('ONESIGNAL_APP_ID');
    }

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
            $this->oneSignalCheckUser(Auth::user());
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
        $id = $data['id'];
        unset($data['id']);

        $validation = [
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
        ];

        if (!$id) {
            $validation['email'] = 'required|unique:users,email';
            $validation['password'] = 'required|min:6';
        }

        $request->validate($validation);

        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($id) {
            $user = User::find($id);
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $data['role'];
            if ($data['password']) {
                $user->password = $data['password'];
            }
            $this->oneSignalCheckUser($user);
            $user->save();
        } else {
            $user = User::create($data);
            $this->oneSignalCheckUser($user);
        }

        return redirect('/user')
            ->with('message', 'User ' . $data['name'] . ($id ? ' updated.' : ' created.'))
            ->with('status', 'success');
    }

    public function deleteUser($id)
    {
        $task = Task::where('user_id', '=', $id)->first();
        if ($task) {
            return back()->with('message', 'cannot be deleted, this user has ATP.')->with('status', 'error');
        }
        User::find($id)->delete();
        $this->oneSignalDeleteUser($id);
        return redirect('/user')->with('message', 'User deleted.')->with('status', 'success');
    }

    private function oneSignalViewUser($id)
    {
        $path = '/users/by/external_id/user-' . $id;
        return Http::get($this->oneSignalUrl . $path);
    }

    private function oneSignalCreateUser(User $user)
    {
        $path = '/users';
        return Http::post($this->oneSignalUrl . $path, [
            'identity' => [
                'external_id' => 'user-' . $user->id
            ],
            'subscriptions' => [
                [
                    'type' => 'Email',
                    'token' => $user->email
                ]
            ]
        ]);
    }

    private function oneSignalDeleteUser($id)
    {
        $viewUser = $this->oneSignalViewUser($id);
        if ($viewUser->status() === 200) {
            $path = '/users/by/external_id/user-' . $id;
            return Http::delete($this->oneSignalUrl . $path);
        }
        return false;
    }

    private function oneSignalUpdateSubscription($id, $data)
    {
        $path = '/subscriptions/' . $id;
        return Http::patch($this->oneSignalUrl . $path, [
            'subscription' => $data
        ]);
    }

    private function oneSignalCheckUser(User $user)
    {
        $viewUser = $this->oneSignalViewUser($user->id);
        if ($viewUser->status() === 200) {
            $subscriptions = $viewUser->json()['subscriptions'];
            $i = array_search('Email', array_column($subscriptions, 'type'));
            if ($i !== false) {
                $sub = $subscriptions[$i];
                if ($sub['token'] !== $user->email) {
                    $dataUpdate = ['token' => $user->email];
                    $this->oneSignalUpdateSubscription($sub['id'], $dataUpdate);
                }
            }
        } else if ($viewUser->status() === 404) {
            $this->oneSignalCreateUser($user);
        }
        return true;
    }
}
