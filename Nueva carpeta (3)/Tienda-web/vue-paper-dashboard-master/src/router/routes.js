import DashboardLayout from "@/layout/dashboard/DashboardLayout.vue";
import NotFound from "@/pages/NotFoundPage.vue";
import Login from "@/components/Prueba/LoginComponent.vue";
import Dashboard from "@/pages/Dashboard.vue";
import UserProfile from "@/pages/UserProfile.vue";
import Notifications from "@/pages/Notifications.vue";
import Icons from "@/pages/Icons.vue";
import Maps from "@/pages/Maps.vue";
import Typography from "@/pages/Typography.vue";
import TableList from "@/pages/TableList.vue";
import InventarioAdmin from '@/components/Prueba/InventarioAdmin.vue';
import ProductosAdmin from '@/components/Prueba/ProductosAdmin.vue';
import AdminDashboardLayout from "@/layout/dashboard/AdminDashboardLayout.vue";
import GerenteDashboardLayout from "@/layout/dashboard/GerenteDashboardLayout.vue";
import ClienteDashboardLayout from "@/layout/dashboard/ClienteDashboardLayout.vue";

const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "login",
    component: Login,
  },
  {
    path: "/admin",
    component: AdminDashboardLayout,
    redirect: "/admin/dashboard",
    children: [
      { path: "dashboard", name: "admin-dashboard", component: () => import("@/pages/Dashboard.vue") },
      { path: "stats", name: "admin-stats", component: () => import("@/pages/UserProfile.vue") },
      { path: "table-list", name: "admin-table-list", component: () => import("@/pages/TableList.vue") },
      { path: "ProductosAdministrador", name: "Administrar Productos", component: () => import("@/components/Prueba/ProductosAdmin.vue") },
      { path: "InventarioAdministrador", name: "Administrar Inventario", component: () => import("@/components/Prueba/InventarioAdmin.vue") },
    ],
  },
  {
    path: "/gerente",
    component: GerenteDashboardLayout,
    redirect: "/gerente/notifications",
    children: [
      { path: "notifications", name: "gerente-notifications", component: () => import("@/pages/Notifications.vue") },
      { path: "stats", name: "gerente-stats", component: () => import("@/pages/UserProfile.vue") },
      { path: "GerenteUsuario", name: "Administrar Usuarios", component: () => import("@/components/Prueba/GerenteUsuario.vue") },
    ],
  },
  {
    path: "/cliente",
    component: ClienteDashboardLayout,
    redirect: "/cliente/stats",
    children: [
      { path: "stats", name: "cliente-stats", component: () => import("@/pages/UserProfile.vue") },
      { path: "historial", name: "cliente-historial", component: () => import("@/pages/TableList.vue") },
      { path: "NuevoComponente", name: "Productos", component: () => import("@/components/Prueba/NuevoComponente.vue") },
    ],
  },
  { path: "*", component: NotFound },
];



export default routes;
