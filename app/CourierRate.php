<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourierRate extends Model
{
    /** Ways */
    const AIRWAY = 'airway';
    const SEAWAY = 'seaway';

    protected $fillable = ['courier_id', 'sector_id', 'way', 'rate'];

    /**
     * Courier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class)->withTrashed();
    }

    /**
     * Sector
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class)->withTrashed();
    }
}
