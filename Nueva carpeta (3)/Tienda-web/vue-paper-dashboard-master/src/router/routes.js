import DashboardLayout from "@/layout/dashboard/DashboardLayout.vue";
import NotFound from "@/pages/NotFoundPage.vue";
import Login from "@/components/Prueba/LoginComponent.vue";
import AdminDashboardLayout from "@/layout/dashboard/AdminDashboardLayout.vue";
import GerenteDashboardLayout from "@/layout/dashboard/GerenteDashboardLayout.vue";
import ClienteDashboardLayout from "@/layout/dashboard/ClienteDashboardLayout.vue";
import CrearCuenta from "@/components/Clientes/CrearCuenta.vue";

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
    path: "/crear-cuenta",
    name: "crear-cuenta",
    component: CrearCuenta,
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
      { path: "Descuentos", name: "Administrar Descuentos", component: () => import("@/components/Prueba/Descuentos.vue") },
      { path: "InventarioAdministrador", name: "Administrar Inventario", component: () => import("@/components/Prueba/InventarioAdmin.vue") },
      { path: "TrasladosAdministrador", name: "Administrar Traslados", component: () => import("@/components/Prueba/TrasladosAdmin.vue")},
    ],
  },
  {
    path: "/gerente",
    component: GerenteDashboardLayout,
    redirect: "/gerente/notifications",
    children: [
      { path: "stats", name: "admin-stats", component: () => import("@/pages/UserProfile.vue") },
      { path: "notifications", name: "gerente-notifications", component: () => import("@/pages/Notifications.vue") },
      { path: "stats", name: "gerente-stats", component: () => import("@/pages/UserProfile.vue") },
      { path: "Roles", name: "Administrar Accesos (roles)", component: () => import("@/components/Gerente/AdministrarRol.vue") },
      { path: "Usuarios", name: "Administrar Usarios", component: () => import("@/components/Gerente/AdministrarUsuarios.vue") },
      { path: "Reportes", name: "Administrar Reportes", component: () => import("@/components/Gerente/Reportes.vue") },
      { path: "ReporteProducto", name: "Reportes Productos", component: () => import("@/components/Gerente/ReporteProducto.vue") },
      { path: "BajoInventario", name: "Reportes Inventario Bajo", component: () => import("@/components/Gerente/ReporteBajoInventario.vue") },
      { path: "ReporteGrafica", name: "Reportes Grafica Productos", component: () => import("@/components/Gerente/ReporteGrafica.vue") },
      { path: "Reportes2", name: "Administrar Reportes 2", component: () => import("@/components/Gerente/ReporteDos.vue") },
      { path: "ReportesClientes", name: "Clientes Reportes", component: () => import("@/components/Gerente/ReporteClientes.vue") },
      { path: "ReportesFechas", name: "Reportes por Fechas", component: () => import("@/components/Gerente/ReporteFechas.vue") },
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
      { path: "Ubicacion", name: "Ubicacion Actual", component: () => import("@/components/Clientes/Ubicacion.vue") },
      { path: "resumen-compra", name: "Resumen Compra", component: () => import("@/components/Clientes/ResumenCompra.vue") }, // Nueva ruta
      { path: "Camcelar-compra", name: "Cancelar Compra", component: () => import("@/components/Clientes/CancelarPago.vue") },
    ],
  },  
  { path: "*", component: NotFound },
];



export default routes;

