<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    //
    protected $fillable = [
        'pod_code', 'prod_code', 'prod_name', 'prod_uom', 'prod_qty', 'prod_price', 'prod_less', 'prod_amount',
        'prod_cost','prod_srp'
    ];
    public function po_header()
    {
        return $this->belongsTo(PoHeader::class,'pod_code','po_code');
    }
    public function rec_header()
    {
        return $this->belongsTo(ReceivingHeader::class,'pod_code','rh_po_no');
    }
    /*public function getProdPriceAttribute($value)
    {
        return number_format($value,2);

    }
    public function getProdAmountAttribute($value)
    {
        return number_format($value,2);

    }*/
}