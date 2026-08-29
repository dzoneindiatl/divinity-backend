<?php

namespace App\Http\Controllers\Admin\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\UserLoginHistory;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            return view('admin.auth.login');
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Somethig went wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function verifyLogin(AuthRequest $request)
    {   
        try {
            $credentials = $request->only('email', 'password');
            $user = Auth::attempt($credentials);

            // Auth::logoutOtherDevices($request->password);
			if ($user) {
                $userSession = Auth::user();
                
                // If user already has session, destroy it
                $userDetails = User::where('id', $userSession->getAttribute('id'))->first(); 
                if (!empty($userDetails->session_id)) {
                    Session::getHandler()->destroy($userDetails->session_id);
                }
                
                //  save session id 
                $user = User::find($userSession->getAttribute('id'));
                $user->session_id    = Session::getId();
                $user->last_login_ip    = $request->ip();
                $user->save();
                
                //  create login history
                // echo "<pre>"; print_r([
                //     'user_id' => $userDetails->id,
                //     'ip' => $request->ip(),
                //     'login_time' => date('d-m-Y H:i:s')
                // ]); echo "</pre>"; die;
                // 
                
                UserLoginHistory::create([
                    'user_id' => $userDetails->id,
                    'ip' => $request->ip(),
                    'login_time' => date('d-m-Y H:i:s')
                ]);
                
                
				$admin_modules	=	$this->buildTree(0);
                Session()->put('acls',$admin_modules);
                // return redirect()->back()->with('success', 'you are logged in as admin');
				return redirect()->route('admin-admin.dashboard')->with('success', 'you are logged in as admin');
            } else {
                return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Somethig went wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();
            session()->flush();
            cache()->flush();
            auth()->logout();

            return redirect()->route('admin-login')->with('success', "You're logged out successfully");
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Somethig went wrong', 'error_msg' => $e->getMessage()]);
        }
    }
}
