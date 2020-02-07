<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PopaymentHeader extends Model
{
    //
    protected $fillable = [
        'ph_no', 'ph_rh_no', 'ph_rembal', 'ph_amount', 'ph_status'
    ];
    public function ph_detail(){
        return $this->hasMany(PopaymentDetail::class,'pd_no','ph_no');
    }
    public function rec_pop_header(){
        return $this->belongsTo(ReceivingHeader::class,'ph_rh_no','rh_no');
    }
}
