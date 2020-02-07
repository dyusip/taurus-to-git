<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','name', 'email', 'password','position','status','gender', 'contact','branch'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /*public function getNameAttribute($value)
    {
        return "User: ".$value;
    }*/
    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }
    public function setNameAttribute($value){
        $this->attributes['name'] = strtoupper($value);
    }
    public function branch_user()
    {
        return $this->belongsTo(Branch::class,'branch','code');
    }
    public function prepared()
    {
        return $this->hasMany(PoHeader::class,'po_prepby','username');
    }
    public function approved()
    {
        return $this->hasMany(PoHeader::class,'po_appby','username');
    }
    public function salesman()
    {
        return $this->hasMany(SoHeader::class,'so_salesman','username');
    }
    public function mechanic()
    {
        return $this->hasMany(SoHeader::class,'so_mechanic','username');
    }
    public function tf_prep_by()
    {
        return $this->hasMany(TransferHeaders::class,'tf_prepby','username');
    }
    public function tf_app_by()
    {
        return $this->hasMany(TransferHeaders::class,'tf_appby','username');
    }
    public function rqh_req_by_header()
    {
        return $this->hasMany(ReqHeader::class,'req_reqby','username');
    }
    public function rqh_check_by_header()
    {
        return $this->hasMany(ReqHeader::class,'req_checkby','username');
    }
    public function pr_reqby_header()
    {
        return $this->hasMany(PrHeader::class,'prh_reqby','username');
    }
    public function pr_appby_header()
    {
        return $this->hasMany(PrHeader::class,'prh_appby','username');
    }
}
