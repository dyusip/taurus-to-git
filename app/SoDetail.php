<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoDetail extends Model
{
    //
    protected $fillable = [
        'sod_code', 'sod_prod_code', 'sod_prod_name','sod_prod_uom', 'sod_prod_qty', 'sod_prod_price', 'sod_less', 'sod_prod_amount',
        'sod_prod_cost','sod_prod_srp'
    ];
    public function so_header(){
        return $this->belongsTo(SoHeader::class, 'sod_code','so_code');
    }
}
