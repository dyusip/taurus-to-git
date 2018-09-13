<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SrHeader extends Model
{
    //
    protected $fillable =[
        'sr_code', 'so_code', 'sr_date', 'sr_prepby', 'sr_total','sr_status'
    ];
    public function getSrDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
    public function sr_detail()
    {
        return $this->hasMany(SrDetail::class,'srd_code','sr_code');
    }
    public function sr_so_header(){
        return $this->belongsTo(SoHeader::class,'so_code','so_code');
    }
    public function setSrDateAttribute($value)
    {
        $this->attributes['sr_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
}
