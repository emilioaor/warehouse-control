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
                        {{ t('navbar.packingList') }}
                    </div>

                    <div class="card-body">

                        <form @submit.prevent="validateForm()">


                            <div class="row form">

                                <div class="col-sm-6 col-md-4 form-group" v-if="! editData">
                                    <label for="customer_id">{{ t('validation.attributes.customer') }}</label>

                                    <search-input
                                            route="/warehouse/customer"
                                            :description-fields="['description']"
                                            @selectResult="changeCustomer($event)"
                                            :input-class="errors.has('customer_id') ? 'is-invalid' : ''"
                                            :value="customer ? customer.searchDescription : ''"
                                            :nullable="true"
                                            :read-only="!!editData"
                                    ></search-input>
                                    <input
                                            type="hidden"
                                            name="customer_id"
                                            id="customer_id"
                                            v-validate
                                            data-vv-rules=""
                                            v-model="form.customer_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('customer_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'customer'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group">
                                    <label for="courier_id">{{ t('validation.attributes.courier') }}</label>

                                    <search-input
                                        route="/warehouse/courier"
                                        :description-fields="['name']"
                                        @selectResult="changeCourier($event)"
                                        :input-class="errors.has('courier_id', 'report') ? 'is-invalid' : ''"
                                        :value="courier ? courier.searchDescription : ''"
                                        :nullable="true"
                                        :read-only="!!editData"
                                    ></search-input>
                                    <input
                                        type="hidden"
                                        name="courier_id"
                                        id="courier_id"
                                        v-validate
                                        data-vv-rules=""
                                        data-vv-scope="report"
                                        v-model="form.courier_id"
                                    >

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('courier_id', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'courier'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-if="editData">
                                    <label for="courier_id">{{ t('validation.attributes.transport') }}</label>
                                    <select
                                        name="transport"
                                        id="transport"
                                        class="form-control"
                                        v-model="form.transport_id"
                                        :disabled="editData.status === 'sent'"
                                    >
                                        <option :value="null">
                                            {{ t('form.sameCourier') }}
                                            ({{ form.courier ? form.courier.name : '' }})
                                        </option>
                                        <option
                                            v-for="transport in transports"
                                            :key="transport.id"
                                            :value="transport.id"
                                        >{{ transport.name }}</option>
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-if="!editData">
                                    <label for="invoice_number"> {{ t('validation.attributes.salesOrder') }}</label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="invoice_number"
                                        id="invoice_number"
                                        :class="{'is-invalid': errors.has('invoice_number')}"
                                        v-validate
                                        data-vv-rules=""
                                        v-model="form.invoice_number"
                                    >

                                    <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('invoice_number', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'invoiceNumber'}) }}</strong>
                                    </span>
                                </div>

                                <div class="col-sm-6 col-md-4 form-group" v-if="editData">
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
                            </div>

                            <div class="buttons">
                                <button class="btn btn-primary" v-if="!loading && !editData">
                                    <i class="fa fa-list-alt"></i>
                                     {{ t('form.report') }}
                                </button>

                                <button-confirmation
                                        :label="t('form.save')"
                                        btn-class="btn btn-secondary"
                                        icon-class="fa fa-save"
                                        v-if="!loading && results.length && !editData"
                                        :disabled="! oneCourierOnly"
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
                                        @confirmed="validatePackingList($event)"
                                ></button-confirmation>

                                <button
                                    class="btn btn-primary"
                                    v-if="!loading && editData && editData.status === 'sent'"
                                    @click="saveImages()"
                                >
                                    <i class="fa fa-list-alt"></i>
                                    {{ t('form.save') }}
                                </button>

                                <button-confirmation
                                        :label="t('form.markAsReceived')"
                                        btn-class="btn btn-primary"
                                        icon-class="fa fa-check"
                                        v-if="!loading && form.status === 'pending_send'"
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
                                        @confirmed="validatePackingList($event)"
                                ></button-confirmation>

                                <packing-list-email
                                    v-if="!loading && editData"
                                    :title="t('form.sendByEmail')"
                                    :edit-data="editData"
                                ></packing-list-email>

                                <a
                                    :href="'/warehouse/packing-list/' + editData.uuid + '/excel'"
                                    class="btn btn-success"
                                    v-if="!loading && editData"
                                >
                                    <i class="fa fa-download"></i>
                                    {{ t('form.downloadExcel') }}
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-info text-white"
                                    v-if="!loading && results.length && editData"
                                    @click="print()"
                                >
                                    <i class="fa fa-print"></i>
                                    {{ t('form.print') }}
                                </button>

                                <span class="invalid-feedback d-block" role="alert" v-if="results.length && ! oneCourierOnly">
                                    <strong>{{ t('validation.oneCourier') }}</strong>
                                </span>

                                <img src="/img/loading.gif" v-if="loading">
                            </div>

                            <div class="row mt-4 not-print" v-if="results.length && editData">
                                <div class="col-sm-6 col-md-3 form-group">
                                    <label for="sign"> {{ t('validation.attributes.sign') }}</label>

                                    <div
                                            class="sign"
                                            @click="openSignature()"
                                            :class="{'is-invalid': errors.has('sign')}"
                                    >

                                        <img
                                                :src="form.status === 'sent' ? '/storage/' + form.sign : form.sign"
                                                alt="sign"
                                                v-if="form.sign"
                                        >

                                        <i class="fa fa-edit" v-else></i>
                                    </div>

                                    <span class="invalid-feedback d-block" role="alert" v-if="errors.firstByRule('sign', 'required')">
                                        <strong>{{ t('validation.required', {attribute: 'sign'}) }}</strong>
                                    </span>

                                    <input type="hidden" name="sign" v-model="form.sign" v-validate data-vv-rules="required">

                                    <signature
                                            ref="signature"
                                            @save="changeSign($event)"
                                    ></signature>
                                </div>

                                <div class="col-sm-6 col-md-3 form-group" v-for="(image, i) in form.packing_list_images">
                                    <label for="photo"> {{ t('validation.attributes.photo') }}</label>

                                    <div class="photo">
                                        <button type="button" class="btn btn-danger" @click.prevent="removeImage(i)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <img
                                            @click="openImageExplorer(image)"
                                            :src="image.id ? '/storage/' + image.url : image.url"
                                        >
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-3 form-group not-print" v-if="editData">
                                    <label for="photo"> {{ t('validation.attributes.photo') }}</label>

                                    <input
                                            type="file"
                                            id="photo"
                                            class="d-none"
                                            accept="image/jpeg|image/png"
                                            @change="changeImage($event)"
                                    >

                                    <div
                                            class="photo"
                                            :class="{'is-invalid': errors.has('photo')}"
                                            @click="openImageExplorer()"
                                    >
                                        <i class="fa fa-camera"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="results.length">
                                <div class="col-12 text-right print-only">
                                    <img src="/img/logo.jpeg" class="logo">
                                </div>

                                <div class="col-7 print-only">
                                    <h5>
                                        {{ courier ? courier.address : '' }}
                                    </h5>
                                </div>

                                <div class="col-5 text-right print-only">
                                    <h5>
                                        <strong>{{ t('form.phone') }}:</strong> {{ courier ? courier.phone : '' }}
                                    </h5>
                                </div>

                                <div class="col-7 print-only">
                                    <h5>
                                        <strong>{{ t('form.freightForwarder') }}:</strong>
                                        {{ courier ? courier.name : '' }}
                                    </h5>
                                </div>
                                <div class="col-5 text-right print-only">
                                    <h5>
                                        <strong>{{ t('form.date') }}:</strong>
                                        <span v-if="!editData">{{ (new Date()) | date(true) }}</span>
                                        <span v-else-if="editData.status === 'sent'">{{ (new Date(editData.received_at)) | date(true) }}</span>
                                        <span v-else>{{ (new Date(editData.created_at)) | date(true) }}</span>
                                    </h5>
                                </div>

                                <div class="col-12">
                                    <table class="table table-responsive mt-2">
                                        <thead>
                                            <tr>
                                                <th width="1%" class="text-center not-print" v-if="! editData"></th>
                                                <th class="text-center">{{ t('validation.attributes.way') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.salesOrder') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.customer') }}</th>
                                                <th class="text-center" width="1%">{{ t('validation.attributes.boxes') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.size') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.weight') }}</th>
                                                <th class="text-center">
                                                    {{ t('validation.attributes.weight') }} x
                                                    {{ t('validation.attributes.qty') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(result, i) in results">
                                                <template v-for="(detail, ii) in result.order_details">
                                                    <tr v-if="ii === 0 && ! editData" class="not-print">
                                                        <td colspan="8" class="order-head">
                                                            {{ result.courier.name }}
                                                            ({{ result.date | date }})
                                                            {{ result.order_details.length }}
                                                            {{ t('form.lines') }}

                                                            <button
                                                                type="button"
                                                                class="btn btn-link text-white pull-right"
                                                                @click="deleteOrder(i)"
                                                            >
                                                                <i class="fa fa-trash"></i>
                                                                {{ t('form.delete') }}
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="not-print" v-if="! editData">
                                                            <i class="fa fa-arrow-right"></i>
                                                        </td>
                                                        <td class="text-center">{{ t('way.' + result.way) }}</td>
                                                        <td class="text-center">{{ result.invoice_number }}</td>
                                                        <td class="text-center">{{ result.customer.description }}</td>
                                                        <td class="text-center">{{ detail.qty }}</td>
                                                        <td class="text-center">{{ detail.size }}</td>
                                                        <td class="text-center">{{ detail.weight }}</td>
                                                        <td class="text-center">{{ detail.weight * detail.qty }}</td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <tr v-if="editData">
                                                <th class="text-center">{{ t('form.totals') }}</th>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">{{ qtySum }}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">{{ weightSum }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-5 print-only print-flex" v-if="results.length && editData && editData.status === 'sent'">
                                <div class="col-sm-6 form-group">
                                    <label> {{ t('validation.attributes.sign') }}</label>

                                    <div class="sign">
                                        <img :src="form.status === 'sent' ? '/storage/' + form.sign : form.sign" v-if="form.sign">
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group" v-for="(image, i) in form.packing_list_images">
                                    <label> {{ t('validation.attributes.photo') }}</label>

                                    <div class="photo">
                                        <img
                                            :src="image.id ? '/storage/' + image.url : image.url"
                                        >
                                    </div>
                                </div>
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

            transports: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                form: {
                    courier_id: null,
                    customer_id: null,
                    transport_id: null,
                    invoice_number: null,
                    sign: null,
                    packing_list_images: [],
                    orderIds: []
                },
                loading: false,
                courier: null,
                customer: null,
                results: []
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {... this.editData};
                this.courier = {
                    ... this.editData.courier,
                    searchDescription: this.editData.courier.name
                };
                this.results = this.editData.orders;
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll('report').then(res => res && this.sendForm())
            },

            sendForm() {
                this.loading = true;
                this.results = [];

                ApiService.post('/warehouse/order/packing-list', this.form).then(res => {

                    if (res.data.success) {
                        this.results = res.data.data;
                        this.loading = false;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            validatePackingList(code) {
                if (code === 'yes') {
                    this.$validator.validateAll().then(res => res && this.savePackingList())
                }
            },

            savePackingList() {
                this.loading = true;
                const payload = {
                    ...this.form
                };
                payload.orderIds = this.results.map(r => r.id);
                payload.courier_id = this.results[0].courier_id;

                const apiService = this.editData ?
                    ApiService.put('/warehouse/packing-list/' + this.editData.uuid, payload) :
                    ApiService.post('/warehouse/packing-list', payload);

                apiService.then(res => {

                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            saveImages() {
                this.loading = true;

                ApiService.post('/warehouse/packing-list/' + this.form.uuid + '/images', {
                    packing_list_images: this.form.packing_list_images
                }).then(res => {

                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeCourier(result) {
                if (result) {
                    this.courier = result;
                    this.form.courier_id= result.id;
                } else {
                    this.courier = null;
                    this.form.courier_id= null;
                }

                this.results = [];
            },

            changeCustomer(result) {
                if (result) {
                    this.customer = result;
                    this.form.customer_id = result.id;
                } else {
                    this.customer = null;
                    this.form.customer_id = null;
                }

                this.results = [];
            },

            print() {
                window.print()
            },

            deleteOrder(index) {
                this.results.splice(index, 1);
            },

            openImageExplorer(image) {
                if (image && image.id) {
                    window.open('/storage/' + image.url);
                } else if (! image) {
                    document.querySelector('#photo').click();
                }
            },

            openSignature() {
                if (this.form.status === 'sent') {
                    if (this.form.sign) {
                        window.open('/storage/' + this.form.sign);
                    }
                } else {
                    this.$refs.signature.open();
                }
            },

            changeImage(e) {
                const file = $('#photo')[0].files[0];

                if (!file || (file.type !== 'image/png' && file.type !== 'image/jpeg')) {
                    return false;
                }

                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    if (reader.result) {
                        this.form.packing_list_images.push({
                            url: reader.result
                        });
                        this.errors.remove('photo');
                    }
                });

                reader.readAsDataURL(file);
            },

            changeSign(event) {
                this.form.sign = event.base64;
                this.errors.remove('sign');
            },

            removeImage(index) {
                this.form.packing_list_images.splice(index, 1);
            },

            volumetricWeight(detail) {
                const weight = detail.weight;
                const split = detail.size ? detail.size.split('*') : [];
                let volumetricWeight = 0;
                const width = split[0];
                const length = split[1];
                const height = split[2];

                if (width && length && height) {

                    if (this.form.way === 'airway') {

                        volumetricWeight = (width * length * height) / 166;
                        // volumetricWeight = volumetricWeight > weight ? volumetricWeight : weight;

                    } else if (this.form.way === 'seaway') {
                        volumetricWeight = (width * length * height) / 1728;
                    }

                    return (Math.ceil(volumetricWeight * 100) / 100) * detail.qty;
                }

                return null;
            }
        },

        computed: {
            oneCourierOnly() {
                if (this.results.length) {
                    const courierId = this.results[0].courier_id;

                    return ! this.results.find(r => r.courier_id !== courierId);
                }

                return false;
            },

            weightSum() {
                let sum = 0;
                this.results.forEach(result => {
                    result.order_details.forEach(detail => sum = Math.round((sum + detail.weight * detail.qty) * 100) / 100);
                });

                return sum
            },

            qtySum() {
                let sum = 0;
                this.results.forEach(result => {
                    result.order_details.forEach(detail => sum += detail.qty);
                });

                return sum
            }
        }
    }
</script>

<style lang="scss" scoped>
    .print-only {
        display: none;
    }

    .order-head {
        background-color: #666666;
        color: #ffffff;
        font-size: .9rem;
        padding-top: 0;
        padding-bottom: 0;

        .btn {
            font-size: .8rem;
            &:focus {
                box-shadow: none;
                text-decoration: none;
            }
            &:hover {
                text-decoration: none;
            }
        }
    }

    @media print {
        .print-only {
            display: block;
        }
        .not-print {
            display: none;
        }
        .print-flex {
            display: flex;
        }

        @page {
            margin: 85px 0 100px 0;
        }
        .logo {
            margin: 0 0 1rem;
            width: 300px;
        }

        h5 {
            font-size: 1.8rem;
            margin: 1rem 0;
        }

        .form, .buttons, .card-header {
            display: none;
        }

        .card {
            border: none
        }

        .table {
            border: solid 2px #999999;
            font-size: 1.5rem;
            margin-top: 0 !important;
        }

        .sign,
        .photo {
            height: 300px;

            img {
                max-height: 300px;
            }
        }
    }

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
        position: relative;

        .btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }

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
</style>
