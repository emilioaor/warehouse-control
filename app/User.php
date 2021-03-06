<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements IuuidGenerator
{
    use Notifiable;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;
    use TimeZoneLocalTrait;

    /** User roles */
    const ROLE_ADMIN = 'admin';
    const ROLE_WAREHOUSE = 'warehouse';
    const ROLE_SELLER = 'seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $search_fields = ['name', 'email'];

    /**
     * Created orders
     */
    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    /**
     * Approved orders
     */
    public function approvedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'approved_by');
    }

    /**
     * Customers assigned to seller user
     *
     * @return HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'seller_id');
    }

    /**
     * Is admin?
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Is warehouse?
     */
    public function isWarehouse(): bool
    {
        return $this->role === self::ROLE_WAREHOUSE;
    }

    /**
     * Is seller?
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Role in string
     */
    public function role(): string
    {
        return __(sprintf('role.%s', $this->role));
    }

    /**
     * Roles available
     */
    public static function rolesAvailable(): array
    {
        return [
            self::ROLE_ADMIN => __(sprintf('role.%s', self::ROLE_ADMIN)),
            self::ROLE_WAREHOUSE => __(sprintf('role.%s', self::ROLE_WAREHOUSE)),
            self::ROLE_SELLER => __(sprintf('role.%s', self::ROLE_SELLER)),
        ];
    }

    /**
     * Exclude me from select
     */
    public function scopeNotMe(Builder $query): Builder
    {
        return $query->where('id', '<>', Auth::user()->id)->where('email', '<>', 'emilioaor@gmail.com');
    }
}
