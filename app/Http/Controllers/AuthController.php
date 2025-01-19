<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;

class AuthController extends Controller
{
    public function login(){
        return view('login');
    }

    public function loginSubmit(Request $request){
        // form validation
        $request->validate(
            //rules
            [
                'text_username' => 'required|email',
                'text_password' => 'required|min:6|max:16'
            ],
            //error messages
            [
                'text_username.required' => 'O username é obrigatório',
                'text_username.email' => 'O username deve ser um email válido',

                'text_password.required' => 'A senha é obrigatória',
                'text_password.min' => 'A senha deve ter no mínimo :min caracteres',
                'text_password.max' => 'A senha deve ter no máximo :max caracteres',
            ]
        );

        //get user input

        $username = $request->input('text_username');
        $password = $request->input('text_password');

        //check if users exists

        $user = User::where('username', $username)
            ->where('deleted_at', NULL)
            ->first();

        if(!$user){
            return redirect()->back()->withInput()->with('login_error', 'Username ou password incorretos');
        }

        //check if password is correct

        if(!password_verify($password, $user->password)){
            return redirect()->back()->withInput()->with('login_error', 'Username ou password incorretos');
        }

        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);

        return redirect('/');

    }

    public function logout(){
        //logout

        session()->forget(('user'));
        return redirect('/login');
    }
}
