<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Box extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;
    use SearchTrait;
    use TimeZoneLocalTrait;

    protected $table = 'boxes';

    protected $fillable = [
        'description',
        'size',
        'weight'
    ];

    protected $search_fields = ['uuid', 'description', 'size', 'weight'];

    /**
     * Order details that assigned this default box. The
     * user can choose default box from boxes available or
     * set custom dimensions
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'box_id');
    }
}
