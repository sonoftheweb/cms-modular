import Login from '../components/pages/Login'
import Home from '../components/pages/Home'
import Subscription from '../components/pages/Subscription'
import Me from '../components/pages/account/Me'
import ManageSubscription from '../components/pages/account/ManageSubscription'
import Users from '../components/pages/account/Users'
import Register from '../components/pages/register/Register'

export default [
  {
    path: '/',
    name: 'login',
    component: Login,
    meta: {displayableName: 'Login'}
  },
  {
    path: '/registration',
    name: 'registration',
    component: Register,
    meta: {displayableName: 'Registration',}
  },
  {
    path: '/subscription',
    name: 'subscription',
    component: Subscription,
    meta: {displayableName: 'Subscription',}
  },
  {
    path: '/home',
    name: 'home',
    component: Home,
    meta: {requiresAuth: true, icon: 'mdi-view-dashboard-outline', displayableName: 'Dashboard', inMenu: true},
    children: [
      {
        name: 'home.task-dash',
        path: 'tasks',
        component: Home,
        meta: {icon: 'mdi-calendar-check-outline', displayableName: 'Task Dashboard', inMenu: true},
      }
    ]
  },
  {
    path: '/contracts',
    name: 'contracts',
    component: Login,
    meta: {requiresAuth: true, icon: 'mdi-file-document-edit-outline', displayableName: 'Contracts', inMenu: true}
  },
  {
    path: '/tasks',
    name: 'tasks',
    component: Login,
    meta: {requiresAuth: true, icon: 'mdi-calendar-clock', displayableName: 'Tasks', inMenu: true}
  },
  {
    path: '/users',
    name: 'users',
    component: Users,
    meta: {requiresAuth: true, icon: 'mdi-account-check-outline', displayableName: 'Users', inMenu: true}
  },
  {
    path: '/manage-subscription',
    name: 'manage-subscription',
    component: ManageSubscription,
    meta: {requiresAuth: true, displayableName: 'Manage Subscription'}
  },
  {
    path: '/me',
    name: 'me',
    component: Me,
    meta: {requiresAuth: true, displayableName: 'My Profile'},
  },
  {
    path: '/account',
    name: 'account',
    component: Me,
    meta: {requiresAuth: true, displayableName: 'Account'}
  },
]