import { createWebHistory, createRouter } from "vue-router";
import store from '../store'
import Manager from "../dashboard/Manager";
import Auth from "../pages/Auth";
import Users from "../pages/Users";
import NotFound from "../pages/404"
import UsersList from "../dashboard/pages/UsersList"
import DashboardHome from "../dashboard/components/DashboardHome";
import ManagerProfile from "../dashboard/pages/ManagerProfile";
import TechnicalCard from "../dashboard/pages/TechnicalCard"
import UserWorks from "../dashboard/pages/UserWorks"
import RecordTask from "../dashboard/pages/RecordTask"

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
            },
            {
                name: 'profile',
                path: 'profile',
                component: ManagerProfile
            },
            {
                name: 'user-work',
                path: 'user-work',
                component: UserWorks
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
