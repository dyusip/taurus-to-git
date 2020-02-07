<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MiscDetail extends Model
{
    //
    protected $fillable = [
        'msd_code', 'msd_prod_code', 'msd_prod_name', 'msd_prod_uom', 'msd_prod_cost', 'msd_prod_qty', 'msd_prod_price',
        'msd_prod_amount', 'msd_remarks','msd_upd_qty'
    ];
    public function misc_header()
    {
        return $this->belongsTo(MiscHeader::class,'msd_code','msh_code');
    }
}
