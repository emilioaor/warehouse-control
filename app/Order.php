<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;
    use SearchTrait;

    /** Status */
    const STATUS_PENDING_SEND = 'pending_send';
    const STATUS_SENT = 'sent';

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

    protected $search_fields = ['date'];

    public function __construct(array $attributes = [])
    {
        $this->generateUuid();
        $this->status = self::STATUS_PENDING_SEND;
        $this->date = Carbon::now();
        $this->created_by = Auth::user()->id;
        parent::__construct($attributes);
    }

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
     * Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
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

    /**
     * Status string
     */
    public function status()
    {
        return __(sprintf('status.%s', $this->status));
    }
}
