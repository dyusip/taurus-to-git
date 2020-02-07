<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReqDetail extends Model
{
    //
    protected $fillable = [
        'rqd_code', 'rqd_prod_code', 'rqd_prod_name', 'rqd_prod_uom', 'rqd_prod_qty', 'rqd_prod_price',
        'rqd_prod_amount', 'rqd_status','rqd_prod_cost','rqd_prod_srp'
    ];
    public function req_header()
    {
        return $this->belongsTo(ReqHeader::class,'rqd_code','rqh_code');
    }
}
