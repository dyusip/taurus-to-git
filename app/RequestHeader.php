<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestHeader extends Model
{
    //
    protected $fillable = [
        'rqh_no', 'rqh_date', 'rqh_branch', 'rqh_total', 'status'
    ];
    public function req_detail(){
        return $this->hasMany(RequestDetail::class,'rqd_code','rqh_no');
    }
}
