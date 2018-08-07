<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SoHeader extends Model
{
    //
    protected $fillable = [
        'so_code', 'branch_code', 'jo_code', 'so_prepby', 'so_salesman','so_mechanic','cust_name','cust_add','cust_contact',
        'so_date', 'serv_charge', 'amount_rec', 'so_amount', 'so_status'
    ];
    public function getSoDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
    public function so_detail(){
        return $this->hasMany(SoDetail::class,'sod_code','so_code');
    }
    public function setCustNameAttribute($value){
        $this->attributes['cust_name'] =  strtoupper($value);
    }
}
