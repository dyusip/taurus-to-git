<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceivingDetail extends Model
{
    //
    protected $fillable = [
        'rd_no', 'rd_prod_code', 'rd_prod_name', 'rd_prod_uom', 'rd_prod_qty','rd_status'
    ];
    public function re_header(){
        return $this->belongsTo(ReceivingHeader::class,'rd_code','rh_no');
    }
}