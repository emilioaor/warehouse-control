const TranslationPlugin = {
    install(Vue, options) {

        Vue.mixin({
            data() {
                return {
                    translations: []
                }
            },

            methods: {
                t(value, params = []) {
                    this.getTranslations();

                    const deep = value.split('.');
                    let response = {...this.laravelTranslations};

                    for (let i of deep) {
                        const current = response[i];

                        if (current) {
                            if (typeof current === 'object') {
                                response = {...current};
                            } else {
                                response = current
                            }
                        } else {
                            return '';
                        }
                    }

                    if (typeof response === 'string') {
                        for (let i in params) {

                            let value = params[i];

                            if (
                                i === 'attribute' &&
                                this.laravelTranslations[deep[0]].attributes &&
                                this.laravelTranslations[deep[0]].attributes[value]
                            ) {
                                value = this.laravelTranslations[deep[0]].attributes[value];
                            }

                            response = response.replace(':' + i, value)
                        }

                        return response;
                    }

                    return '';
                },

                getTranslations() {
                    if (typeof window.TranslationPlugin === 'undefined') {
                        window.TranslationPlugin = {
                            translations: []
                        };

                        axios.get('/warehouse/translation').then(res => {
                            window.TranslationPlugin.translations = res.data;
                            this.translations = res.data;
                        });
                    } else {
                        this.translations = window.TranslationPlugin.translations;
                    }
                }
            },

            computed: {
                laravelTranslations() {
                    return this.translations;
                }
            }
        })
    }
};

export default TranslationPlugin;