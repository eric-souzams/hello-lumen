<?php

namespace App\Models;

use App\Models\Transaction\Wallet;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, HasApiTokens;

    protected $table = 'users';
    
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name', 
        'email',
        'document_id'
    ];

    protected $hidden = [
        'password',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
