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
}
