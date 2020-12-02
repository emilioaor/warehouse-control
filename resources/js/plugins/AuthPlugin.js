const AuthPlugin = {
  install(Vue, options) {

      Vue.mixin({
          data() {
              return {
                  user: null
              }
          },

          methods: {
              getAuthUser() {
                  if (typeof window.AuthPlugin === 'undefined') {
                      window.AuthPlugin = {
                          user: null
                      };

                      axios.get('/warehouse/user/auth').then(res => {
                          window.AuthPlugin.user = res.data.user;
                          this.user = res.data.user;
                      });
                  } else {
                      this.user = window.AuthPlugin.user;
                  }
              },

              authUser() {
                  this.getAuthUser();

                  return this.user
              }
          }
      })
  }
};

export default AuthPlugin;