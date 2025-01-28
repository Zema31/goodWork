import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/",
        component: () => import("../views/HomeRoute.vue"),
    },
    {
        path: "/test",
        component: () => import("../views/TestRoute.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});