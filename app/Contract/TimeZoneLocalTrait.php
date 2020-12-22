<?php

namespace App\Contract;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

trait TimeZoneLocalTrait {

    public function getCreatedAtLocalAttribute()
    {
        return $this->dateToLocalDate($this->created_at);
    }

    public function getUpdatedAtLocalAttribute()
    {
        return $this->dateToLocalDate($this->updated_at);
    }

    public function dateToLocalDate(Carbon $date): Carbon
    {
        $clone = clone $date;

        if (Session::has('UTC')) {
            return $clone->utcOffset(Session::get('UTC'));
        }

        return $clone;
    }
}
