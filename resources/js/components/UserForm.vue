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
                        {{ t('navbar.users') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="email"> {{ t('validation.attributes.email') }}</label>
                                    <input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('email') || emailExists}"
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

                                    <span class="invalid-feedback" role="alert" v-if="emailExists">
                                        <strong>{{ t('validation.unique', {attribute: 'email'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="name"> {{ t('validation.attributes.name') }}</label>
                                    <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('name')}"
                                            value=""
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.name"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="role"> {{ t('validation.attributes.role') }}</label>
                                    <select
                                            name="role"
                                            id="role"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('role')}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.role"
                                    >
                                        <option v-for="(role, i) in roles" :value="i">{{ role }}</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('role', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'role'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="password"> {{ t('validation.attributes.password') }}</label>
                                    <input
                                            type="password"
                                            name="password"
                                            id="password"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('password')}"
                                            v-validate
                                            :data-vv-rules="this.editData ? 'confirmed:password_confirmation' : 'required|confirmed:password_confirmation'"
                                            v-model="form.password"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'password'}) }}</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password', 'confirmed')">
                                        <strong>{{ t('validation.confirmed', {attribute: 'passwordConfirmation'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="password_confirmation"> {{ t('validation.attributes.passwordConfirmation') }}</label>
                                    <input
                                            type="password"
                                            name="password_confirmation"
                                            id="password_confirmation"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('password_confirmation')}"
                                            v-validate
                                            :data-vv-rules="this.editData ? 'confirmed:password' : 'required|confirmed:password'"
                                            v-model="form.password_confirmation"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'passwordConfirmation'}) }}</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'confirmed')">
                                        <strong>{{ t('validation.confirmed', {attribute: 'passwordConfirmation'}) }}</strong>
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
            roles: {
                type: Object,
                required: true
            },

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
                    email: null,
                    role: null,
                    password: null,
                    password_confirmation: null
                },

                emailExists: false,
                loading: false
            }
        },

        methods: {
            validateForm() {
                this.emailExists = false;
                this.$validator.validateAll().then(res => res && this.checkIfUserExists())
            },

            sendForm() {
                const apiService = this.editData ?
                    ApiService.put('/admin/user/' + this.editData.uuid, this.form) :
                    ApiService.post('/admin/user', this.form);

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            checkIfUserExists() {
                this.loading = true;

                ApiService.get('/admin/user/exists/' + this.form.email).then(res => {
                    this.emailExists =
                        (this.editData && res.data.data && this.editData.uuid !== res.data.data.uuid) ||
                        (! this.editData && res.data.data);

                    if (! this.emailExists) {
                        this.sendForm();
                    } else {
                        this.loading = false;
                    }
                }).catch(err => {
                    this.loading = false;
                })
            }
        },

        watch: {
            "form.email"(value, old) {
                this.emailExists = false;
            }
        }
    }
</script>
