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
  const publicPages = ["/login"];
  const authRequired = !publicPages.includes(to.path);
  const userRole = localStorage.getItem("role");

  if (authRequired && !userRole) {
    return next("/login");
  }

  if (to.path === "/login" && userRole) {
    if (userRole === "Administrador") {
      return next("/admin/dashboard");
    } else if (userRole === "Gerente") {
      return next("/gerente/notifications");
    } else {
      return next("/login"); // O redirige a otra ruta predeterminada para otros roles
    }
  }

  next();
});

export default router;
