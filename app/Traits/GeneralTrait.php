<?php


namespace App\Traits;

use App\Models\Setting;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

/**
 *
 */
trait GeneralTrait{

    public function setting($key)
    {
       return json_decode(Setting::where('key',$key)->first()->value);
    }

    public function send_notification($sender,$template)
    {
     
        try {
            Mail::to($sender)->send($template);
            return 200;
        } catch (\Throwable $th) {
            // dd($th->getMessage());
           return $th->getMessage();
        }
 

    }

    public function formated_price($amount)
    {
      return number_format($amount, 2);
    }

    public function today()
    {
        return Date::today();
        
    }


    public function generate_barcode($id)
    {
       return   rand($id,50);
    }


}