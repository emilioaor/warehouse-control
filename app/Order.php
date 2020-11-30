<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        'status',
        'invoice_number',
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
     * Attach document to order
     */
    public function attachDocument(string $base64, string $filename): string
    {
        $explode = explode(',', $base64);
        $filename = sprintf('%s-%s', $filename, ((string) time()));

        if (strpos($explode[0], 'image/png') > 0) {
            $format = 'png';
        } elseif (strpos($explode[0], 'image/jpg') > 0 || strpos($explode[0], 'image/jpeg') > 0) {
            $format = 'jpg';
        } else {
            return false;
        }

        $path = sprintf('orders/%s/%s.%s', $this->uuid, $filename, $format);
        Storage::disk('public')->put($path, base64_decode($explode[1]));

        return $path;
    }
}
