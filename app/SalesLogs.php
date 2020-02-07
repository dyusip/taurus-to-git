<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesLogs extends Model
{
    //
    protected $fillable = [
        'sol_code','sol_prod_code', 'sol_prod_name','sol_remarks','sol_status','sol_prod_qty','sol_prod_price','sol_prod_amount'
    ];
    public function so_logs(){
        return $this->belongsTo(SoHeader::class,'sol_code','so_code');
    }
}
