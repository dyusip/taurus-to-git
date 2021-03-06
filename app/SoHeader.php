<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SoHeader extends Model
{
    //
    protected $fillable = [
        'so_code', 'branch_code', 'jo_code', 'so_prepby', 'so_salesman', 'so_mechanic','cust_name','cust_add','cust_contact',
        'so_date', 'serv_charge', 'amount_rec', 'so_amount', 'so_status'
    ];
    public function setSoDateAttribute($value)
    {
        $this->attributes['so_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function getSoDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');
    }
    public function so_detail(){
        return $this->hasMany(SoDetail::class,'sod_code','so_code');
    }
    public function so_logs(){
        return $this->hasMany(SalesLogs::class,'sol_code','so_code');
    }
    public function so_sr_header(){
        return $this->hasMany(SrHeader::class,'so_code','so_code');
    }
    public function setCustNameAttribute($value){
        $this->attributes['cust_name'] =  strtoupper($value);
    }
    public function salesman()
    {
        return $this->belongsTo(User::class,'so_salesman','username');
    }
    public function mechanic()
    {
        return $this->belongsTo(User::class,'so_mechanic','username');
    }
    public function so_branch()
    {
        return $this->belongsTo(Branch::class,'branch_code','code');
    }
}
