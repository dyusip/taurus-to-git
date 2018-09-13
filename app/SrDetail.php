<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SrDetail extends Model
{
    //
    protected $fillable = [
        'srd_code', 'srd_prod_code', 'srd_prod_name', 'srd_prod_uom', 'srd_prod_qty', 'srd_prod_price',
        'srd_less', 'srd_prod_amount', 'status'
    ];
    public function sr_header()
    {
        return $this->belongsTo(SrHeader::class, 'srd_code','sr_code');
    }
}
