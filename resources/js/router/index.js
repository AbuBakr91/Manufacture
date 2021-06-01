import { createWebHistory, createRouter } from "vue-router";
import store from '../store'
import Manager from "../pages/Manager";
import Auth from "../pages/Auth";
import Users from "../pages/Users";
import NotFound from "../pages/404"
import UsersList from "../pages/dashboard/UsersList"
import DashboardHome from "../pages/dashboard/DashboardHome";

const routes = [
    {
        path: "/",
        name: "Users",
        component: Users,
        meta: {
            layout: 'main',
            auth: true
        }
    },
    {
        path: "/manager",
        name: "Manager",
        component: Manager,
        meta: {
            layout: 'main',
            auth: true
        },
        children: [
            {
                name: 'home',
                path: '',
                component: DashboardHome
            },
            {
                name: 'users',
                path: 'users',
                component: UsersList
            }
        ]
    },
    {
        path: "/login",
        name: "Auth",
        component: Auth,
        meta: {
            layout: 'auth',
            auth: false
        }
    },
    {
        path: "/:catchAll(.*)",
        name: "NotFound",
        component: NotFound,
        meta: {
            layout: 'main',
            auth: false
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const requireAuth = to.meta.auth

    if (requireAuth && store.getters['auth/isAuthenticated']) {
        next()
    } else if (requireAuth && !store.getters['auth/isAuthenticated']) {
        next('/login?message=auth')
    } else {
        next()
    }
})

export default router;
