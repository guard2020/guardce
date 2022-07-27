<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Validator;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login()
    {
        //check if database is filled.
        if(Schema::hasTable('users')){
            $users = User::all();
            if(count($users) > 0){
                //exists
            }else{
                Artisan::call('migrate:fresh --force');
                Artisan::call('db:seed --force');
            }
        }else{
            Artisan::call('migrate:fresh --force');
            Artisan::call('db:seed --force');
        }

        return view('auth.login');
    }

    public function checkLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        $userData = array(
            'email' => $request->get('email'),
            'password' => $request->get('password')
        );

        $user = User::query()
            ->where('email', $request->get('email'))
            ->first();

        if(!empty($user)) {
            if($user->status === 1){
                if (Auth::attempt($userData)) {
                    return redirect()->route('dashboard');
                } else {
                    return back()->with('error', 'Password is incorrect');
                }
            } else {
                return back()->with('error', 'User account is not yet approved');
            }
        } else {
            return back()->with('error', 'User not found with this email address');
        }


    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
