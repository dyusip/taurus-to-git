<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $fillable = [
        'code','name', 'address', 'email','contact','status'
    ];
    public function setNameAttribute($value){
        $this->attributes['name'] = strtoupper($value);
    }
    public function po_supplier()
    {
        return $this->hasMany(PoHeader::class,'sup_code','code');
    }
}
