<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    //
    protected $fillable = [
        'rqd_code', 'rqd_prod_code', 'rqd_prod_name', 'rqd_prod_uom ', 'rqd_prod_qty','rqd_prod_price','rqd_prod_amount'
    ];
    public function req_header(){
        return $this->belongsTo(RequestHeader::class,'rqd_code','rqh_no');
    }
}
