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

                                <div class="col-12 text-right print-only">
                                    <img src="/img/logo.jpeg" class="logo">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="customer_id"> {{ t('validation.attributes.customer') }}</label>

                                    <search-input
                                        route="/warehouse/customer"
                                        :description-fields="['description']"
                                        @selectResult="changeCustomer($event)"
                                        :input-class="errors.has('customer_id') ? 'is-invalid' : ''"
                                        :value="customer ? customer.searchDescription : ''"
                                        :read-only="!! editData"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="customer_id"
                                            id="customer_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.customer_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('customer_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'customer'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="courier_id"> {{ t('validation.attributes.courier') }}</label>

                                    <search-input
                                            route="/warehouse/courier"
                                            :description-fields="['name']"
                                            @selectResult="changeCourier($event)"
                                            :input-class="errors.has('courier_id') ? 'is-invalid' : ''"
                                            :value="courier ? courier.searchDescription : ''"
                                            :disabled="! customer"
                                            :read-only="editData && this.authUser() && (this.authUser().role !== 'admin' || editData.status === 'sent')"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="courier_id"
                                            id="courier_id"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.courier_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courier'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 form-group col-md-4">
                                    <label for="invoice_number"> {{ t('validation.attributes.way') }}</label>

                                    <select
                                        class="form-control"
                                        name="way"
                                        id="way"
                                        :class="{'is-invalid': errors.has('way')}"
                                        v-validate
                                        data-vv-rules=""
                                        v-model="form.way"
                                        :disabled="!! editData"
                                    >
                                        <option
                                            v-for="(label, value) in waysAvailable"
                                            :value="value"
                                        >{{ label }}</option>
                                    </select>

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('way', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'way'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 form-group col-md-4">
                                    <label for="invoice_number"> {{ t('validation.attributes.salesOrder') }}</label>

                                    <input
                                            type="text"
                                            class="form-control"
                                            name="invoice_number"
                                            id="invoice_number"
                                            :class="{'is-invalid': errors.has('invoice_number') || exists}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.invoice_number"
                                            :readonly="!! editData"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('invoice_number', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'salesOrder'}) }}</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="exists">
                                        <strong>{{ t('validation.unique', {attribute: 'salesOrder'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group print-only7" v-if="!! editData">
                                    <label> {{ t('validation.attributes.date') }}</label>
                                    <input type="text" class="form-control" :readonly="true" :value="editData.created_at | date(true)">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group not-print" v-if="!! editData">
                                    <label> {{ t('validation.attributes.created_by') }}</label>
                                    <input type="text" class="form-control" :readonly="true" :value="editData.created_by.name">
                                </div>

                                <div class="col-sm-6 col-md-4 form-group not-print" v-if="!! editData">
                                    <label> {{ t('validation.attributes.status') }}</label>
                                    <div>
                                        <span
                                            class="d-inline-block p-1 rounded status"
                                            :class="form.status === 'pending_send' ? 'bg-info text-white' : 'bg-success text-white'"
                                        >
                                            {{ t('status.' + form.status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12 print-only" v-if="!! editData">
                                    <label> {{ t('validation.attributes.courierAddress') }}</label>
                                    <div>
                                       {{ editData.courier.address }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <table class="table table-responsive mt-4">
                                        <thead>
                                            <tr>
                                                <td v-if="!editData">{{ t('validation.attributes.box') }}</td>
                                                <td>{{ t('validation.attributes.description') }}</td>
                                                <td>{{ t('validation.attributes.size') }}</td>
                                                <td>{{ t('validation.attributes.weight') }}</td>
                                                <td width="1%">{{ t('validation.attributes.qty') }}</td>
                                                <td width="5%" v-if="! editData"></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detail, i) in form.order_details">
                                                <td v-if="!editData">
                                                    <search-input
                                                            route="/warehouse/box"
                                                            ref="searchBox"
                                                            :description-fields="['description', 'size', 'weight']"
                                                            @selectResult="changeBoxDetail($event, i)"
                                                            :nullable="true"
                                                            :value="detail.searchDescription ? detail.searchDescription : ''"
                                                            :read-only="!! editData"
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
                                                            :readonly="detail.box_id || editData"
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
                                                            :readonly="editData"
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
                                                            :readonly="editData"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('weight' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'weight'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control input-qty"
                                                            :name="'qty' + i"
                                                            :id="'qty' + i"
                                                            :class="{'is-invalid': errors.has('qty' + i)}"
                                                            v-validate
                                                            data-vv-rules="required|numeric"
                                                            v-model="detail.qty"
                                                            :readonly="!! editData"
                                                    >

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i, 'required')">
                                                        <strong>{{ t('validation.required', {attribute: 'qty'}) }}</strong>
                                                    </span>

                                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i, 'numeric')">
                                                        <strong>{{ t('validation.numeric', {attribute: 'qty'}) }}</strong>
                                                    </span>
                                                </td>
                                                <td v-if="! editData">
                                                    <button type="button" class="btn btn-danger" @click="removeDetail(i)">
                                                            <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="! editData">
                                                <td colspan="6">
                                                    <button
                                                            type="button"
                                                            class="btn btn-success"
                                                            @click="addDetail()"
                                                    >
                                                        <i class="fa fa-box"></i>
                                                        {{ t('form.add') }} {{ t('navbar.boxes') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot v-if="editData">
                                            <tr>
                                                <td colspan="2">{{ t('form.totals') }}</td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        readonly="readonly"
                                                        :value="weightSum"
                                                    >
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        readonly="readonly"
                                                        :value="qtySum"
                                                    >
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="col-12 form-group">
                                    <template v-if="isCommentShow">
                                        <label for="comment"> {{ t('validation.attributes.comment') }}</label>

                                        <textarea
                                            name="comment"
                                            id="comment"
                                            class="form-control mt-1"
                                            cols="30"
                                            rows="5"
                                            v-model="form.comment"
                                        ></textarea>
                                    </template>

                                    <template v-else>
                                        <button type="button" class="btn btn-secondary" @click="showComment()">
                                            <i class="fa fa-comment"></i>
                                            {{ t('form.addComment') }}
                                        </button>
                                    </template>
                                </div>

                                <div class="col-sm-6 col-md-3 form-group" v-if="form.status === 'sent'">
                                    <label for="sign"> {{ t('validation.attributes.sign') }}</label>

                                    <div class="sign" @click="openImageExplorer(form.packing_list.sign)">
                                        <img
                                            :src="'/storage/' + form.packing_list.sign"
                                        >
                                    </div>
                                </div>

                                <template v-if="form.status === 'sent'">
                                    <div class="col-sm-6 col-md-3 form-group" v-for="image in form.packing_list.packing_list_images">
                                        <label for="photo"> {{ t('validation.attributes.photo') }}</label>

                                        <div class="photo" @click="openImageExplorer(image.url)">
                                            <img
                                                :src="'/storage/' + image.url"
                                            >
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div>
                                <button
                                    class="btn"
                                    :class="{
                                        'btn-primary': !editData,
                                        'btn-success': editData
                                    }"
                                    v-if="!loading && this.authUser() && (! editData || this.authUser().role === 'admin')"
                                >
                                    <i class="fa fa-save"></i>
                                     {{ t('form.save') }}
                                </button>

                                <iframe
                                    :src="'/warehouse/order/' + editData.uuid + '/labels'"
                                    frameborder="0"
                                    v-if="!loading && editData"
                                ></iframe>

                                <button
                                    type="button"
                                    class="btn btn-info text-white"
                                    @click="print()"
                                    v-if="!loading && editData"
                                >
                                    <i class="fa fa-print"></i>
                                    {{ t('form.print') }}
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
            waysAvailable: {
                type: Object,
                required: true
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.editData,
                    order_details: this.editData.order_details.map(detail => {
                        return {
                            ...detail,
                            searchDescription: detail.box_id ?
                                detail.description + ' / ' + detail.size + ' / ' + detail.weight :
                                ''
                        }
                    })
                };
                this.customer = {
                    ...this.editData.customer,
                    searchDescription: this.editData.customer.description
                };
                this.courier = {
                    ...this.editData.courier,
                    searchDescription: this.editData.courier.name
                };
            } else {
                this.addDetail()
            }
        },

        data() {
            return {
                form: {
                    customer_id: null,
                    courier_id: null,
                    invoice_number: null,
                    order_details: [],
                    comment: null,
                    way: 'airway'
                },
                loading: false,
                customer: null,
                courier: null,
                isCommentShow: this.editData && this.editData.comment,
                exists: false
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
                    if (err.response.status === 400) {
                        location.reload();
                    } else if (err.response.status === 422) {
                        if (err.response.data.errors.invoice_number) {
                            this.exists = true;
                        }
                    }
                })
            },

            handleDelete(code) {
                if (code === 'yes') {
                    this.loading = true;

                    ApiService.delete('/warehouse/order/' + this.editData.uuid).then(res => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    }).catch(err => {
                        this.loading = false;
                    })
                }

            },

            changeCustomer(result) {
                this.customer = result;
                this.form.customer_id = result.id;

                this.courier = {
                    ...result.default_courier,
                    searchDescription: result.default_courier.name
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

                if (this.form.order_details.length > 1) {
                    window.setTimeout(() => this.$refs.searchBox[this.form.order_details.length - 1].openModal());
                }
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
                    this.errors.remove('description' + i);
                    this.errors.remove('size' + i);
                    this.errors.remove('weight' + i);
                } else {
                    this.form.order_details[i].box_id = null;
                    this.form.order_details[i].description = null;
                    this.form.order_details[i].size = null;
                    this.form.order_details[i].weight = null;
                    this.form.order_details[i].searchDescription = null;
                }
            },

            openImageExplorer(url) {
                window.open('/storage/' + url);
            },

            print() {
                window.print();
            },

            showComment() {
                this.isCommentShow = true;
                window.setTimeout(() => document.querySelector('#comment').focus(), 500);
            }
        },

        computed: {
            weightSum() {
                let sum = 0;
                this.form.order_details.forEach(detail => sum = Math.round((sum + detail.weight * detail.qty) * 100) / 100);

                return sum
            },

            qtySum() {
                let sum = 0;
                this.form.order_details.forEach(detail => sum += detail.qty);

                return sum
            }
        },

        watch: {
            "form.invoice_number"() {
                this.exists = false;
            }
        }
    }
</script>

<style scoped lang="scss">
    .sign,
    .photo {
        background-color: #ccc;
        color: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 40px;
        height: 160px;
        cursor: pointer;

        &.is-invalid {
            border: solid 2px #e3342f;
            opacity: .5;
        }

        img {
            max-height: 160px;
            max-width: 100%;
        }
    }

    .status {
        width: 80px;
        text-align: center;
    }

    .form-control[readonly],
    select[disabled] {
        background-color: #ffffff;
    }

    iframe {
        width: 155px;
        height: 37px;
        position: relative;
        bottom: -14px;
    }

    .input-qty {
        min-width: 200px;
    }
</style>

<style lang="scss">
    .print-only {
        display: none;
    }

    @media print {
        .print-only {
            display: block;
        }
        .not-print {
            display: none;
        }

        body {
            font-size: 20px;                                                                               ;
        }

        .logo {
            margin: 0 0 1rem;
            width: 300px;
        }

        .form-control {
            border: none;
            font-size: 20px;
        }

        .card-header {
            display: none;
        }

        label {
            font-weight: bold;
        }

        thead, tfoot {
            font-weight: bold;

            .form-control {
                font-weight: bold;
            }
        }
    }
</style>
