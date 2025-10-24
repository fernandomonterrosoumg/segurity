var apiEndpoint = '/EMRApp/module/ingreso_paciente/function/ajax_functions.php?FUNC=';
var Headers = {
  json: { header: 'Content-Type', value: 'application/json' },
  form: { header: 'Content-Type', value: 'application/x-www-form-urlencoded' }
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
  mounted: function () {
    this.getEstCivil();
    this.getTipoSangre();
    this.getNacionalidad();
  },
  data() {
    return {
      mostrarAnimacion: false,
      required: true,
      estadoCivilOptions: {},
      tipoSangreOptions: {},
      nacOptions: {},
      paciente: {
        nombre1: "",
        nombre2: "",
        nombre3: "",
        apellido1: "",
        apellido2: "",
        apellidoC: "",
        docId: "",
        fecNac: null,
        foto: "",
        ocupacion: "",
        nacionalidadId: "",
        estadoCivil: "",
        telefono: null,
        correo: "",
        telEmergencia: null,
        direccion: "",
        correlativo: "",
      },
      datClinico: {
        genero: 1,
        pesoLB: "",
        altura: "",
        tipoSangre: "",
        presionArt: "",
        freqCardiaca: "",
      },
    };
  },
  watch: {},
  methods: {
    validarFormulario: function() {
      // VALIDACIONES DE CAMPOS REQUERIDOS
      var forms = document.querySelectorAll('.needs-validation');
      const vm = this;
      // Bucle sobre ellos y evitar la submition
      Array.prototype.slice.call(forms)
        .forEach(function (form) {          
          event.stopPropagation();

          //Agrega la clase que da estilos a los campos valios e invalidos
          form.classList.add('was-validated');

          if (form.checkValidity()) {
            vm.guardarPaciente();
          }
        });
    },
    guardarPaciente: async function(){
      //Mostrar animación de carga
      this.mostrarAnimacion = true;

      //Se deshabilita el boton para evitar un doble envío
      btnGuardar = document.querySelector('.btnGuardar');
      btnGuardar.disabled = true;

      try {

        var data = await this.getKeys();
      
        if (data && data.estado) {         

          var data2 = await this.guardarFoto(this.paciente.docId + '-' + data.corr);

          if (data2 && data2[0] && data2[0].archivo && data2[0].archivo != "error") {
            this.paciente.foto = data2[0].archivo;
          } 

          this.paciente.correlativo = data.corr;
          var raw = JSON.stringify({
            paciente: this.paciente,
            datClinico: this.datClinico,
          });
          var requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json; charset=utf-8" },
            body: raw,
            redirect: "follow",
          };
  
          fetch(apiEndpoint + 'guardarPaciente',requestOptions)
            .then(response => {         
              return response.json();
            })
            .then(respuesta => {
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
          this.modalError('Error inesperado al obtener las Keys del paciente.');
        }
        
      } catch (error) {
        this.modalErrorApi(error)
      } finally {
        this.mostrarAnimacion = false;
      }
    },
    getKeys: async function() {
      var raw = JSON.stringify({
        paciente: this.paciente,
      });
    
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        body: raw,
        redirect: "follow",
      };
    
      try {
        const response = await fetch(apiEndpoint + 'getKeys', requestOptions);
        const datos = await response.json();
    
        if (datos.estado) {
          return datos;
        } else {
          this.modalError(datos.desc);
          return datos;  // Puedes retornar un valor específico aquí si quieres manejar errores de otra manera
        }
      } catch (error) {
        return null;
      }
    },    
    guardarFoto: async function (nombre) {
      const formData = new FormData();
      const fileInputs = document.querySelectorAll('#formFile');
      let archivoCargado = false;
      

      fileInputs.forEach(fileLput => {
          // Añade el archivo de cada elemento al FormData
          if (fileLput.files && fileLput.files.length > 0) {
              archivoCargado = true;
              formData.append('ARCHIVO[]', fileLput.files[0]);
              formData.append('fileName[]', nombre);
          }
      });

      if (!archivoCargado) {
        return;
      }
      
      formData.append('ROOT', 'foto_paciente');
      
      return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: '/EMRApp/API/guardarArchivos.php',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {                        
            },
            complete: function() {         
            },
            success: function(response) {
                var respuestas = JSON.parse(response);     
                resolve(respuestas); // Devuelve la respuesta con resolve
            },
            error: function(request, status, error) {
                // Manejar errores               
                var respuestaAPI = JSON.parse(request.responseText);
                this.mostrarAnimacion = false;

                reject(respuestaAPI); // Devuelve el error con reject
            }
        });
    });
    },
    getEstCivil: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getEstadoCivil',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {
          this.estadoCivilOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    getTipoSangre: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getTipoSangre',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {          
          this.tipoSangreOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    },
    getNacionalidad: function(){
      var requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json; charset=utf-8" },
        redirect: "follow",
      };

      fetch(apiEndpoint + 'getNacionalidad',requestOptions)
        .then(response => {
          return response.json();
        })
        .then(datos => {                
          this.nacOptions = datos;
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
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
app.mount('#app');