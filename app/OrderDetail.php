<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'box_id',
        'description',
        'size',
        'weight',
        'qty'
    ];

    /**
     * Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id')->withTrashed();
    }

    /**
     * Default box selected
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id')->withTrashed();
    }

    /**
     * Volumetric weight
     *
     * @return float|int
     */
    public function volumetricWeight()
    {
        $weight = $this->weight;
        $split = explode('*', $this->size);
        $volumetricWeight = 0;
        $width = $split[0];
        $length = $split[1];
        $height = $split[2];

        if ($this->order->way === 'airway') {

            $volumetricWeight = ($width * $length * $height) / 166;
            $volumetricWeight = $volumetricWeight > $weight ? $volumetricWeight : $weight;

        } else if ($this->order->way === 'seaway') {
            $volumetricWeight = ($width * $length * $height) / 1728;
        }

        return ceil($volumetricWeight * 100) / 100;
    }

    /**
     * Get rate by lbs
     *
     * @param Customer $customer
     * @param Courier $courier
     * @param string $way
     * @return int|float
     */
    public static function getRate(Customer $customer, Courier $courier, string $way)
    {
        $customerRate = $customer->customerRates()->where('courier_id', $courier->id)->where('way', $way)->first();

        if ($customerRate) {
            return $customerRate->rate;
        }

        $courierRate = $courier->courierRates()->where('sector_id', $customer->sector_id)->where('way', $way)->first();

        if ($courierRate) {
            return $courierRate->rate;
        }

        return 0;
    }
}
