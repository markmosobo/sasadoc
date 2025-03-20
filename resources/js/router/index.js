import {  createRouter, createWebHistory  } from 'vue-router';
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
// import ResetPassword from '../views/ResetPassword.vue'
import Register from '../views/Register.vue'

function guardMyroute(to, from, next)
{
 var isAuthenticated= false;
//this is just an example. You will have to find a better or 
// centralised way to handle you localstorage data handling 
if(localStorage.getItem('user'))
  isAuthenticated = true;
 else
  isAuthenticated= false;
 if(isAuthenticated) 
 {
  next(); // allow to enter route
 } 
 else
 {
  next('/login'); // go to '/login';
 }
}

const routes = [
    {
        path:'/',
        name: 'index',
        component: Login
    },

    {
        path:'/home',
        name: 'home',
        component: Home
    },
    // {
    //     path:'/resetpassword',
    //     name: 'resetpassword',
    //     component: ResetPassword
    // },
    {
        path:'/register',
        name: 'register',
        component: Register
    },
                              
];

const router = createRouter({
    history: createWebHistory('/'),
    routes
});



export default router;