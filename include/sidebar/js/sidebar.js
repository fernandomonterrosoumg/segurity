document.addEventListener('DOMContentLoaded', function() {
  const apiEndpoint = "/EMRApp/include/sidebar/function/ajax_functions.php?FUNC=";
  
  // Obtener elementos del DOM
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleBtn');
  const indicator = document.getElementById('indicator');
  const modulosList = document.getElementById('modulosList');

  // Función para obtener módulos
  function obtenerModulos() {
      fetch(apiEndpoint + "getModulos", {
          method: "POST",
          headers: { "Content-Type": "application/json; charset=utf-8" }
      })
      .then(response => response.json())
      .then(respuesta => {
          if (respuesta.estado === 1) {
              insertarModulos(respuesta.desc);
          } else if (respuesta.estado === 2) {
              modalInfo(respuesta.desc);
          } else {
              modalError(respuesta.desc);
          }
      })
      .catch(error => {
          modalErrorApi(error);
      });
  }

  // Función para insertar módulos en la lista
  function insertarModulos(modulos) {
      modulos.forEach(modulo => {
          const li = document.createElement('li');
          li.addEventListener('mouseover', moverIndicador);

          const a = document.createElement('a');
          a.className = 'achor-sidebar';
          a.href = modulo.MDL_RUTA;

          const icon = document.createElement('iconify-icon');
          icon.setAttribute('icon', modulo.MDL_IMG);
          icon.setAttribute('width', '1.8em');
          icon.setAttribute('height', '1.8em');
          icon.style.color = 'white';

          const span = document.createElement('span');
          span.textContent = modulo.MDL_NOM;

          a.appendChild(icon);
          a.appendChild(span);
          li.appendChild(a);
          modulosList.appendChild(li);
      });
  }

  // Función para mostrar errores en SweetAlert
  function modalErrorApi(error) {
      Swal.fire({
          icon: "error",
          title: "Oops...",
          text: `Error: ${error}`,
          footer: null,
      });
  }

  function modalError(error) {
      Swal.fire({
          title: "Oops...",
          html: `Error: ${error}`,
          icon: "error",
          showConfirmButton: false,
          timer: 5000,
          position: "top-end",
          toast: true,
          width: "auto",
      });
  }

  function modalInfo(msj) {
      Swal.fire({
          title: "Atención",
          html: `${msj}`,
          icon: "info",
          showConfirmButton: false,
          timer: 5000,
          position: "top-end",
          toast: true,
          width: "auto",
      });
  }

  // Función para desplegar o cerrar la sidebar
  toggleBtn.addEventListener('click', function() {
      sidebar.classList.toggle('open');
      if (sidebar.classList.contains('open')) {
          toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
      } else {
          toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
      }
  });

  // Función para mover el indicador
  function moverIndicador(event) {
      const item = event.currentTarget;
      const itemHeight = item.offsetHeight;
      const offsetTop = item.offsetTop;
      indicator.style.top = `${offsetTop}px`;
      indicator.style.height = `${itemHeight}px`;
  }

  // Llamar a obtenerModulos cuando la página se carga
  obtenerModulos();
});
