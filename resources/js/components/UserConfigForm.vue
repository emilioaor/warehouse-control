<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-cog"></i>
                        {{ t('navbar.security') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="current_password"> {{ t('validation.attributes.currentPassword') }}</label>
                                    <input
                                            type="password"
                                            class="form-control"
                                            name="current_password"
                                            id="current_password"
                                            :class="{'is-invalid': errors.has('current_password')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.current_password"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('current_password', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'currentPassword'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="password"> {{ t('validation.attributes.newPassword') }}</label>
                                    <input
                                            type="password"
                                            class="form-control"
                                            name="password"
                                            id="password"
                                            :class="{'is-invalid': errors.has('password')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.password"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'newPassword'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="password_confirmation"> {{ t('validation.attributes.newPasswordConfirmation') }}</label>
                                    <input
                                            type="password"
                                            class="form-control"
                                            name="password_confirmation"
                                            id="password_confirmation"
                                            :class="{'is-invalid': errors.has('password_confirmation')}"
                                            v-validate
                                            data-vv-rules="required|confirmed:password"
                                            v-model="form.password_confirmation"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'newPasswordConfirmation'}) }}</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'confirmed')">
                                        <strong>{{ t('validation.confirmed', {attribute: 'newPasswordConfirmation'}) }}</strong>
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
        data() {
            return {
                form: {
                    current_password: null,
                    password: null,
                    password_confirmation: null
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

                ApiService.put('/warehouse/user/config', this.form).then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {

                    if (err.response.status === 400) {
                        location.reload();
                    } else {
                        this.loading = false;
                    }
                })
            }
        }
    }
</script>
