<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PoHeader extends Model
{
    //
    protected $fillable = [
        'po_code', 'po_prepby', 'req_date', 'term', 'po_date', 'sup_name', 'sup_add', 'sup_contact', 'status', 'total'
    ];

    public function po_detail(){
        return $this->hasMany(PoDetail::class,'pod_code','po_code');
    }
    public function setReqDateAttribute($value)
    {
        $this->attributes['req_date'] = Carbon::createFromFormat('m/d/Y', $value)->format('Y-m-d');
    }
    public function setSupNameAttribute($value)
    {
        $this->attributes['sup_name'] = strtoupper($value);

    }
    public function setSupAddAttribute($value)
    {
        $this->attributes['sup_add'] = strtoupper($value);

    }
    public function setSupContactAttribute($value)
    {
        $this->attributes['sup_contact'] = strtoupper($value);

    }
    public function getPoDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('m/d/Y');

    }
    public function getTotalAttribute($value)
    {
        return number_format($value,2);

    }
    public function prepared()
    {
        return $this->belongsTo(User::class,'po_prepby','username');
    }
    public function approved()
    {
        return $this->belongsTo(User::class,'po_appby','username');
    }
}
