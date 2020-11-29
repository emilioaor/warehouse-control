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
}
