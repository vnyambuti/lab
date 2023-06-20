<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupCulture extends Model
{
    public $guarded=[];

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id','id');
    }

    public function culture()
    {
        return $this->belongsTo(Culture::class,'culture_id','id')->withTrashed();
    }

    public function antibiotics()
    {
        return $this->hasMany(GroupCultureResult::class,'group_culture_id','id')->orderBy('id','asc');
    }

    public function high_antibiotics()
    {
        return $this->hasMany(GroupCultureResult::class,'group_culture_id','id')->where('sensitivity',__('High'));
    }

    public function moderate_antibiotics()
    {
        return $this->hasMany(GroupCultureResult::class,'group_culture_id','id')->where('sensitivity',__('Moderate'));
    }

    public function resident_antibiotics()
    {
        return $this->hasMany(GroupCultureResult::class,'group_culture_id','id')->where('sensitivity',__('Resident'));
    }

    public function culture_options()
    {
        return $this->hasMany(GroupCultureOption::class,'group_culture_id','id');
    }
}
