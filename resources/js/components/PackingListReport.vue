<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        {{ t('navbar.report') }}
                        {{ t('navbar.packingList') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="start"> {{ t('validation.attributes.startDate') }}</label>

                                    <date-picker
                                            name = "start"
                                            id = "start"
                                            language="en"
                                            input-class = "form-control date-picker"
                                            format = "dd/MM/yyyy"
                                            v-model="start"
                                            @input="form.start = changeDate($event, 0, 0, 0)"

                                    ></date-picker>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="end"> {{ t('validation.attributes.endDate') }}</label>

                                    <date-picker
                                            name = "end"
                                            id = "end"
                                            language="en"
                                            input-class = "form-control date-picker"
                                            format = "dd/MM/yyyy"
                                            v-model="end"
                                            @input="form.end = changeDate($event, 23, 59, 59)"

                                    ></date-picker>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.status') }}</label>

                                    <select
                                            name="status"
                                            id="status"
                                            class="form-control"
                                            v-model="form.status"
                                    >
                                        <option :value="0">{{ t('form.all') }}</option>
                                        <option
                                                v-for="(status, i) in statusList"
                                                :value="i">
                                            {{ status }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.courier') }}</label>
                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name']"
                                            @selectResult="changeCourier($event)"
                                            :nullable="true"
                                            :value="courier ? courier.searchDescription : ''"
                                    ></search-input>

                                    <input type="hidden" v-model="form.courier_id">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.customer') }}</label>
                                    <search-input
                                            route="/warehouse/customer"
                                            :description-fields="['description']"
                                            @selectResult="changeCustomer($event)"
                                            :nullable="true"
                                            :value="customer ? customer.searchDescription : ''"
                                    ></search-input>

                                    <input type="hidden" v-model="form.courier_id">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.salesOrder') }}</label>
                                    <input type="text" class="form-control" v-model="form.invoice_number">
                                </div>
                            </div>

                            <div>
                                <button class="btn btn-primary" v-if="!loading">
                                    <i class="fa fa-list-alt"></i>
                                     {{ t('form.report') }}
                                </button>

                                <img src="/img/loading.gif" v-if="loading">
                            </div>
                        </form>

                        <div class="row" v-if="results.length">
                            <div class="col-12">
                                <table class="table table-responsive mt-4">
                                    <thead>
                                    <tr>
                                        <th width="1%" class="text-center"></th>
                                        <th class="text-center">{{ t('validation.attributes.salesOrder') }}</th>
                                        <th class="text-center">{{ t('validation.attributes.customer') }}</th>
                                        <th class="text-center" width="1%">{{ t('validation.attributes.boxes') }}</th>
                                        <th class="text-center">{{ t('validation.attributes.size') }}</th>
                                        <th class="text-center">{{ t('validation.attributes.weight') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <template v-for="(result, i) in results">
                                            <template v-for="(order, ii) in result.orders">
                                                <tr v-if="ii === 0" class="order-head">
                                                    <td colspan="5" >
                                                        {{ result.courier.name }}
                                                        ({{ result.created_at | date(true) }})
                                                        {{ order.order_details.length }}
                                                        {{ t('form.lines') }}

                                                        <a
                                                                class="btn btn-link text-white"
                                                                :href="'/warehouse/packing-list/' + result.uuid + '/edit'"
                                                                target="_blank"
                                                        >
                                                            <i class="fa fa-edit"></i>
                                                            {{ t('form.edit') }}
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="d-inline-block p-1 rounded status text-center"
                                                            :class="result.status === 'pending_send' ? 'bg-info text-white' : 'bg-success text-white'"
                                                        >
                                                            {{ t('status.' + result.status) }}
                                                        </span>
                                                    </td>
                                                </tr>

                                                <template v-for="detail in order.order_details">
                                                    <tr>
                                                        <td class="">
                                                            <i class="fa fa-arrow-right"></i>
                                                        </td>
                                                        <td class="text-center">{{ order.invoice_number }}</td>
                                                        <td class="text-center">{{ order.customer.description }}</td>
                                                        <td class="text-center">{{ detail.qty }}</td>
                                                        <td class="text-center">{{ detail.size }}</td>
                                                        <td class="text-center">{{ detail.weight }}</td>
                                                    </tr>
                                                </template>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '../services/ApiService'

    export default {
        name: 'PackingListReport',
        props: {
            statusList: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                start: new Date(),
                end: new Date(),
                form: {
                    start: null,
                    courier_id: null,
                    customer_id: null,
                    invoice_number: null,
                    status: 0
                },
                courier: null,
                customer: null,
                loading: false,
                results: []
            }
        },

        mounted() {
            this.form.start = this.changeDate(this.start, 0, 0, 0);
            this.form.end = this.changeDate(this.end, 23, 59, 59);
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;
                this.results = [];

                ApiService.post('/warehouse/packing-list/report', this.form).then(res => {
                    this.results = res.data.data;
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCourier(result) {
                if (result) {
                    this.courier = result;
                    this.form.courier_id = result.id;
                } else {
                    this.courier = null;
                    this.form.courier_id = null;
                }
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

            changeDate(date, h, i, s) {
                date.setHours(h, i, s);

                return date;
            },
        }
    }
</script>

<style lang="scss">
    .date-picker[readonly] {
        background-color: #ffffff;
    }

    .status {
        width: 80px;
    }
    .order-head {
        background-color: #666666;
        color: #ffffff;
        font-size: .9rem;
        padding-top: 0;
        padding-bottom: 0;

        .btn {
            font-size: .8rem;
            &:focus {
                box-shadow: none;
                text-decoration: none;
            }
            &:hover {
                text-decoration: none;
            }
        }
    }
</style>