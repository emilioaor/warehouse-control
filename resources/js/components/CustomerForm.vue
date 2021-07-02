<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        <span v-if="editData">
                            <i class="fa fa-edit"></i>
                            {{ t('form.edit') }}
                        </span>
                        <span v-else>
                            <i class="fa fa-plus"></i>
                            {{ t('form.add') }}
                        </span>
                        {{ t('navbar.customers') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="description"> {{ t('validation.attributes.name') }}</label>
                                    <input
                                            type="text"
                                            name="description"
                                            id="description"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('description')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.description"
                                            :readonly="! authUser() || authUser().role === 'seller'"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('description', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="phone"> {{ t('validation.attributes.phone') }}</label>
                                    <input
                                            type="text"
                                            name="phone"
                                            id="phone"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('phone')}"
                                            v-validate
                                            data-vv-rules="required|regex:^([\+])?[0-9]+$"
                                            v-model="form.phone"
                                            :readonly="! authUser() || authUser().role === 'seller'"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'phone'}) }}</strong>
                                    </span>
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'regex')">
                                        <strong>{{ t('validation.regex', {attribute: 'phone'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="default_courier_id"> {{ t('validation.attributes.courierDefault') }}</label>
                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name']"
                                            @selectResult="changeCourier($event)"
                                            :input-class="errors.has('default_courier_id') ? 'is-invalid' : ''"
                                            :value="courier ? courier.searchDescription : ''"
                                            :readOnly="! authUser() || authUser().role === 'seller'"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="default_courier_id"
                                            id="default_courier_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.default_courier_id"
                                    >
                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('default_courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courierDefault'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="address"> {{ t('validation.attributes.address') }}</label>
                                    <input
                                            type="text"
                                            name="address"
                                            id="address"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('address')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.address"
                                            :readonly="! authUser() || authUser().role === 'seller'"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('address', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'address'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="locker_number"> {{ t('validation.attributes.lockerNumber') }}</label>
                                    <input
                                        type="text"
                                        name="locker_number"
                                        id="locker_number"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('locker_number')}"
                                        v-validate
                                        v-model="form.locker_number"
                                        :readonly="! authUser() || authUser().role === 'seller'"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('locker_number', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'lockerNumber'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="seller"> {{ t('validation.attributes.seller') }}</label>
                                    <search-input
                                        route="/warehouse/user/seller"
                                        :description-fields="['name']"
                                        @selectResult="changeSeller($event)"
                                        :input-class="errors.has('seller') ? 'is-invalid' : ''"
                                        :value="form.seller ? form.seller.searchDescription : ''"
                                        :readOnly="! authUser() || authUser().role === 'seller'"
                                    ></search-input>
                                    <input
                                        type="hidden"
                                        name="seller"
                                        id="seller"
                                        v-validate
                                        data-vv-rules="required"
                                        v-model="form.seller_id"
                                    >
                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('seller', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'seller'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="sector_id"> {{ t('validation.attributes.sector') }}</label>
                                    <select
                                        id="sector_id"
                                        name="sector_id"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('sector_id')}"
                                        v-validate
                                        data-vv-rules="required"
                                        v-model="form.sector_id"
                                    >
                                        <option
                                            v-for="sector in sectors"
                                            :value="sector.id">{{ sector.name }}
                                        </option>
                                    </select>

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('sector_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'sector'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-for="(customerEmail, i) in form.customer_emails">
                                    <label :for="'email' + i">
                                        {{ t('validation.attributes.email') }}
                                        {{ i + 1 }}
                                    </label>

                                    <div class="input-group">
                                        <input
                                                type="text"
                                                :name="'email' + i"
                                                :id="'email' + i"
                                                class="form-control"
                                                :class="{'is-invalid': errors.has('email'  + i)}"
                                                v-validate
                                                data-vv-rules="required|email"
                                                v-model="customerEmail.email"
                                                :readonly="! authUser() || authUser().role === 'seller'"
                                        >
                                        <span class="input-group-btn" v-if="i > 0">
                                            <button type="button" class="btn btn-danger" @click="removeEmail(i)">
                                                X
                                            </button>
                                        </span>

                                        <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email' + i, 'required')">
                                            <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                                        </span>
                                        <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email' + i, 'email')">
                                            <strong>{{ t('validation.email', {attribute: 'email'}) }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-if="this.authUser() && this.authUser().role !== 'seller'">
                                    <label> {{ t('form.addEmail') }}</label>
                                    <div>
                                        <button type="button" class="btn btn-add-email w-100" @click="addEmail()">
                                            <i class="fa fa-plus"></i>
                                            <i class="fa fa-card"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">

                                <div class="col-12 form-group">
                                    <h5>{{ t('form.specialRates') }}</h5>

                                    <table class="table table-responsive-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ t('validation.attributes.courier') }}</th>
                                                <th>{{ t('validation.attributes.way') }}</th>
                                                <th width="15%">{{ t('validation.attributes.rate') }}</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(customerRate, i) in form.customer_rates">
                                                <td>
                                                    <search-input
                                                        route="/warehouse/courier"
                                                        :description-fields="['name']"
                                                        @selectResult="changeRateCourier($event, i)"
                                                        :input-class="errors.has('courier' + i) ? 'is-invalid' : ''"
                                                        :value="customerRate.courier ? customerRate.courier.name : ''"
                                                    ></search-input>
                                                    <input type="hidden" :name="'courier' + i" v-validate data-vv-rules="required" v-if="! customerRate.courier">
                                                    <input type="hidden" :name="'same' + customerRate.courier_id + customerRate.way" v-validate data-vv-rules="required" v-if="hasSameRateConfig(customerRate)">

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('courier' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'courier'}) }}</strong>
                                                    </span>

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('same' + customerRate.courier_id + customerRate.way, 'required')">
                                                        <strong>{{ t('form.duplicated') }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <select
                                                        :name="'way' + i"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('way'  + i)}"
                                                        v-model="customerRate.way"
                                                        v-validate
                                                        data-vv-rules="required"
                                                    >
                                                        <option value="airway">{{ t('way.airway') }}</option>
                                                        <option value="seaway">{{ t('way.seaway') }}</option>
                                                    </select>

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('way' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'way'}) }}</strong>
                                                    </span>

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('same' + customerRate.courier_id + customerRate.way, 'required')">
                                                        <strong>{{ t('form.duplicated') }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        :name="'rate' + i"
                                                        class="form-control"
                                                        :class="{'is-invalid': errors.has('rate'  + i)}"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        v-model="customerRate.rate"
                                                    >

                                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('rate' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'rate'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click="removeCustomerRate(i)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4">
                                                    <button type="button" class="btn btn-success" @click="addCustomerRate()">
                                                        <i class="fa fa-plus"></i>
                                                        {{ t('form.addRate') }}
                                                    </button>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>

                            <div v-if="this.authUser() && this.authUser().role !== 'seller'">
                                <button class="btn btn-primary" v-if="!loading">
                                    <i class="fa fa-save"></i>
                                     {{ t('form.save') }}
                                </button>

                                <button-confirmation
                                        :label="t('form.delete')"
                                        btn-class="btn btn-danger"
                                        icon-class="fa fa-trash"
                                        v-if="!loading && editData && this.authUser() && this.authUser().role === 'admin'"
                                        :confirmation="t('form.areYouSure')"
                                        :buttons="[
                                        {
                                            label: t('form.yes'),
                                            btnClass: 'btn btn-success',
                                            code: 'yes'
                                        },
                                        {
                                            label: t('form.no'),
                                            btnClass: 'btn btn-danger',
                                            code: 'no'
                                        }
                                    ]"
                                        @confirmed="handleDelete($event)"
                                ></button-confirmation>

                                <img src="/img/loading.gif" v-if="loading">
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
        props: {
            editData: {
                type: Object,
                required: false
            },

            sectors: {
                type: Array,
                required: true
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.form,
                    ...this.editData
                };
                this.courier = {
                    ...this.editData.default_courier,
                    searchDescription: this.editData.default_courier.name
                };
                this.form.seller = {
                    ...this.editData.seller,
                    searchDescription: this.editData.seller.name
                };
            }
        },

        data() {
            return {
                form: {
                    description: null,
                    phone: null,
                    default_courier_id: null,
                    default_courier: null,
                    address: null,
                    locker_number: null,
                    customer_emails: [
                        {email: null}
                    ],
                    seller_id: null,
                    seller: null,
                    sector_id: null,
                    customer_rates: []
                },
                loading: false,
                courier: null
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;
                const apiService = this.editData ?
                    ApiService.put('/warehouse/customer/' + this.editData.uuid, this.form) :
                    ApiService.post('/warehouse/customer', this.form);

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            handleDelete(code) {
                if (code === 'yes') {
                    this.loading = true;

                    ApiService.delete('/warehouse/customer/' + this.editData.uuid).then(res => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    }).catch(err => {
                        this.loading = false;
                    })
                }

            },

            changeCourier(result) {
                this.courier = result;
                this.form.default_courier_id= result.id;
            },

            changeSeller(result) {
                this.form.seller = result;
                this.form.seller_id= result.id;
            },

            addEmail() {
                this.form.customer_emails.push({
                    email: null
                })
            },

            removeEmail(index) {
                this.form.customer_emails.splice(index, 1);
            },

            addCustomerRate() {
                this.form.customer_rates.push({
                    courier: null,
                    courier_id: null,
                    way: null,
                    rate: null
                });
            },

            changeRateCourier(event, i) {
                this.form.customer_rates[i].courier_id = event.id;
                this.form.customer_rates[i].courier = event;
            },

            removeCustomerRate(index) {
                this.form.customer_rates.splice(index, 1);
            },

            hasSameRateConfig(rate) {
                return (
                    rate.courier &&
                    rate.way &&
                    this.form.customer_rates
                        .filter(cr => cr.courier_id == rate.courier_id && cr.way === rate.way).length > 1
                )
            }
        }
    }
</script>

<style lang="scss" scoped>
    .btn-add-email {
        border: solid 1px #ccc;
    }

    .form-control[readonly],
    select[disabled] {
        background-color: #ffffff;
    }
</style>
