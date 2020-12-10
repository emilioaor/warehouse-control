<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
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

    protected $table = 'packing_lists';

    protected $fillable = ['courier_id'];

    protected $search_fields = ['courier.name'];

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
}
