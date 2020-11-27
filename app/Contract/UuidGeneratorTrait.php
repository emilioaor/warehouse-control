<?php

namespace App\Contract;

trait UuidGeneratorTrait {

    public function __construct()
    {
        return $this instanceof IuuidGenerator && $this->generateUuid();
    }

    public function generateUuid(): string
    {
        return $this->uuid = sprintf('%s', uniqid());
    }
}