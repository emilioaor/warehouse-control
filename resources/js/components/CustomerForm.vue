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
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'phone'}) }}</strong>
                                    </span>
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('phone', 'regex')">
                                        <strong>{{ t('validation.regex', {attribute: 'phone'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="email"> {{ t('validation.attributes.email') }}</label>
                                    <input
                                            type="text"
                                            name="email"
                                            id="email"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('email')}"
                                            v-validate
                                            data-vv-rules="required|email"
                                            v-model="form.email"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                                    </span>
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'email')">
                                        <strong>{{ t('validation.email', {attribute: 'email'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="email"> {{ t('validation.attributes.courierDefault') }}</label>
                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name', 'uuid']"
                                            @selectResult="changeCourier($event)"
                                            :input-class="errors.has('default_courier_id') ? 'is-invalid' : ''"
                                            :value="courier ? courier.searchDescription : ''"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="default_courier_id"
                                            id="default_courier_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.default_courier_id"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('default_courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courierDefault'}) }}</strong>
                                    </span>
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
                this.form = {...this.editData};
                this.courier = {
                    ...this.editData.default_courier,
                    searchDescription: this.editData.default_courier.name + ' / ' + this.editData.default_courier.uuid
                }
            }
        },

        data() {
            return {
                form: {
                    description: null,
                    phone: null,
                    email: null,
                    default_courier_id: null
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

            changeCourier(result) {
                this.courier = result;
                this.form.default_courier_id= result.id;
            },
        }
    }
</script>
