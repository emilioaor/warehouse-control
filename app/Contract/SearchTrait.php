<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Builder;

trait SearchTrait {

    /**
     * Search by any field
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $fields = $this->search_fields;

        if (! $fields) {
            throw new \Exception(sprintf('%s->search_fields is not defined', self::class));
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($fields, $search) {
                $s = strtolower($search);

                foreach ($fields as $field) {
                    $q->orWhereRaw(sprintf('lower(%s::text) LIKE \'%%%s%%\'', $field, $s));
                }
            });
        }

        return $query;
    }
}