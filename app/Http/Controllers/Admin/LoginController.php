<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show_login_view()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['username', 'password']);

        if (! auth()->guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('username'))->with('error', __('auth.failed'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        auth()->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.showlogin');
    }

    public function editlogin($id)
    {
        abort_unless((int) $id === auth()->guard('admin')->id(), 403);

        $data = Admin::findOrFail($id);

        return view('admin.auth.edit', compact('data'));
    }

    public function updatelogin(Request $request, $id)
    {
        abort_unless((int) $id === auth()->guard('admin')->id(), 403);

        $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:admins,username,'.$id],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $admin = Admin::findOrFail($id);
        $admin->username = $request->input('username');
        $admin->password = Hash::make($request->input('password'));
        $admin->save();

        return $this->logout($request);
    }
}
