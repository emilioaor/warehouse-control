<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ t('navbar.packingList') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row form">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="courier_id">{{ t('validation.attributes.courier') }}</label>

                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name']"
                                            @selectResult="changeCourier($event)"
                                            :input-class="errors.has('courier_id') ? 'is-invalid' : ''"
                                            :value="courier ? courier.searchDescription : ''"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="courier_id"
                                            id="courier_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.courier_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courier'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="customer_id">{{ t('validation.attributes.customer') }}</label>

                                    <search-input
                                            route="/warehouse/customer"
                                            :description-fields="['description']"
                                            @selectResult="changeCustomer($event)"
                                            :input-class="errors.has('customer_id') ? 'is-invalid' : ''"
                                            :value="customer ? customer.searchDescription : ''"
                                            :disabled="! courier"
                                            :nullable="true"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="customer_id"
                                            id="customer_id"
                                            v-validate
                                            data-vv-rules=""
                                            v-model="form.customer_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('customer_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'customer'}) }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="buttons">
                                <button class="btn btn-primary" v-if="!loading">
                                    <i class="fa fa-save"></i>
                                     {{ t('form.report') }}
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-info text-white"
                                    v-if="!loading && results.length"
                                    @click="print()"
                                >
                                    <i class="fa fa-print"></i>
                                    {{ t('form.print') }}
                                </button>

                                <img src="/img/loading.gif" v-if="loading">
                            </div>

                            <div class="row" v-if="results.length">
                                <div class="col-12 text-right table-head">
                                    <img src="/img/techland.png" class="logo">
                                </div>

                                <div class="col-8 table-head">
                                    <h5>
                                        <strong>{{ t('form.freightForwarder') }}:</strong>
                                        {{ courier ? courier.name : '' }}
                                    </h5>
                                </div>
                                <div class="col-4 text-right table-head">
                                    <h5>
                                        <strong>{{ t('form.date') }}:</strong>
                                        {{ (new Date()).toLocaleDateString() }}
                                    </h5>
                                </div>

                                <div class="col-12">
                                    <table class="table table-responsive mt-4">
                                        <thead>
                                            <tr>
                                                <th class="text-center">{{ t('validation.attributes.salesOrder') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.customer') }}</th>
                                                <th class="text-center" width="1%">{{ t('validation.attributes.boxes') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.size') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.weight') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(result, i) in results">
                                                <tr v-for="(detail, ii) in result.order_details" :key="i + '-' + ii">
                                                    <td class="text-center">{{ result.invoice_number }}</td>
                                                    <td class="text-center">{{ result.customer.description }}</td>
                                                    <td class="text-center">{{ detail.qty }}</td>
                                                    <td class="text-center">{{ detail.size }}</td>
                                                    <td class="text-center">{{ detail.weight }}</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '../services/ApiService'

    export default {

        data() {
            return {
                form: {
                    courier_id: null,
                    customer_id: null
                },
                loading: false,
                courier: null,
                customer: null,
                results: []
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;
                this.results = [];

                ApiService.post('/warehouse/order/packing-list', this.form).then(res => {

                    if (res.data.success) {
                        this.results = res.data.data;
                        this.loading = false;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCourier(result) {
                this.courier = result;
                this.form.courier_id= result.id;
            },

            changeCustomer(result) {
                if (result) {
                    this.customer = result;
                    this.form.customer_id = result.id;
                } else {
                    this.customer = null;
                    this.form.customer_id = null;
                }
            },

            print() {
                window.print()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .table-head {
        display: none;
    }

    @media print {
        @page {
            margin: 85px 0 100px 0;
        }
        .table-head {
            display: block;
        }
        .logo {
            margin: 0 0 1rem;
        }

        h5 {
            font-size: 1.5rem;
            margin: 1rem 0;
        }

        .form, .buttons, .card-header {
            display: none;
        }

        .card {
            border: none
        }

        .table {
            border: solid 2px #999999;
            font-size: 1.2rem;
            margin-top: 0 !important;
        }
    }
</style>
