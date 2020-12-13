<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerEmail extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;

    protected $table = 'customer_emails';

    protected $fillable = ['email', 'customer_id'];

    /**
     * Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }
}
