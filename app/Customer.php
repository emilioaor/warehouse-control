<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;

    protected $table = 'customers';

    protected $fillable = [
        'description',
        'phone',
        'email',
        'default_courier_id'
    ];

    /**
     * Default courier
     */
    public function defaultCourier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'default_courier_id')->withTrashed();
    }
}
