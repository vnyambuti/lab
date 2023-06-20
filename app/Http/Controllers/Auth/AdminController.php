<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\ResetMailRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Models\Setting;
use App\Models\UserBranch;
use App\Traits\GeneralTrait;
use Hash;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Mail;
use Illuminate\Support\Str;


class AdminController extends Controller
{
    use GeneralTrait;
    /**
    * show login form
    *
    * @access public
    */
    public function login()
    {
        $info=$this->setting('info');
       

        return view('auth.admin.login',compact('info'));
    }

    /**
    * submit login form
    * @request $request
    * @access public
    */
    public function login_submit(LoginRequest $request)
    {
        //logout from patient
        auth()->guard('patient')->logout();
        
        $user=User::where('email',$request['email'])->first();
        //check if email exist
        if(isset($user)&&FacadesHash::check($request['password'],$user['password']))
        {
            $remember=($request->has('remeber'))?true:false;

            FacadesAuth::guard('admin')->login($user,$remember);

            $user_branch=UserBranch::where('user_id',auth()->guard('admin')->user()->id)
                                    ->first();

            session()->put('branch_id',$user_branch['branch_id']);
            session()->put('branch_name',$user_branch['branch']['name']);

            FacadesAuth::shouldUse('admin');

            return redirect('admin');
        }
        else{
            session()->flash('failed',__('Wrong email or password'));
            return redirect()->route('admin.auth.login');
        }
    }

    /**
    * logout admin
    * @request $request
    * @access public
    */
    public function logout(Request $request)
    {
        FacadesAuth::guard('admin')->logout();

        return redirect()->route('admin.auth.login');
    }


    /**
    * 
    * show ressetting mail form
    * @access public
    */
    public function mail()
    {
        $info=$this->setting('info');

        return view('auth.admin.mail',compact('info'));
    }

    /**
    * 
    * sending resetting mail
    * @access public
    */
    public function mail_submit(ResetMailRequest $request)
    {
        $user=User::where('email',$request->email)->first();
        // dd($user);

        if(isset($user))
        {
            
          //generate new user token
          $user->token=Str::random(32);
          $user->save();

          //send mail
        $code= $this->send_notification($request->email,new \App\Mail\ResetPasswordMail($user));
        // dd($code == 200);
         if ($code == 200) {
            session()->flash('success',__('Please check your email to complete resetting your password'));
         } else {
            session()->flash('failed',__($code));
         }
         
          

          return redirect()->route('admin.reset.mail');
        }
        else{
            session()->flash('failed',__('Email not found'));
            return redirect()->route('admin.reset.mail');
        }
    }

    /**
    * 
    * show resetting password form
    * @access public
    */
    public function reset_password_form($token)
    {
        $user=User::where('token',$token)->first();
        
        if(isset($user))
        {
            session()->put('token',$token);

            $info=$this->setting('info');

            // $info=json_decode($info,true);

            return view('auth.admin.reset_password',compact('info'));
        }
        else{
            return abort(403);
        }
    }

    /**
    * 
    * resetting password
    * @access public
    */
    public function reset_password_submit(ResetPasswordRequest $request)
    {
        $user=User::where('token',session('token'))->first();

        //update user password
        $user->password=bcrypt($request['password']);

        //regenerate token
        $user->token=Str::random(32);
        $user->save();

        session()->flash('success',__('Password reset successfully'));

        return redirect()->route('admin.auth.login');
    }
}
