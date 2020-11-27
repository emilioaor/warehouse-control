<?php

namespace App\Contract;

interface IuuidGenerator {

    /**
     * Generate correlative
     */
    function generateUuid(): string;
}