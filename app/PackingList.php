<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PackingList extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;
    use TimeZoneLocalTrait;

    /** Status */
    const STATUS_PENDING_SEND = 'pending_send';
    const STATUS_SENT = 'sent';

    protected $table = 'packing_lists';

    protected $fillable = ['courier_id'];

    protected $search_fields = ['couriers.name'];

    protected $dates = ['received_at', 'status_date'];

    /**
     * Attached images
     */
    public function packingListImages(): HasMany
    {
        return $this->hasMany(PackingListImage::class, 'packing_list_id');
    }

    /**
     * Orders received in this packing list
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'packing_list_id');
    }

    /**
     * Courier
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }

    /**
     * Received at local accessor
     *
     * @return Carbon|null
     */
    public function getReceivedAtLocalAttribute()
    {
        if (! $this->received_at) {
            return null;
        }

        return $this->dateToLocalDate($this->received_at);
    }

    /**
     * Scope search
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query
            ->select(['packing_lists.*'])
            ->join('couriers', 'couriers.id', '=', 'packing_lists.courier_id')
        ;

        return $this->_search($query, $search);
    }

    /**
     * Attach document to packing list
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

        $path = sprintf('packing-list/%s/%s.%s', $this->uuid, $filename, $format);
        Storage::disk('public')->put($path, base64_decode($explode[1]));

        return $path;
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
     * Scope status pending
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING_SEND);
    }

    /**
     * Scope report
     */
    public function scopeReport(Builder $query, array $params): Builder
    {
        $start = new Carbon($params['start']);
        $end = new Carbon($params['end']);
        $status = $params['status'];
        $courierId = $params['courier_id'];
        $customerId = $params['customer_id'];
        $invoiceNumber = $params['invoice_number'];

        $query
            ->selectRaw('packing_lists.*, COALESCE(received_at, packing_lists.created_at) as status_date')
            ->join('orders', 'orders.packing_list_id', '=', 'packing_lists.id')
            ->whereRaw("
                CASE WHEN  received_at IS NULL THEN 
                    packing_lists.created_at between '{$start}' and '{$end}'
                ELSE
                    packing_lists.received_at between '{$start}' and '{$end}'
                END
            ")
            ->with([
                'courier',
                'orders.orderDetails',
                'orders.customer',
            ])
            ->orderBy('status_date')
            ->distinct()
        ;

        if ($status !== 0) {
            $query->where('packing_lists.status', $status);
        }

        if ($courierId) {
            $query->where('packing_lists.courier_id', $courierId);
        }

        if ($customerId) {
            $query->where('orders.customer_id', $customerId);
        }

        if ($invoiceNumber) {
            $query->where('orders.invoice_number', $invoiceNumber);
        }

        return $query;
    }
}
