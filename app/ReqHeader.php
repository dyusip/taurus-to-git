<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReqHeader extends Model
{
    //
    protected $fillable = [
        'rqh_code','req_from','req_to','req_date','req_reqby','req_checkby','req_amount','req_status'
    ];
    public function req_detail()
    {
        return $this->hasMany(ReqDetail::class,'rqd_code','rqh_code');
    }
    public function setReqDateAttribute($value)
    {
        $this->attributes['req_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function getReqDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');
    }
    public function req_from_branch()
    {
        return $this->belongsTo(Branch::class,'req_from','code');
    }
    public function req_to_branch()
    {
        return $this->belongsTo(Branch::class,'req_to','code');
    }
    public function rqh_req_by()
    {
        return $this->belongsTo(User::class,'req_reqby','username');
    }
    public function rqh_check_by()
    {
        return $this->belongsTo(User::class,'req_checkby','username');
    }
}
