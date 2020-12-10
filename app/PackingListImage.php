<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingListImage extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;

    protected $table = 'packing_list_images';

    protected $fillable = ['packing_list_id', 'url'];

    /**
     * Packing list
     */
    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id')->withTrashed();
    }
}
