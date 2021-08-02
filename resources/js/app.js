require('./bootstrap');
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import PrimeVue from 'primevue/config'
import loader from "vue-ui-preloader"
import './theme.css'
import 'primeicons/primeicons.css'
import 'primevue/resources/themes/saga-blue/theme.css'
import 'primevue/resources/primevue.min.css'

createApp(App).use(loader).use(PrimeVue).use(store).use(router).mount('#app')
