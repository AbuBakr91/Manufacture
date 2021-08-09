import { createWebHistory, createRouter } from "vue-router";
import store from '../store'
import Manager from "../dashboard/Manager";
import Auth from "../pages/Auth";
import Users from "../pages/Users";
import NotFound from "../pages/404"
import UsersList from "../dashboard/pages/UsersList"
import ManagerProfile from "../dashboard/pages/ManagerProfile";
import UserTask from "../dashboard/pages/UserTask";
import TechnicalCard from "../dashboard/pages/TechnicalCard"
import UserWorks from "../dashboard/pages/UserWorks"
import RecordTask from "../dashboard/pages/RecordTask"
import UserEdit from "../dashboard/pages/UserEdit"
import Logs from "../dashboard/pages/Logs";
import Manufacture from "../dashboard/pages/Manufacture";

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
                component: UserTask
            },
            {
                name: 'users',
                path: 'users',
                component: UsersList
            },
            {
                name: 'user',
                path: 'user/:id',
                component: UserEdit
            },
            {
                name: 'profile',
                path: 'profile',
                component: ManagerProfile
            },
            {
                name: 'manufacture',
                path: 'manufacture',
                component: Manufacture
            },
            {
                name: 'logs',
                path: 'logs',
                component: Logs
            },
            {
                name: 'list-map',
                path: 'list-map',
                component: TechnicalCard
            },
            {
                name: 'journal',
                path: 'journal',
                component: RecordTask
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
    linkActiveClass: "active"
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
