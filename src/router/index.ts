import { createRouter, createWebHistory } from 'vue-router'

import MainPageView from '../views/MainPageView.vue'
import SignUpView from "../views/SignUpView.vue";
import LandingPageView from "../views/LandingPageView.vue";
import EventCalendarView from "../views/EventCalendarView.vue";
import ChecklistView from "../views/ChecklistView.vue";
import DanceView from '../views/DanceView.vue';
import ImprintView from "../views/ImprintView.vue";
import LoginView from '@/views/LoginView.vue';
import EnterDanceView from "../views/EnterDanceView.vue"

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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
      path: '/events',
      name: 'Eventcalendar',
      component: EventCalendarView
    },
    {
      path: '/checklist',
      name: 'Checklist',
      component: ChecklistView
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
      component: EnterDanceView
    }
  ],
})

export default router;
