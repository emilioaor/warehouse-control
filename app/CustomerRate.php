<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRate extends Model
{
    protected $fillable = ['customer_id', 'courier_id', 'way', 'rate'];

    /**
     * Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Courier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class)->withTrashed();
    }
}
