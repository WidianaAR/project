<?php

namespace App\Http\Controllers;

use App\Models\ForgetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function login()
    {
        return view('authentication.login');
    }

    public function login_action(Request $request)
    {
        $credential = $request->validate([
            'email' => 'email:dns',
            'password' => 'required',
        ]);

        if (Auth::attempt($credential)) {
            $user = Auth::user();
            if ($user->role_id == 1) {
                activity()->log('PJM login');
                return redirect()->intended('pjm');
            } elseif ($user->role_id == 2) {
                activity()->log('Kajur login');
                return redirect()->intended('kajur');
            } elseif ($user->role_id == 3) {
                activity()->log('Koorprodi login');
                return redirect()->intended('koorprodi');
            } elseif ($user->role_id == 4) {
                activity()->log('Auditor login');
                return redirect()->intended('auditor');
            }
            return redirect('login');
        }
        return back()->withErrors(['login_gagal' => 'Email atau password salah']);
    }

    public function logout(Request $request)
    {
        activity()->log('User logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    public function change_pass($id)
    {
        return view('authentication.change_password', ['user' => User::find($id)]);
    }

    public function change_pass_action(Request $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->old_pass, $user->password)) {
            return back()->withErrors(['old_pass' => 'Password lama salah!']);
        } elseif (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $request->validate([
            'password' => 'min:8|string',
            'conf_pass' => 'same:password'
        ], [
                'password.min' => 'Password minimal 8 karakter!',
                'conf_pass.same' => 'Password tidak sama, mohon periksa kembali input Anda.'
            ]);

        $data = User::find($user->id);
        $data->update(['password' => Hash::make($request->password)]);
        activity()
            ->performedOn($data)
            ->log('User mengubah password');
        return redirect()->route('ed_chart')->with('success', 'Password akun berhasil diubah.');
    }

    public function forget_pass()
    {
        return view('authentication.forget_password');
    }

    public function forget_pass_action(Request $request)
    {
        $request->validate([
            'email' => 'email|exists:users|unique:forget_passwords'
        ], [
                'email.exists' => 'Email tidak terdaftar dalam sistem!',
                'email.unique' => 'Tautan sudah dikirim, mohon periksa email Anda!'
            ]);

        $token = Str::random(64);

        ForgetPassword::create([
            'email' => $request->email,
            'token' => $token
        ]);

        Mail::send('authentication.email_forget_password', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset password akun Simjamu ITK');
        });

        activity()->log('User mengirim tautan reset password ke ' . $request->email);
        return back()->with('success', 'Tautan untuk reset password sudah dikirim ke email anda!');
    }

    public function reset_pass($token)
    {
        return view('authentication.reset_password', ['token' => $token]);
    }

    public function reset_pass_action(Request $request)
    {
        $request->validate([
            'email' => 'email:dns|exists:users',
            'password' => 'min:8|string',
            'password_conf' => 'same:password',
        ], [
                'email.exists' => 'Email tidak terdaftar dalam sistem',
                'password.min' => 'Password minimal 8 karakter!',
                'password_conf.same' => 'Password tidak sama, mohon periksa kembali password Anda.'
            ]);

        $updatePassword = ForgetPassword::where([
            'email' => $request->email,
            'token' => $request->token
        ])->first();

        if (!$updatePassword) {
            return back()->withErrors(['token_error' => 'Invalid token!']);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        ForgetPassword::where([
            'email' => $request->email,
            'token' => $request->token
        ])->delete();
        activity()->log('User merubah password akun ' . $request->email);

        return redirect('/login')->with('success', 'Password berhasil diubah');
    }
}