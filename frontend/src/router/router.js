import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'

Vue.use(VueRouter)
const router = new VueRouter({
    routes: routes,
    mode: 'history'
})

router.initialize = async () => {
    router.beforeEach(async (to, from, next) => {
        // check if user data is populated (requires store)
        // check if token exists (requires store)
        // check if user belongs to an instance (requires store)
        return next();
    })
}

export default router