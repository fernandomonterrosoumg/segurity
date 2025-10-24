var apiEndpoint =
  "/EMRApp/login/function/ajax_functions.php?FUNC=";
var Headers = {
  json: { header: "Content-Type", value: "application/json" },
  form: { header: "Content-Type", value: "application/x-www-form-urlencoded" },
};

const { createApp } = Vue;

const app = createApp({
  directives: {
    upper: {
      update(el) {
        el.value = el.value.toUpperCase();
      },
    },
  },
  mounted: function () { },
  data() {
    return {
      required: true,
      dataRegistro: {
        nombre1: "",
        nombre2: "",
        apellido1: "",
        apellido2: "",
        mail: "",
        password: "",
        confirmPassword: "",
      },
      dataLogin: {
        mail: "",
        password: "",
      },
    };
  },
  watch: {},
  methods: {
    loginConGoogle: function () {
      window.location.href = '/EMRApp/login/function/ajax_functions.php?FUNC=oauth_google_init';
    },
    validarFormulario: function (redirect) {
      // VALIDACIONES DE CAMPOS REQUERIDOS
      var forms = document.querySelectorAll(".needs-validation");
      const vm = this;
      // Bucle sobre ellos y evitar la submition
      Array.prototype.slice.call(forms).forEach(function (form) {
        event.stopPropagation();

        //Agrega la clase que da estilos a los campos valios e invalidos
        form.classList.add("was-validated");
        console.log(form.checkValidity());

        if (form.checkValidity()) {
          event.preventDefault();
          switch (redirect) {
            case "registro":
              vm.guardarRegistro();
              break;
            case "iniciarSesion":
              vm.iniciarSesion();
              break;
            default:
              break;
          }
        }
      });
    },
    guardarRegistro: function () {
      if (this.dataRegistro.password === this.dataRegistro.confirmPassword) {
        var raw = JSON.stringify({
          dataRegistro: this.dataRegistro,
        });

        var requestOptions = {
          method: "POST",
          headers: { "Content-Type": "application/json; charset=utf-8" },
          body: raw,
          redirect: "follow",
        };

        http.postJson(apiEndpoint + "guardarRegistro", requestOptions)
          .then((response) => {
            return response.json();
          })
          .then((respuesta) => {
            if (respuesta.estado) {
              this.modalSuccess(respuesta.desc).then(() => {
                location.reload(true);
              });
            } else {
              this.modalError(respuesta.desc);
            }
          })
          .catch((error) => {
            this.modalErrorApi(error);
            this.mostrarAnimacion = false;
          });
      } else {
        this.modalError("Las contraseñas no coinciden");
      }
    },
    iniciarSesion: function () {
      var raw = JSON.stringify({
        dataLogin: this.dataLogin,
      });

      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        body: raw,
        redirect: "follow",
      };

      http.postJson(apiEndpoint + "iniciarSesion", requestOptions)
        .then((response) => {
          return response.json();
        })
        .then((respuesta) => {
          if (respuesta.estado) {
            window.location.href = '/EMRApp';
          } else {
            this.modalError(respuesta.desc);
          }
        })
        .catch((error) => {
          this.modalErrorApi(error);
          this.mostrarAnimacion = false;
        });
    },


    cambioValidation: function () {
      if (this.required) {
        this.required = !this.required;
        console.log(this.required);
      }
    },
    modalErrorApi: function (error) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: `Error: ${error}`,
        footer: null,
      });
    },
    //Modal de error, recibe como parametro el mensaje de error a mostrar
    modalError: function (error) {
      swal.fire({
        title: "Oops...",
        html: `Error: ${error}`,
        icon: "error",
        showConfirmButton: false,
        timer: 5000,
        position: "top-end",
        toast: true,
        width: "auto",
      });
    },
    //Modal de realizado con exito, recibe como parametro el mensaje de exito a mostrar
    modalSuccess: function (mensaje) {
      return swal.fire({
        title: mensaje,
        icon: "success",
        showConfirmButton: false,
        timer: 3000,
        position: "top-end",
        toast: true,
        width: "17.8rem",
      });
    },
  },
});
app.mount("#app");

$(document).ready(function () {
  var panelOne = $(".form-panel.two").height(),
    panelTwo = $(".form-panel.two")[0].scrollHeight;

  $(".form-panel.two")
    .not(".form-panel.two.active")
    .on("click", function (e) {
      e.preventDefault();

      $(".form-toggle").addClass("visible");
      $(".form-panel.one").addClass("hidden");
      $(".form-panel.two").addClass("active");
      $(".form").animate(
        {
          height: panelTwo,
        },
        200
      );
    });

  $(".form-toggle").on("click", function (e) {
    e.preventDefault();
    $(this).removeClass("visible");
    $(".form-panel.one").removeClass("hidden");
    $(".form-panel.two").removeClass("active");
    $(".form").animate(
      {
        height: panelOne,
      },
      200
    );
  });
});
