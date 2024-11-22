<template>
  <card class="card" :title="title">
    <div>
      <ul class="list-unstyled team-members">
        <li v-for="member in members" :key="member.id_usuario">
          <div class="row">
            <div class="col-3">
              <div class="avatar">
                <img
                  :src="member.image || defaultImage"
                  alt="Circle Image"
                  class="rounded img-fluid"
                />
              </div>
            </div>
            <div class="col-6">
              <strong>{{ member.nombre }} {{ member.apellido }}</strong>
              <br />
              <small>Teléfono: {{ member.numero_telefono }}</small>
              <br />
              <small>Dirección: {{ member.direccion }}</small>
              <br />
              <span :class="getStatusClass(member.status)">
                <small>{{ member.status }}</small>
              </span>
            </div>
            <div class="col-3 text-end">
              <button class="btn btn-success btn-sm">
                <i class="fa fa-envelope"></i>
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </card>
</template>

<script>
export default {
  props: {
    members: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      title: "Team Members",
      defaultImage: "https://via.placeholder.com/150", // Imagen predeterminada
    };
  },
  methods: {
    getStatusClass(status) {
      switch (status) {
        case "Offline":
          return "text-muted";
        case "Available":
          return "text-success";
        case "Busy":
          return "text-danger";
        default:
          return "text-muted";
      }
    },
  },
};
</script>

<style scoped>
.avatar img {
  width: 50px;
  height: 50px;
  object-fit: cover;
}
</style>
