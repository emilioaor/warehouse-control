<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;
    use SearchTrait;
    use TimeZoneLocalTrait;

    protected $table = 'couriers';

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    protected $search_fields = ['name', 'address'];

    /**
     * Customers that configured this courier to default option
     */
    public function customersWithDefault(): HasMany
    {
        return $this->hasMany(Customer::class, 'default_courier_id');
    }

    /**
     * Orders by this courier
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_id');
    }

    /**
     * Packing lists
     */
    public function packingLists(): HasMany
    {
        return $this->hasMany(PackingList::class, 'courier_id');
    }
}
