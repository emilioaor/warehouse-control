<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'courier_id',
        'date',
        'created_by',
        'approved_by',
        'sign',
        'status'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    /**
     * Created by
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Approved by
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    /**
     * Courier
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'courier_id')->withTrashed();
    }

    /**
     * Details (Lines)
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
