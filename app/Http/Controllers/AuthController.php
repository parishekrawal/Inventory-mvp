<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class AuthController extends Controller
{   
    public function login(AuthRequest $request){

        $user=User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json(['message'=>'invalid credentials'],401);
        }

        $token=$user->createToken('admin-token')->plainTextToken;

        Auth::login($user);
        
        logActivity('LOGIN','Admin logged in');
        return response()->json(['token'=>$token]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        logActivity('LOGOUT','Admin logged out');
        return response()->json(['message'=>'Logged out']);
    }

    public function changePassword(AuthRequest $request){
        $admin=$request->user();

        if(!Hash::check($request->current_password,$admin->password)){
            return response()->json(['message'=> 'Current password is incorrect'],422);
        }

        $admin->password=Hash::make($request->new_password);
        $admin->save();

        return response()->json(['messsage'=>'Password changed successfully'],200);
    }

    public function forgotPassword(AuthRequest $request){
        try{
            $status=Password::sendResetLink($request->only('email'));

            if($status===Password::RESET_LINK_SENT){
                return response()->json(['message'=>'Reset link sent to email'],200);
            }

            if($status===Password::INVALID_USER){
                return response()->json(['message'=>'Email not found'],422);
            }
                
            return response()->json(['message'=>'Unable to send reset link'],500);
        }catch(Exception $err){
            return response()->json([
                'message'=>'Something went wrong',
                'error'=>$err->getMessage()
            ],500);
        }
    }

    public function resetPassword(AuthRequest $request){
        $status=Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),function($user,$password){
                $user->password=Hash::make($password);
                $user->save();
            }
        );

        if($status===Password::PASSWORD_RESET){
            return response()->json(['message'=>'Password reset successfully']);
        }

        return response()->json(['message'=>'Invalid token'],422);
    }
}
