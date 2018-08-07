<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferDetails extends Model
{
    //
    protected $fillable = [
        'td_no', 'tf_prod_code', 'tf_prod_name', 'tf_prod_uom', 'tf_prod_qty','tf_prod_price', 'tf_prod_amount'
    ];
    public function tf_header(){
        return $this->belongsTo(TransferHeaders::class,'td_code','tf_code');
    }
}
