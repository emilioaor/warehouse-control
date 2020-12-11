<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
    use TimeZoneLocalTrait;

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
        'status',
        'invoice_number',
        'packing_list_id'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    protected $search_fields = ['customers.description', 'couriers.name'];

    public function __construct(array $attributes = [])
    {
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
     * Packing list
     */
    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id');
    }

    /**
     * Status string
     */
    public function status()
    {
        return __(sprintf('status.%s', $this->status));
    }

    /**
     * Status available
     */
    public static function statusAvailable(): array
    {
        return [
            self::STATUS_PENDING_SEND => __(sprintf('status.%s', self::STATUS_PENDING_SEND)),
            self::STATUS_SENT => __(sprintf('status.%s', self::STATUS_SENT)),
        ];
    }

    /**
     * Status HTML
     */
    public function statusHtml()
    {
        $status = $this->status;
        $statusText = $this->status();
        $class = $status === self::STATUS_PENDING_SEND ? 'bg-info text-white' : 'bg-success text-white';

        return "<div class=\"{$class} d-inline-block p-1 rounded\" style='width: 80px; text-align: center'>
                    <strong>{$statusText}</strong>
                </div>";
    }

    /**
     * Orders today
     */
    public function scopeToday(Builder $query): Builder
    {
        $start = Carbon::now()->setTime(00, 00, 00);
        $end = Carbon::now()->setTime(23, 59, 59);

        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope search
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query
            ->select(['orders.*'])
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('couriers', 'couriers.id', '=', 'orders.courier_id')
        ;

        return $this->_search($query, $search);
    }

    /**
     * Scope report
     */
    public function scopeReport(Builder $query, array $params): Builder
    {
        $start = new Carbon($params['start']);
        $end = new Carbon($params['end']);
        $status = $params['status'];
        $customerId = $params['customer_id'];
        $courierId = $params['courier_id'];

        $query
            ->whereBetween('date', [$start, $end])
            ->with(['customer', 'courier'])
            ->orderBy('date')
        ;

        if ($status !== 0) {
            $query->where('status', $status);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($courierId) {
            $query->where('courier_id', $courierId);
        }

        return $query;
    }

    /**
     * Scope boxes sum
     */
    public function scopeWithBoxesSum(Builder $query): Builder
    {
        return $query
            ->selectRaw('orders.*, SUM(order_details.qty) as boxes_sum')
            ->join('order_details', 'order_details.order_id', '=', 'orders.id')
            ->groupBy('orders.id')
        ;
    }

    /**
     * Scope status pending
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_SEND);
    }

    /**
     * Scope packing list
     */
    public function scopePendingForPackingList(Builder $query, ?int $courierId, ?int $customerId): Builder
    {
        $query
            ->whereNull('packing_list_id')
            ->with(['orderDetails', 'customer', 'courier'])
        ;

        if ($courierId) {
            $query->where('courier_id', $courierId);
        }

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return $query;
    }

    /**
     * Date local accessor
     */
    public function getDateLocalAttribute()
    {
        return $this->dateToLocalDate($this->date);
    }
}
