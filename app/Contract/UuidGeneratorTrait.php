<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Builder;

trait UuidGeneratorTrait {

    public function initializeUuidGeneratorTrait()
    {
        return ! $this->uuid && $this->generateUuid();
    }

    public function generateUuid(): string
    {
        return $this->uuid = sprintf('%s', uniqid());
    }

    public function scopeUuid(Builder $query, string $uuid): Builder
    {
        return $query->where(sprintf('%s.uuid', $this->getTable()), $uuid);
    }
}