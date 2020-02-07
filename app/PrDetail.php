<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrDetail extends Model
{
    //
    protected $fillable = [
        'prd_code', 'prd_prod_code','prd_prod_name','prd_prod_uom','prd_prod_qty','prd_prod_price',
        'prd_prod_amount','prd_status'
    ];
    public function pr_header()
    {
        return $this->belongsTo(PrHeader::class,'prd_code','prh_no');
    }
}
