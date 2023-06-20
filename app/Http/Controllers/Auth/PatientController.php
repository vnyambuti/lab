<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Setting as set;
use App\Mail\PatientCode;
use Auth;
use Mail;
use App\Http\Requests\Patient\PatientRegisterRequest;
use App\Http\Requests\Patient\PatientLoginRequest;
use App\Http\Requests\Patient\ForgetCodeRequest;
use App\Models\Setting;
use Str;
class PatientController extends Controller
{
    /**
    * show patient registration form
    *
    * @access public
    */
    public function showRegistrationForm()
    {
        $info=json_decode(Setting::where('key','info')->first()->value);
        // dd($info);

        return view('auth.patient.register',compact('info'));
    }

    /**
    * register patient
    * @param Request $request
    * @access public
    */
    public function register_submit(PatientRegisterRequest $request)
    {
        //logout from admin
        auth()->guard('admin')->logout();
        
        $patient=Patient::create([
            'name'=>$request['name'],
            'national_id'=>$request['national_id'],
            'passport_no'=>$request['passport_no'],
            'country_id'=>$request['country_id'],
            'phone'=>$request['phone'],
            'email'=>$request['email'],
            'gender'=>$request['gender'],
            'dob'=>$request['dob'],
            'address'=>$request['address'],
        ]);

        patient_code($patient['id']);

        send_notification('patient_code',$patient);

        session()->flash('success',__('Patient registered successfully'));
        
        Auth::guard('patient')->login($patient);

        return redirect()->route('patient.index');
    }

    /**
    * show patient login form
    *
    * @access public
    */
    public function showLoginForm()
    {
        $info= json_decode(Setting::where('key','info')->first()->value);
        // dd($info->name);

        return view('auth.patient.login',compact('info'));
    }

    /**
    * login patient
    * @param Request $request
    * @access public
    */
    public function login_submit(PatientLoginRequest $request)
    {
        $patient=Patient::where('code',$request['code'])->first();

        if(isset($patient))
        {
            //logout from admin
            auth()->guard('admin')->logout();

            $remember=($request->has('remeber'))?true:false;

            Auth::guard('patient')->login($patient,$remember);

            session()->flash('success',__('Login success'));
            
            return redirect()->route('patient.index');
        }
        else{

            session()->flash('failed',__('Wrong patient code'));
            return redirect()->back();
        }

    }

    /**
    * send patient code form
    *
    * @access public
    */
    public function showMailForm()
    {
        $info=setting('info');

        return view('auth.patient.mail',compact('info'));
    }


    /**
    * send patient code mail
    * @param Request $request
    * @access public
    */
    public function mail_submit(ForgetCodeRequest $request)
    {

       $patient=Patient::where('email',$request['email'])
                        ->orWhere('phone',$request['email'])
                        ->first();

       if(isset($patient))
       {
           //send mail
           send_notification('patient_code',$patient);

           session()->flash('success',__('we sent you the patient code,Please check your mail or phone for the patient code message'));
           return redirect()->route('patient.auth.login');
       }
       else{
        session()->flash('failed',__('Wrong patient email or phone'));
        return redirect()->back();
       }
    }

    /**
    * logout patient
    * @request $request
    * @access public
    */
    public function logout(Request $request)
    {
        Auth::guard('patient')->logout();

        return redirect()->route('patient.auth.login');
    }

    /**
    * QRCode patient login
    * $code
    * @access public
    */
    public function login_patient($code)
    {
        //logout from admin
        auth()->guard('admin')->logout();

        $patient=Patient::where('code',$code)->first();
        
        if(isset($patient))
        {
            session()->flash('success',__('Login success'));
            Auth::guard('patient')->login($patient);
            
            return redirect()->route('patient.groups.index');
        }
        else{
            session()->flash('failed',__('Wrong patient code'));
            return redirect()->back();
        }
    }

}
