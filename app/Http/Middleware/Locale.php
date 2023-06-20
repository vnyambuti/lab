<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Support\Facades\Schema;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Schema::hasTable('migrations'))
        {
            
            $languages=\App\Models\Language::select('iso')->where('active',true)->get();
            $info_settings=json_decode(Setting::where('key','info')->first()->value);
        
            view()->share([
                'languages'=>$languages
            ]);

            if(session('locale'))
            {
                app()->setLocale(session('locale'));
            }
            else{
                app()->setLocale($info_settings->language);
            }

            if(isset($info_settings->timezone))
            {
                date_default_timezone_set($info_settings->timezone);
            }
            else{
                date_default_timezone_set('UTC');
            }

            //add transfile content to session if not exist
            if(!session()->has('trans'))
            {
                $trans=file_get_contents(resource_path('lang/'.app()->getLocale().'.json'));
                $trans=json_decode($trans);
                $trans=json_encode($trans);
        
                session()->put('trans',$trans);
            }

        }

        return $next($request);
    }
}
