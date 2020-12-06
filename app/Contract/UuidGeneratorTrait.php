<?php

namespace App\Contract;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait UuidGeneratorTrait {

    public function __construct(array $attributes = [])
    {
        Model::__construct($attributes);
        return $this instanceof IuuidGenerator && $this->generateUuid();
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