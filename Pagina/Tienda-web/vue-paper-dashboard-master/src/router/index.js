// src/router/index.js
import Vue from "vue";
import VueRouter from "vue-router";
import routes from "./routes";

Vue.use(VueRouter);

const router = new VueRouter({
  mode: "hash",
  routes,
  linkActiveClass: "active",
});

router.beforeEach((to, from, next) => {
  const publicPages = ["/login", "/crear-cuenta"]; 
  const authRequired = !publicPages.includes(to.path);
  const userData = JSON.parse(localStorage.getItem("userData")); 
  
  if (authRequired && !userData) {
    return next("/login");
  }

  // Si está en la página de login y ya está autenticado redirige según el rol
  if (to.path === "/login" && userData) {
    switch (userData.rol) {
      case "Administrador":
        return next("/admin/stats");
      case "Gerente":
        return next("/gerente/notifications");
      case "Cliente":
        return next("/cliente/stats");
      default:
        return next("/general/nuevo-componente");
    }
  }

  // Permitir el acceso a la ruta solicitada
  next();
});

export default router;
