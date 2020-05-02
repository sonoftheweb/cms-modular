import Login from '../components/pages/Login'

export default [
    {
        path: '/',
        name: 'login',
        component: Login,
        meta: { icon: 'mdi-view-dashboard-outline' }
    },
    {
        path: '/',
        name: 'contracts',
        component: Login,
        meta: { icon: 'mdi-file-document-edit-outline' }
    },
    {
        path: '/',
        name: 'tasks',
        component: Login,
        meta: { icon: 'mdi-calendar-clock' }
    },
    {
        path: '/',
        name: 'users',
        component: Login,
        meta: { icon: 'mdi-account-check-outline' }
    },
]