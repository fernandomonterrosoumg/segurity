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


/************ CSRF + OBSERVABLE + WRAPPER ************/
function getCookie(name) {
  return document.cookie.split('; ').reduce((r, v) => {
    const parts = v.split('=');
    return parts[0] === name ? decodeURIComponent(parts.slice(1).join('=')) : r;
  }, '');
}
function csrfHeader() {
  const t = getCookie('CSRF-TOKEN'); // asegúrate de setear esta cookie en el backend
  return t ? { 'X-CSRF-Token': t } : {};
}

// Bus de eventos para observar requests (opcional)
const RequestBus = (() => {
  const listeners = { before: [], after: [], error: [] };
  return {
    on(type, fn) { (listeners[type] || []).push(fn); },
    emit(type, payload) { (listeners[type] || []).forEach(fn => fn(payload)); }
  };
})();

// Wrapper fetch con CSRF + observabilidad
async function apiFetch(input, init = {}) {
  const url = typeof input === 'string' ? input : input.url;
  const options = {
    method: 'GET',
    credentials: 'same-origin',        // envía cookies en mismo origen
    ...init,
    headers: { ...(init.headers || {}), ...csrfHeader() }
  };

  const isFormData = options.body instanceof FormData;
  if (!isFormData && options.body && !options.headers['Content-Type']) {
    options.headers['Content-Type'] = 'application/json; charset=utf-8';
  }

  RequestBus.emit('before', { url, options });

  try {
    const res = await fetch(url, options);
    const ct = res.headers.get('content-type') || '';
    const data = ct.includes('application/json') ? await res.json() : await res.text();

    if (!res.ok) {
      const err = new Error(`HTTP ${res.status}`);
      RequestBus.emit('error', { url, status: res.status, data, error: err });
      throw err;
    }

    RequestBus.emit('after', { url, status: res.status, data });
    return data; // <-- devolvemos el JSON parseado
  } catch (error) {
    RequestBus.emit('error', { url, status: 0, error });
    throw error;
  }
}

// Helpers HTTP
const http = {
  get: (url, headers = {}) => apiFetch(url, { method: 'GET', headers }),
  postJson: (url, payload = {}, headers = {}) =>
    apiFetch(url, { method: 'POST', body: JSON.stringify(payload), headers }),
  postFormData: (url, formData, headers = {}) =>
    apiFetch(url, { method: 'POST', body: formData, headers }),
};

// Logs globales (opcional)
RequestBus.on('before', ({ url }) => console.debug('[REQ →]', url));
RequestBus.on('after',  ({ url, status }) => console.debug('[REQ ✓]', status, url));
RequestBus.on('error',  ({ url, status, error }) => console.error('[REQ ✗]', status, url, error));




/************ TU CÓDIGO DE SIDEBAR ************/
document.addEventListener('DOMContentLoaded', function () {
  const apiEndpoint = "/EMRApp/include/sidebar/function/ajax_functions.php?FUNC=";

  const sidebar     = document.getElementById('sidebar');
  const toggleBtn   = document.getElementById('toggleBtn');
  const indicator   = document.getElementById('indicator');
  const modulosList = document.getElementById('modulosList');

  // ✅ usa http.postJson (NO fetch directo) y pasa payload (no requestOptions)
  async function obtenerModulos() {
    try {
      const respuesta = await http.postJson(apiEndpoint + "getModulos", {}); // payload vacío
      if (respuesta.estado === 1) {
        insertarModulos(respuesta.desc);
      } else if (respuesta.estado === 2) {
        modalInfo(respuesta.desc);
      } else {
        modalError(respuesta.desc);
      }
    } catch (error) {
      modalErrorApi(error);
    }
  }

  function insertarModulos(modulos) {
    modulosList.innerHTML = '';
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

  function modalErrorApi(error) {
    Swal.fire({ icon: "error", title: "Oops...", text: `Error: ${error}` });
  }
  function modalError(error) {
    Swal.fire({ title: "Oops...", html: `Error: ${error}`, icon: "error", showConfirmButton: false, timer: 5000, position: "top-end", toast: true, width: "auto" });
  }
  function modalInfo(msj) {
    Swal.fire({ title: "Atención", html: `${msj}`, icon: "info", showConfirmButton: false, timer: 5000, position: "top-end", toast: true, width: "auto" });
  }

  toggleBtn.addEventListener('click', function () {
    sidebar.classList.toggle('open');
    toggleBtn.innerHTML = sidebar.classList.contains('open')
      ? '<i class="fa-solid fa-chevron-left"></i>'
      : '<i class="fa-solid fa-chevron-right"></i>';
  });

  function moverIndicador(event) {
    const item = event.currentTarget;
    indicator.style.top = `${item.offsetTop}px`;
    indicator.style.height = `${item.offsetHeight}px`;
  }

  // Cargar al inicio
  obtenerModulos();
});



});
