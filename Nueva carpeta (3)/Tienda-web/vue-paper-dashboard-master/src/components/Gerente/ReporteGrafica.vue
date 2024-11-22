<template>
    <div class="productos-mas-vendidos-por-mes">
      <!-- Botón para regresar -->
      <div class="back-button">
        <button @click="regresar">Regresar</button>
      </div>
  
      <h1>Gráfica de Productos Más Vendidos por Mes</h1>
  
      <!-- Botón para generar la gráfica -->
      <div class="reporte-form">
        <h2>Generar Gráfica</h2>
        <button @click="generarGrafica">Generar Gráfica</button>
      </div>
  
      <!-- Contenedor de la gráfica -->
      <div class="grafica-container" v-if="chartData">
        <h2>Productos Más Vendidos por Mes</h2>
        <canvas id="graficaProductosVendidosPorMes"></canvas>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  import Chart from "chart.js/auto";
  
  export default {
    data() {
      return {
        chartData: null,
        chartInstance: null,
      };
    },
    methods: {
      regresar() {
        this.$router.push("/gerente/Reportes");
      },
      async generarGrafica() {
        try {
          const response = await axios.post(
            "http://127.0.0.1:8000/reportes/productos-por-mes"
          );
          this.chartData = response.data.data;
  
          if (!this.chartData || this.chartData.length === 0) {
            alert("No hay datos para mostrar en la gráfica.");
            return;
          }
  
          console.log("Datos recibidos para la gráfica:", this.chartData);
  
          // Preparar los datos para la gráfica
          const meses = [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
          ];
  
          // Agrupar datos por producto y mes
          const labels = [...new Set(this.chartData.map((item) => meses[item.mes - 1]))];
          const datasetsMap = {};
  
          this.chartData.forEach((item) => {
            if (!datasetsMap[item.nombre_producto]) {
              datasetsMap[item.nombre_producto] = Array(labels.length).fill(0);
            }
            const mesIndex = labels.indexOf(meses[item.mes - 1]);
            datasetsMap[item.nombre_producto][mesIndex] = item.total_vendido;
          });
  
          const datasets = Object.entries(datasetsMap).map(([nombre, data]) => ({
            label: nombre,
            data: data,
            borderWidth: 1,
            borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
            backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}33`,
          }));
  
          // Destruir la gráfica previa si existe
          if (this.chartInstance) {
            this.chartInstance.destroy();
          }
  
          // Renderizar la gráfica
          this.$nextTick(() => {
            const canvas = document.getElementById("graficaProductosVendidosPorMes");
            if (canvas) {
              const ctx = canvas.getContext("2d");
              this.chartInstance = new Chart(ctx, {
                type: "bar",
                data: {
                  labels: labels,
                  datasets: datasets,
                },
                options: {
                  responsive: true,
                  plugins: {
                    legend: { position: "top" },
                    title: { display: true, text: "Productos Más Vendidos por Mes" },
                  },
                },
              });
            } else {
              console.error("No se pudo encontrar el canvas para renderizar la gráfica.");
              alert("Hubo un problema al renderizar la gráfica.");
            }
          });
        } catch (error) {
          console.error("Error al generar la gráfica:", error);
          alert("Hubo un error al generar la gráfica.");
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .productos-mas-vendidos-por-mes {
    padding: 20px;
    font-family: "Roboto", Arial, sans-serif;
    background: linear-gradient(135deg, #f5f7fa, #e4f0fb);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .back-button {
    margin-bottom: 20px;
    width: 100%;
    display: flex;
    justify-content: flex-start;
  }
  
  .back-button button {
    padding: 10px 15px;
    background-color: #ff6666;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
  }
  
  .back-button button:hover {
    background-color: #cc0000;
  }
  
  h1 {
    font-size: 2rem;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
  }
  
  .reporte-form {
    margin-bottom: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  
  .reporte-form button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
  }
  
  .reporte-form button:hover {
    background-color: #0056b3;
  }
  
  .grafica-container {
    margin-top: 20px;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
  }
  
  #graficaProductosVendidosPorMes {
    max-width: 100%;
    height: auto;
  }
  
  /* Responsividad */
  @media (max-width: 768px) {
    .reporte-form,
    .grafica-container {
      width: 90%;
    }
  }
  </style>
  