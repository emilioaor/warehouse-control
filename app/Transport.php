<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transport extends Model
{
    use SoftDeletes;
    use SearchTrait;
    use UuidGeneratorTrait;

    protected $fillable = ['name', 'phone', 'address'];

    protected $search_fields = ['name', 'phone', 'address'];

    /**
     * Packing lists
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packingLists()
    {
        return $this->hasMany(PackingList::class);
    }
}
