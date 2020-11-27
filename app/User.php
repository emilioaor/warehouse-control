<?php

namespace App;

use App\Contract\IuuidGenerator;
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
    Use UuidGeneratorTrait;

    /** User roles */
    const ROLE_ADMIN = 'admin';
    const ROLE_WAREHOUSE = 'warehouse';

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
     * Role in string
     */
    public function role(): string
    {
        return __(sprintf('role.%s', $this->role));
    }

    /**
     * Exclude me from select
     */
    public function scopeNotMe(Builder $query): Builder
    {
        return $query->where('id', '<>', Auth::user()->id);
    }

    /**
     * Search by any field
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $fields = ['name', 'email'];

        if (! empty($search)) {
            $query->where(function ($q) use ($fields, $search) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
