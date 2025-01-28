import './bootstrap';
import router from "./router.js";
import { createApp } from "vue";

import App from "../views/App.vue";


createApp(App).use(router).mount("#app");
