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
                        {{ t('navbar.transports') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="name"> {{ t('validation.attributes.name') }}</label>
                                    <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('name')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.name"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
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
                this.form = {...this.editData}
            }
        },

        data() {
            return {
                form: {
                    name: null,
                    phone: null,
                    address: null
                },
                loading: false
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;
                const apiService = this.editData ?
                    ApiService.put('/warehouse/transport/' + this.editData.uuid, this.form) :
                    ApiService.post('/warehouse/transport', this.form);

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

                    ApiService.delete('/warehouse/transport/' + this.editData.uuid).then(res => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    }).catch(err => {
                        this.loading = false;
                    })
                }

            }
        }
    }
</script>
