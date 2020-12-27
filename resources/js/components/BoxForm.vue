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
                        {{ t('navbar.boxes') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row">

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="description"> {{ t('validation.attributes.description') }}</label>
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
                                        <strong>{{ t('validation.required', {attribute: 'description'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="size"> {{ t('validation.attributes.size') }}</label>
                                    <input
                                            type="text"
                                            name="size"
                                            id="size"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('size')}"
                                            v-validate
                                            data-vv-rules=""
                                            v-model="form.size"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('size', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'size'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="weight"> {{ t('validation.attributes.weight') }}</label>
                                    <input
                                            type="text"
                                            name="weight"
                                            id="weight"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('weight')}"
                                            v-validate
                                            data-vv-rules="regex:^[0-9]+(\.[0-9]+)?$"
                                            v-model="form.weight"
                                    >
                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('weight', 'regex')">
                                        <strong>{{ t('validation.numeric', {attribute: 'weight'}) }}</strong>
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
                    description: null,
                    size: null,
                    weight: null
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
                    ApiService.put('/warehouse/box/' + this.editData.uuid, this.form) :
                    ApiService.post('/warehouse/box', this.form);

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

                    ApiService.delete('/warehouse/box/' + this.editData.uuid).then(res => {

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
