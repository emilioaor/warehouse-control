<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_emails', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 15)->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });

        $customers = \App\Customer::all();

        foreach ($customers as $customer) {
            $customerEmail = new \App\CustomerEmail();
            $customerEmail->customer_id = $customer->id;
            $customerEmail->email = $customer->email;
            $customerEmail->save();
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable();
        });

        $customers = \App\Customer::all();

        foreach ($customers as $customer) {
            foreach ($customer->customerEmails as $customerEmail) {
                $customer->email = $customerEmail->email;
                $customer->save();
                break;
            }
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->change();
        });

        Schema::dropIfExists('customer_emails');
    }
}
