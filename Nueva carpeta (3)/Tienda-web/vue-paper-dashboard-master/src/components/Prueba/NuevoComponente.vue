<template>
  <div class="row">
    <div class="col-12">
      <card title="Inventario" subTitle="Lista de productos en inventario">
        <div slot="raw-content" class="grid-container">
          <div v-for="(producto, index) in productos" :key="index" class="grid-item">
            <img :src="producto.foto" alt="Imagen del producto" class="product-image" />
            <h4 class="product-name">{{ producto.nombre }}</h4>
            <p class="product-description">{{ producto.descripcion }}</p>
            <p class="product-price">Precio: Q{{ producto.precio }}</p>
            <p class="product-quantity">Cantidad disponible: {{ producto.existencia }}</p>
            <button class="add-to-cart-btn">
              <i class="fa fa-shopping-cart"></i> Agregar al carrito
            </button>
          </div>
        </div>
      </card>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
data() {
  return {
    productos: [], // Lista de productos
  };
},
mounted() {
  this.fetchInventario();
},
methods: {
  async fetchInventario() {
    try {
      const response = await axios.get("http://localhost:8000/inventario");
      console.log("Datos del inventario:", response.data); // Verifica los datos en la consola
      this.productos = response.data.map(item => ({
        nombre: item.producto ? item.producto.nombre_producto : "Sin nombre", // Asegúrate de que producto exista
        descripcion: item.producto ? item.producto.descripcion : "Sin descripción",
        precio: item.precio,
        existencia: item.existencia, // Cantidad disponible en inventario
        foto: `/${item.fotografia_url}`
      }));
    } catch (error) {
      console.error("Error al obtener los datos de inventario:", error);
    }
  }
}
};
</script>

<style>
.grid-container {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
gap: 30px;
padding: 20px;
}
.grid-item {
border: 1px solid #ddd;
padding: 20px;
text-align: center;
border-radius: 10px;
box-shadow: 0 4px 12px rgba(0,0,0,0.15);
transition: transform 0.2s;
}
.grid-item:hover {
transform: scale(1.05);
}
.product-image {
width: 100%;
height: auto;
max-height: 180px;
object-fit: cover;
border-radius: 10px;
margin-bottom: 10px;
}
.product-name {
font-size: 18px;
font-weight: bold;
color: #333;
margin: 10px 0 5px;
}
.product-description {
font-size: 14px;
color: #666;
margin-bottom: 10px;
}
.product-price {
font-size: 16px;
font-weight: bold;
color: #27ae60;
}
.product-quantity {
font-size: 14px;
color: #888;
margin-bottom: 15px;
}
.add-to-cart-btn {
background-color: #3498db;
color: white;
border: none;
padding: 10px;
border-radius: 5px;
cursor: pointer;
font-size: 16px;
}
.add-to-cart-btn i {
margin-right: 5px;
}
</style>
