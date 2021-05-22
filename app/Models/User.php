<?php

namespace App\Models;

use App\Models\Records;
use App\Models\CIFUploads;
use App\Models\LinelistMaster;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'isAdmin',
        'brgy_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function records() {
        return $this->hasMany(Records::class);
    }

    public function form() {
        return $this->hasMany(Forms::class);
    }

    public function brgy() {
        return $this->hasMany(Brgy::class);
    }

    public function brgyCode() {
        return $this->hasMany(BrgyCodes::class);
    }

    public function interviewer() {
        return $this->hasMany(Interviewers::class);
    }

    public function linelistmaster() {
        return $this->hasMany(LinelistMasters::class);
    }

    public function cifupload() {
        return $this->hasMany(CifUploads::class);
    }
}
