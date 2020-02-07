<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReceivingHeader extends Model
{
    //
    protected $fillable = [
        'rh_no', 'rh_po_no', 'rh_si_no', 'rh_branch_code', 'rh_date', 'rh_prepby','rh_status'
    ];
    public function pop_header(){
        return $this->hasOne(PopaymentHeader::class,'ph_rh_no','rh_no');
    }
    public function re_detail(){
        return $this->hasMany(ReceivingDetail::class,'rd_code','rh_no');
    }
    public function pod_detail(){
        return $this->hasMany(PoDetail::class,'pod_code','rh_po_no');
    }
    public function re_po_header(){
        return $this->belongsTo(PoHeader::class,'rh_po_no','po_code');
    }
    public function setRhDateAttribute($value)
    {
        $this->attributes['rh_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
}
