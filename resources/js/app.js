import { createApp } from 'vue';
import App from './components/App.vue';
import VueApexCharts from "vue3-apexcharts";

const app = createApp(App);
app.component("apexchart", VueApexCharts);
app.mount('#app');
