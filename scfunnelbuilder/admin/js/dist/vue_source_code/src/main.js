import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "@vue-flow/core/dist/style.css";
import "@vue-flow/core/dist/theme-default.css";
import "@vueform/multiselect/themes/default.css";
import "vue3-toastify/dist/index.css";
import "./style.scss";

import { createPinia } from "pinia";
import { getFunnelInit } from "@/utils/funnel-init";
import { initVeeValidate } from "@/utils/ValidationConfig";

import Vue3Toastify from "vue3-toastify";
import App from "./App.vue";

const FunnelId = getFunnelInit();
initVeeValidate();
const pinia = createPinia();
const app = FunnelId ? createApp(App, { funnelId: FunnelId }) : "";

app.use(pinia);
app.use(Vue3Toastify);
app.mount("#scfunnel-root");
import "bootstrap/dist/js/bootstrap.js";
