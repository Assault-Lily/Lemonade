<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(){
        return view('admin.change-password')->with('user', Auth::user());
    }

    public function update(Request $request){
        $user = Auth::user();

        if($request->id != Auth::user()->id){
            abort(400);
        }

        if(!password_verify($request->current_password, $user->password)){
            return back()->withErrors([
                'current_password' => '現在のパスワードが一致しません'
            ]);
        }

        $request->validate([
            'new_password' => 'required|string|confirmed|min:8'
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect(route('admin.dashboard'))->with('message','パスワードを変更しました');
    }
}
