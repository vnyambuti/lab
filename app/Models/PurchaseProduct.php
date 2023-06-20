<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    public $guarded=[];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class,'purchase_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id')->withTrashed();
    }
}
