<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PrHeader extends Model
{
    //
    protected $fillable = [
        'prh_no', 'prh_reqby', 'prh_appby','pr_date','pr_total','pr_status'
    ];
    public function pr_detail()
    {
        return $this->hasMany(PrDetail::class,'prd_code','prh_no');
    }
    public function pr_reqby()
    {
        return $this->belongsTo(User::class,'prh_reqby','username');
    }
    public function pr_appby()
    {
        return $this->belongsTo(User::class,'prh_appby','username');
    }
    public function getPrDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
}
