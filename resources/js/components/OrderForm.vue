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
                        {{ t('navbar.orders') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="customer_id"> {{ t('validation.attributes.customer') }}</label>

                                    <search-input
                                        route="/warehouse/customer"
                                        :description-fields="['description', 'uuid']"
                                        @selectResult="changeCustomer($event)"
                                        :input-class="errors.has('customer_id') ? 'is-invalid' : ''"
                                        :value="customer ? customer.searchDescription : ''"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="customer_id"
                                            id="customer_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.customer_id"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('customer_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'customer'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="customer_id"> {{ t('validation.attributes.courier') }}</label>

                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name', 'uuid']"
                                            @selectResult="changeCourier($event)"
                                            :input-class="errors.has('courier_id') ? 'is-invalid' : ''"
                                            :value="courier ? courier.searchDescription : ''"
                                            :disabled="! customer"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="courier_id"
                                            id="courier_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.courier_id"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courier'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-12">
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center">{{ t('validation.attributes.box') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.description') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.size') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.weight') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.qty') }}</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detail, i) in form.order_details">
                                                <td>
                                                    <search-input
                                                            route="/warehouse/box"
                                                            :description-fields="['description', 'size', 'weight']"
                                                            @selectResult="changeBoxDetail($event, i)"
                                                            :nullable="true"
                                                            :value="detail.searchDescription ? detail.searchDescription : ''"
                                                    ></search-input>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            :name="'description' + i"
                                                            :id="'description' + i"
                                                            :class="{'is-invalid': errors.has('description' + i)}"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            v-model="detail.description"
                                                            :disabled="detail.box_id"
                                                    >
                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('description' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'description'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            :name="'size' + i"
                                                            :id="'size' + i"
                                                            :class="{'is-invalid': errors.has('size' + i)}"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            v-model="detail.size"
                                                            :disabled="detail.box_id"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('size' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'size'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            :name="'weight' + i"
                                                            :id="'weight' + i"
                                                            :class="{'is-invalid': errors.has('weight' + i)}"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            v-model="detail.weight"
                                                            :disabled="detail.box_id"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('weight' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'weight'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            :name="'qty' + i"
                                                            :id="'qty' + i"
                                                            :class="{'is-invalid': errors.has('qty' + i)}"
                                                            v-validate
                                                            data-vv-rules="required|numeric"
                                                            v-model="detail.qty"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'qty'}) }}</strong>
                                                    </span>

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i, 'numeric')">
                                                        <strong>{{ t('validation.numeric', {attribute: 'qty'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" @click="removeDetail(i)">
                                                            <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6">
                                                    <button type="button" class="btn btn-success" @click="addDetail()">
                                                        <i class="fa fa-box"></i>
                                                        {{ t('form.add') }} {{ t('navbar.boxes') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <button class="btn btn-primary" v-if="!loading">
                                    <i class="fa fa-save"></i>
                                     {{ t('form.save') }}
                                </button>

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
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData}
            } else {
                this.addDetail()
            }
        },

        data() {
            return {
                form: {
                    customer_id: null,
                    courier_id: null,
                    order_details: []
                },
                loading: false,
                customer: null,
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
                    ApiService.put('/warehouse/order/' + this.editData.uuid, this.form) :
                    ApiService.post('/warehouse/order', this.form);

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCustomer(result) {
                this.customer = result;
                this.form.customer_id = result.id;

                this.courier = {
                    ...result.default_courier,
                    searchDescription: result.default_courier.name + ' / ' + result.default_courier.uuid
                };
                this.form.courier_id = result.default_courier_id;
            },

            changeCourier(result) {
                this.courier = result;
                this.form.courier_id= result.id;
            },

            addDetail() {
                this.form.order_details.push({
                    box_id: null,
                    description: null,
                    size: null,
                    weight: null,
                    qty: null,
                    searchDescription: null
                });
            },

            removeDetail(i) {
                if (this.form.order_details.length > 1) {
                    this.form.order_details.splice(i, 1);
                }
            },

            changeBoxDetail(result, i) {
                if (result) {
                    this.form.order_details[i].box_id = result.id;
                    this.form.order_details[i].description = result.description;
                    this.form.order_details[i].size = result.size;
                    this.form.order_details[i].weight = result.weight;
                    this.form.order_details[i].searchDescription = result.searchDescription;
                    this.$validator.reset();
                } else {
                    this.form.order_details[i].box_id = null;
                    this.form.order_details[i].description = null;
                    this.form.order_details[i].size = null;
                    this.form.order_details[i].weight = null;
                    this.form.order_details[i].searchDescription = null;
                }
            }
        }
    }
</script>
