<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransferHeaders extends Model
{
    //
    protected $fillable = [
        'tf_code', 'from_branch', 'to_branch', 'tf_date', 'tf_prepby', 'tf_appby','tf_amount', 'tf_status','rqh_code'
    ];
    public function getTfDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
    public function tf_detail(){
        return $this->hasMany(TransferDetails::class,'td_code','tf_code');
    }
    public function setTfDateAttribute($value)
    {
        $this->attributes['tf_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function tf_to_branch(){
        return $this->belongsTo(Branch::class,'to_branch','code');
    }
    public function tf_fr_branch(){
        return $this->belongsTo(Branch::class,'from_branch','code');
    }
    public function tf_prep_by()
    {
        return $this->belongsTo(User::class,'tf_prepby','username');
    }
    public function tf_app_by()
    {
        return $this->belongsTo(User::class,'tf_appby','username');

    }
}