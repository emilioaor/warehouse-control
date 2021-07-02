<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sector extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name'];

    /**
     * Customers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Courier rates
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courierRates()
    {
        return $this->hasMany(CourierRate::class);
    }
}
