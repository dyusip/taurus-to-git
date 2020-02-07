<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PopaymentDetail extends Model
{
    //
    protected $fillable = [
        'pd_no', 'pd_paymentno', 'pd_date', 'pd_type', 'pd_amount','pd_checkno','pd_bank','pd_checkdate'
    ];
    public function pd_header(){
        return $this->belongsTo(PopaymentDetail::class,'pd_no','ph_no');
    }
    public function setPdBankAttribute($value)
    {
        $this->attributes['pd_bank'] = strtoupper($value);
    }
    public function setPdDateAttribute($value)
    {
        $this->attributes['pd_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function getPdDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');
    }
    public function setPdCheckdateAttribute($value)
    {
        $this->attributes['pd_checkdate'] = $value==''?date('Y-m-d',strtotime('0000-00-00')):Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function getPdCheckdateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');
    }
}
