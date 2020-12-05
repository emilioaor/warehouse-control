<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;
    use SearchTrait;

    protected $table = 'customers';

    protected $fillable = [
        'description',
        'phone',
        'email',
        'default_courier_id',
        'address'
    ];

    protected $with = ['defaultCourier'];

    protected $search_fields = ['uuid', 'description', 'phone', 'email'];

    /**
     * Default courier
     */
    public function defaultCourier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'default_courier_id')->withTrashed();
    }

    /**
     * Assigned orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
