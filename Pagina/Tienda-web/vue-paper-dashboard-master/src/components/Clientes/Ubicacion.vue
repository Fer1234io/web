<template>
    <div class="container">
      <h1>Encuentra la Tienda Más Cercana</h1>
      <!-- Botón para obtener la ubicación -->
      <button @click="getUserLocation" class="btn">Obtener Ubicación</button>
  
      <!-- Mostrar ubicación del cliente -->
      <div v-if="userPosition" class="location">
        <p>Tu ubicación: Lat {{ userPosition.lat }}, Lng {{ userPosition.lng }}</p>
      </div>
  
      <!-- Mostrar lista de tiendas -->
      <div v-if="tiendas.length">
        <h2>Tiendas Cercanas</h2>
        <ul>
          <li v-for="tienda in tiendas" :key="tienda.id_tienda">
            {{ tienda.nombre_tienda }} - Distancia: {{ tienda.distancia.toFixed(2) }} km
          </li>
        </ul>
      </div>
  
      <!-- Mostrar la tienda más cercana -->
      <div v-if="tiendaCercana" class="closest-store">
        <h2>Tienda Más Cercana</h2>
        <p>{{ tiendaCercana.nombre_tienda }} ({{ tiendaCercana.distancia.toFixed(2) }} km)</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        userPosition: null, // Coordenadas del usuario
        tiendas: [], // Tiendas cercanas
        tiendaCercana: null, // Tienda más cercana
      };
    },
    methods: {
      // Obtener ubicación del usuario
      getUserLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              this.userPosition = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
              };
              this.fetchClosestStore(); // Obtener las tiendas cercanas
            },
            (error) => {
              console.error("Error obteniendo la ubicación:", error);
              alert("No se pudo obtener tu ubicación.");
            }
          );
        } else {
          alert("Geolocalización no soportada en este navegador.");
        }
      },
  
      // Obtener la tienda más cercana desde el backend
      fetchClosestStore() {
  if (this.userPosition) {
    axios
      .post("http://127.0.0.1:8000/tienda-cercana", this.userPosition)
      .then((response) => {
        this.tiendas = response.data.tiendas; // Tiendas ordenadas por distancia
        this.tiendaCercana = response.data.tiendaCercana; // Tienda más cercana

        console.log("Tiendas cercanas:", this.tiendas);
        console.log("Tienda más cercana:", this.tiendaCercana);
      })
      .catch((error) => {
        console.error("Error al obtener la tienda más cercana:", error);
        alert("Ocurrió un error al buscar la tienda más cercana.");
      });
  }
}

    },
  };
  </script>
  
  <style scoped>
  .container {
    font-family: Arial, sans-serif;
    max-width: 600px;
    margin: 20px auto;
    text-align: center;
  }
  
  .btn {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn:hover {
    background-color: #0056b3;
  }
  
  .location {
    margin: 20px 0;
    font-size: 16px;
    color: #333;
  }
  
  ul {
    list-style: none;
    padding: 0;
  }
  
  li {
    margin: 10px 0;
    font-size: 16px;
  }
  
  .closest-store {
    margin-top: 20px;
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
  }
  </style>
  