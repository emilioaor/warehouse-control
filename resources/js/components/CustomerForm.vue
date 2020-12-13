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
                                    <label for="default_courier_id"> {{ t('validation.attributes.courierDefault') }}</label>
                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name']"
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
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('address', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'address'}) }}</strong>
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

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label> {{ t('form.addEmail') }}</label>
                                    <div>
                                        <button type="button" class="btn btn-add-email w-100" @click="addEmail()">
                                            <i class="fa fa-plus"></i>
                                            <i class="fa fa-card"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
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
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
                this.courier = {
                    ...this.editData.default_courier,
                    searchDescription: this.editData.default_courier.name
                }
            }
        },

        data() {
            return {
                form: {
                    description: null,
                    phone: null,
                    default_courier_id: null,
                    address: null,
                    customer_emails: [
                        {email: null}
                    ]
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

            addEmail() {
                this.form.customer_emails.push({
                    email: null
                })
            },

            removeEmail(index) {
                this.form.customer_emails.splice(index, 1);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .btn-add-email {
        border: solid 1px #ccc;
    }
</style>