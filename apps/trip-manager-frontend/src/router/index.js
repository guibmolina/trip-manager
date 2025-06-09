import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginLayout from '@/layouts/LoginLayout.vue'
import LoginPage from '@/views/LoginPage.vue'
import { useAuthStore } from '@/stores/auth'
import MainLayout from '@/layouts/MainLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginLayout,
      children: [
        {
          path: '',
          name: 'Login',
          component: LoginPage,
        },
      ],
    },
    {
      path: '/',
      component: MainLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: HomeView,
          meta: { requiresAuth: true },
        },
      ],
    },
  ],
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth) {
    if (authStore.isAuthenticated) {
      if (!authStore.user) {
        await authStore.fetchUser()
      }
      next()
    } else {
      next('/login')
    }
  } else {
    next()
  }
})

export default router
