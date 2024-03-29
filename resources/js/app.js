/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Complements
Vue.component('search-input', require('./components/SearchInput.vue').default);
Vue.component('date-picker', require('vuejs-datepicker'));

// Forms
Vue.component('user-form', require('./components/UserForm.vue').default);
Vue.component('user-config-form', require('./components/UserConfigForm.vue').default);
Vue.component('courier-form', require('./components/CourierForm.vue').default);
Vue.component('customer-form', require('./components/CustomerForm.vue').default);
Vue.component('box-form', require('./components/BoxForm.vue').default);
Vue.component('order-form', require('./components/OrderForm.vue').default);
Vue.component('order-report', require('./components/OrderReport.vue').default);
Vue.component('packing-list-form', require('./components/PackingListForm.vue').default);
Vue.component('packing-list-report', require('./components/PackingListReport.vue').default);
Vue.component('packing-list-email', require('./components/PackingListEmail.vue').default);
Vue.component('button-confirmation', require('./components/ButtonConfirmation.vue').default);
Vue.component('signature', require('./components/Signature.vue').default);
Vue.component('transport-form', require('./components/TransportForm.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */



import TranslationPlugin from './plugins/TranslationPlugin'
import AuthPlugin from './plugins/AuthPlugin'
import VeeValidate from 'vee-validate';

Vue.use(TranslationPlugin);
Vue.use(AuthPlugin);
Vue.use(VeeValidate);
Vue.mixin({
    filters: {
        date(d, time = false) {
            const date = new Date(d);
            let response = '';
            let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
            let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
            let year = date.getFullYear();

            response = day + '-' + month + '-' + year;

            if (time) {
                let meridiam = 'AM';
                let hours = date.getHours();
                let minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();

                if (hours > 12) {
                    hours -= 12;
                    meridiam = 'PM';
                }

                hours = (hours < 10) ? '0' + hours : hours;

                response += ' ' + hours + ':' + minutes + ' ' + meridiam;
            }

            return response;
        }
    }
});

const app = new Vue({
    el: '#app',
});
