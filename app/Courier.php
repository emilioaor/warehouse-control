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

    /**
     * Courier rates
     *
     * @return HasMany
     */
    public function courierRates()
    {
        return $this->hasMany(CourierRate::class);
    }

    /**
     * Customer rates
     *
     * @return HasMany
     */
    public function customerRates()
    {
        return $this->hasMany(CustomerRate::class);
    }

    /**
     * Update courier rates
     *
     * @param $courierRates
     */
    public function updateRates($courierRates)
    {
        foreach ($courierRates as $rate) {

            $courierRate = CourierRate::query()
                ->where('way', $rate['way'])
                ->where('courier_id', $this->id)
                ->where('sector_id', $rate['sector_id'])
                ->firstOrNew([
                    'way' => $rate['way'],
                    'courier_id' => $this->id,
                    'sector_id' => $rate['sector_id']
                ])
            ;

            $courierRate->rate = $rate['rate'];
            $courierRate->save();
        }
    }
}
