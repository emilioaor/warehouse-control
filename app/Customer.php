<?php

namespace App;

use App\Contract\IuuidGenerator;
use App\Contract\SearchTrait;
use App\Contract\TimeZoneLocalTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model implements IuuidGenerator
{
    use SoftDeletes;
    Use UuidGeneratorTrait;
    use SearchTrait;
    use TimeZoneLocalTrait;

    protected $table = 'customers';

    protected $fillable = [
        'description',
        'phone',
        'email',
        'default_courier_id',
        'address'
    ];

    protected $with = ['defaultCourier'];

    protected $search_fields = [
        'customers.description',
        'customers.phone',
        'customer_emails.email',
        'couriers.name'
    ];

    /**
     * Default courier
     */
    public function defaultCourier(): BelongsTo
    {
        return $this->belongsTo(Courier::class, 'default_courier_id')->withTrashed();
    }

    /**
     * Assigned orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Emails
     */
    public function customerEmails(): HasMany
    {
        return $this->hasMany(CustomerEmail::class, 'customer_id');
    }

    /**
     * Scope search
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query->select(['customers.*'])
            ->join('couriers', 'couriers.id', '=', 'customers.default_courier_id')
            ->join('customer_emails', 'customer_emails.customer_id', '=', 'customers.id')
            ->distinct()
        ;

        return $this->_search($query, $search);
    }

    /**
     * Update customer emails
     */
    public function updateCustomerEmails(array $customerEmails): void
    {
        $notDelete = [];

        foreach ($customerEmails as $ce) {
            if (isset($ce['id'])) {
                // Update
                $customerEmail = CustomerEmail::query()->find($ce['id']);
                $customerEmail->email = $ce['email'];
                $customerEmail->save();
            } else {
                // Create
                $customerEmail = new CustomerEmail($ce);
                $customerEmail->customer_id = $this->id;
                $customerEmail->save();
            }

            $notDelete[] = $customerEmail->id;
        }

        // Delete
        $this->customerEmails()->whereNotIn('id', $notDelete)->delete();
    }
}
