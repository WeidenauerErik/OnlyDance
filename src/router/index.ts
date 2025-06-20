import { createRouter, createWebHistory } from 'vue-router'

import MainPageView from '../views/MainPageView.vue'
import SignUpView from "../views/SignUpView.vue";
import LandingPageView from "../views/LandingPageView.vue";
import ChecklistView from "../views/ChecklistView.vue";
import DanceView from '../views/DanceView.vue';
import ImprintView from "../views/ImprintView.vue";
import LoginView from '@/views/LoginView.vue';
import EnterDanceView from "../views/EnterDanceView.vue"
import ChecklistSelectionView from "@/views/ChecklistSelectionView.vue";
import {useAuthStore} from "@/stores/auth.ts";
import AddChecklistView from "@/views/AddChecklistView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    console.log('ScrollBehavior triggered', { to, from }); // Debugging
    return { top: 0, behavior: 'smooth' }; // Optional: Add smooth scrolling
  },
  routes: [
    {
      path: '/mainpage',
      name: 'MainPage',
      component: MainPageView,
    },
    {
      path: '/signup',
      name: 'SignUp',
      component: SignUpView,
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginView
    },
    {
      path: '/',
      name: 'landingpage',
      component: LandingPageView
    },

    {
      path: '/checklist/:id',
      name: 'Checklist',
      component: ChecklistView,
      props: true

    },
    {
      path: '/danceview/:id',
      name: 'DanceView',
      component: DanceView,
      props: true
    },
    {
      path: '/imprint',
      name: 'Imprint',
      component: ImprintView
    },
    {
      path: '/enterDance',
      name: 'enterDance',
      component: EnterDanceView,
      meta: {requiresAdmin: true}

    },
    {
      path: '/checklists',
      name: 'checklistSelection',
      component: ChecklistSelectionView,
      meta: {requiresAuth: true}
    },
    {
      path: '/checklist/new',
      name: 'CreateChecklist',
      component: AddChecklistView,
      meta: {requiresAuth: true}
    },
  ],
})
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.checkAdmin();
  authStore.checkAuth();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  }
  else if(to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/mainpage');

  }else {
    next();
  }
});
export default router;