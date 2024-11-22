<template>
  <div class="menu-reportes">
    <h1>Menú de Reportes</h1>
    <p>Seleccione un reporte para ver los detalles.</p>
    <div class="botones-container">
      <button @click="irA4">Top 100 Productos Más Vendidos</button>
      <button @click="irA5">Productos con Existencia Menor a 10</button>
      <button @click="irA6">Gráfica de Productos Más Vendidos por Mes</button>
      <button @click="irA">Reporte de Productos Más Vendidos</button>
      <button @click="irA2">Reporte de Clientes Frecuentes</button>
      <button @click="irA3">Reporte de Compras por Rango de Fecha</button>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    irA4() {
      this.$router.push("/gerente/ReporteProducto");
    },
    irA5() {
      this.$router.push("/gerente/BajoInventario");
    },
    irA6() {
      this.$router.push("/gerente/ReporteGrafica");
    },
    irA() {
      this.$router.push("/gerente/Reportes2");
    },
    irA2() {
      this.$router.push("/gerente/ReportesClientes");
    },
    irA3() {
      this.$router.push("/gerente/ReportesFechas");
    },
  },
};
</script>

<style scoped>
/* Contenedor principal */
.menu-reportes {
  text-align: center;
  padding: 20px;
  font-family: "Roboto", Arial, sans-serif;
  background: linear-gradient(135deg, #f0f8ff, #e6f7ff);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* Título */
h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
  color: #333;
}

p {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 30px;
}

/* Botones */
.botones-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  width: 100%;
  max-width: 800px;
}

.botones-container button {
  flex: 1 1 calc(33.333% - 20px);
  padding: 15px 20px;
  font-size: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
  min-width: 120px;
}

.botones-container button:hover {
  background-color: #0056b3;
  transform: translateY(-5px);
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.botones-container button:active {
  background-color: #003f8a;
  transform: translateY(0);
}

/* Responsividad */
@media (max-width: 768px) {
  .botones-container button {
    flex: 1 1 calc(50% - 20px);
  }
}

@media (max-width: 480px) {
  .botones-container button {
    flex: 1 1 100%;
  }

  h1 {
    font-size: 2rem;
  }

  p {
    font-size: 1rem;
  }
}
</style>
