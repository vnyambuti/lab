<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use LogsActivity;

    public $guarded=[];

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Setting was {$eventName}";
    }

    public function info()
    {
       return json_decode(Setting::where('key','info')->first()->value);
    }
}
