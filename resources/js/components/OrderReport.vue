<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        {{ t('navbar.report') }}
                        {{ t('navbar.orders') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()" v-if="authUser()">


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
                                    <label> {{ t('validation.attributes.customer') }}</label>
                                    <search-input
                                            :route="authUser().role === 'seller' ? '/seller/customer' : '/warehouse/customer'"
                                            :description-fields="['description']"
                                            @selectResult="changeCustomer($event)"
                                            :nullable="true"
                                            :value="customer ? customer.searchDescription : ''"
                                    ></search-input>

                                    <input type="hidden" v-model="form.customer_id">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.courier') }}</label>
                                    <search-input
                                            :route="authUser().role === 'seller' ? '/seller/courier' : '/warehouse/courier'"
                                            :description-fields="['name']"
                                            @selectResult="changeCourier($event)"
                                            :nullable="true"
                                            :value="courier ? courier.searchDescription : ''"
                                    ></search-input>

                                    <input type="hidden" v-model="form.courier_id">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('validation.attributes.salesOrder') }}</label>
                                    <input type="text" class="form-control" v-model="form.invoice_number">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-if="authUser().role === 'admin'">
                                    <label> {{ t('validation.attributes.seeDeletedOrders') }}</label>
                                    <input type="checkbox" v-model="form.see_deleted">
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
                                <table class="table table-responsive table-striped mt-4">
                                    <thead>
                                        <tr>
                                            <th>{{ t('validation.attributes.date') }}</th>
                                            <th>{{ t('validation.attributes.customer') }}</th>
                                            <th>{{ t('validation.attributes.courier') }}</th>
                                            <th>{{ t('validation.attributes.salesOrder') }}</th>
                                            <th>{{ t('validation.attributes.sector') }}</th>
                                            <th>{{ t('validation.attributes.rate') }}</th>
                                            <th>{{ t('validation.attributes.weight') }}</th>
                                            <th>VW</th>
                                            <th>{{ t('validation.attributes.price') }}</th>
                                            <th width="1%" class="text-center">{{ t('validation.attributes.status') }}</th>
                                            <th width="1%" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(result, i) in results" :key="i">
                                            <td>{{ result.date |date(true) }}</td>
                                            <td>{{ result.customer.description }}</td>
                                            <td>{{ result.courier.name }}</td>
                                            <td>{{ result.invoice_number }}</td>
                                            <td>{{ result.sector ? result.sector.name : '' }}</td>
                                            <td>{{ result.rate }}</td>
                                            <td>{{ result.weight * result.qty }}</td>
                                            <td>{{ volumetricWeightSum(result.order_details, result.way) }}</td>
                                            <td>{{ result.price }}</td>
                                            <td class="text-center">
                                                <template v-if="result.deleted_at">
                                                    <span class="d-inline-block p-1 rounded status bg-danger text-white">
                                                        {{ t('form.deleted') }}
                                                    </span>
                                                </template>
                                                <template v-else>
                                                    <span
                                                        class="d-inline-block p-1 rounded status"
                                                        :class="result.status === 'pending_send' ? 'bg-info text-white' : 'bg-success text-white'"
                                                    >
                                                        {{ t('status.' + result.status) }}
                                                    </span>
                                                </template>
                                            </td>
                                            <td>
                                                <a
                                                    class="btn btn-warning"
                                                    target="_blank"
                                                    v-if="authUser() && ! result.deleted_at"
                                                    :href="authUser().role === 'seller' ? '/seller/order/' + result.uuid + '/edit' : '/warehouse/order/' + result.uuid + '/edit'">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
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
                    end: null,
                    customer_id: null,
                    courier_id: null,
                    status: 0,
                    invoice_number: null,
                    see_deleted: false
                },
                customer: null,
                courier: null,
                loading: false,
                results: [],
                user: this.authUser()
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
                const route = this.user.role === 'seller' ? '/seller/order/report' : '/warehouse/order/report';

                ApiService.post(route, this.form).then(res => {
                    this.results = res.data.data;
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                })
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

            changeCourier(result) {
                if (result) {
                    this.courier = result;
                    this.form.courier_id = result.id;
                } else {
                    this.courier = null;
                    this.form.courier_id = null;
                }
            },

            changeDate(date, h, i, s) {
                date.setHours(h, i, s);

                return date;
            },

            volumetricWeight(detail, way) {
                const split = detail.size ? detail.size.split('*') : [];
                let volumetricWeight = 0;

                const width = split[0];
                const length = split[1];
                const height = split[2];

                if (width && length && height) {

                    if (way === 'airway') {

                        volumetricWeight = (width * length * height) / 166;

                    } else if (way === 'seaway') {
                        volumetricWeight = (width * length * height) / 1728;
                    }

                    return (Math.ceil(volumetricWeight * 100) / 100) * detail.qty;
                }

                return 0;
            },

            volumetricWeightSum(details, way) {
                let sum = 0;
                details.forEach((detail) => sum += this.volumetricWeight(detail, way));

                return sum
            }
        }
    }
</script>

<style>
    .date-picker[readonly] {
        background-color: #ffffff;
    }

    .status {
        width: 80px;
    }
</style>
