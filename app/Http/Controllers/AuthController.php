<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function __construct()
    {
        // Added to ignore BaseController`s constructor
    }

    public function login(LoginRequest $request)
    {
      $validated = $request->validated();
      if (Auth::attempt($validated)) {

        $request->session()->regenerate();
        $user_name = ucwords(
          Str::of(Auth::user()->name)
            ->lower()
        );
        $user_name =  str_replace(' ', '', $user_name);
            // dd(Auth::id());
            $userDetail =  UserDetail::where("user_id",Auth::id())->first();
            $images = [];
            if ($userDetail !== null) {
              if (!empty($userDetail->file_path)) {
                $files = Storage::disk('public')->files('uploads/users/FMB_' . Auth::id() . '_' . $user_name . '/user-images');
                if (count($files) > 0) {
                  $images = "";
                  foreach ($files as $key => $file) {
                    $imageName = basename($file);
                    $images = $imageName;
                  }
                }
              }
              session()->put('user_image', $images);
              session()->put('has_user', true);
            }else {
              session()->put('has_user', false);
            }
            
            return redirect('dashboard');
        }
        return redirect()->back()->withError('Invalid Login!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
