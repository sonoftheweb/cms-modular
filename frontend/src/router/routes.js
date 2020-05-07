import Login from '../components/pages/Login'
import Home from '../components/pages/Home'
import Subscription from '../components/pages/Subscription'

export default [
    {
        path: '/',
        name: 'login',
        component: Login,
        meta: { displayableName: 'Login' }
    },
    {
        path: '/subscription',
        name: 'subscription',
        component: Subscription,
        meta: { displayableName: 'Subscription', }
    },
    {
        path: '/home',
        name: 'home',
        component: Home,
        meta: { icon: 'mdi-view-dashboard-outline', displayableName: 'Dashboard', inMenu: true }
    },
    {
        path: '/contracts',
        name: 'contracts',
        component: Login,
        meta: { icon: 'mdi-file-document-edit-outline', displayableName: 'Contracts', inMenu: true }
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: Login,
        meta: { icon: 'mdi-calendar-clock', displayableName: 'Tasks', inMenu: true }
    },
    {
        path: '/users',
        name: 'users',
        component: Login,
        meta: { icon: 'mdi-account-check-outline', displayableName: 'Users', inMenu: true }
    },
]