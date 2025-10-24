<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EMRApp</title>
  <link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
  <link
    rel="icon"
    type="image/webp"
    href="/EMRApp/include/Images/esculapio1.webp" />

  <link rel="stylesheet" href="/EMRApp/login/css/login.css" />
</head>

<body>
  <!-- Form-->
  <div id="app">
    <div class="form">
      <div class="form-toggle" @click="required = true"></div>
      <div class="form-panel one">
        <div class="form-header">
          <h1>Inicio de Sesión</h1>
        </div>
        <div class="form-content">
          <form>
            <div class="form-group">
              <label for="correo">Correo Electrónico</label>
              <input
                id="correo"
                v-model="dataLogin.mail"
                type="email"
                name="correo"
                required="required" />
            </div>
            <div class="form-group">
              <label for="password">Contraseña</label>
              <input
                id="password"
                v-model="dataLogin.password"
                type="password"
                name="password"
                required="required" />
            </div>
            <div class="form-group">
              <button
                type="submit"
                class="btn btn-outline-primary"
                @click="validarFormulario('iniciarSesion')">
                Iniciar Sesión
              </button>
            </div>
          </form>

          <div class="form-group" style="margin-top:.5rem">
            <button type="button" class="btn btn-outline-danger" @click="loginConGoogle()">
              Continuar con Google
            </button>
          </div>

        </div>
      </div>

      <div class="form-panel two" @click="cambioValidation()">
        <div class="form-header">
          <h1>Registrar Cuenta</h1>
        </div>
        <div class="form-content">
          <form class="needs-validation">
            <div class="form-group">
              <label for="nombres">Nombres</label>
              <div class="row">
                <div class="col">
                  <input
                    type="text"
                    id="nombre1"
                    v-model="dataRegistro.nombre1"
                    placeholder="Primer Nombre"
                    :required="!required" />
                </div>
                <div class="col">
                  <input
                    type="text"
                    id="nombre2"
                    v-model="dataRegistro.nombre2"
                    placeholder="Segundo Nombre" />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="nombres">Apellidos</label>
              <div class="row">
                <div class="col">
                  <input
                    type="text"
                    id="apellido1"
                    v-model="dataRegistro.apellido1"
                    placeholder="Primer Apellido"
                    :required="!required" />
                </div>
                <div class="col">
                  <input
                    type="text"
                    id="apellido2"
                    v-model="dataRegistro.apellido2"
                    placeholder="Segundo Apellido" />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="username">Correo Electronico</label>
              <input
                id="correoR"
                v-model="dataRegistro.mail"
                type="email"
                name="correoR"
                :required="!required" />
            </div>

            <div class="form-group">
              <label for="passworR">Contraseña</label>
              <input
                id="passworR"
                v-model="dataRegistro.password"
                type="password"
                name="passworR"
                :required="!required" />
            </div>

            <div class="form-group">
              <label for="cpassword">Confirmar Contraseña</label>
              <input
                id="cpassword"
                v-model="dataRegistro.confirmPassword"
                type="password"
                name="cpassword"
                :required="!required" />
            </div>

            <div class="form-group">
              <button
                type="submit"
                class="btn btn-light"
                @click="validarFormulario('registro')">
                Registrar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="/EMRApp/include/js_lib/JQuery/3.7.1.js"></script>
  <script src="/EMRApp/include/js_lib/bootstrap/5.0.2.js"></script>
  <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
  <script src="/EMRApp/include/js_lib/VUEjs/3.4.33.js"></script>
  <script src="/EMRApp/login/js/login.js"></script>
</body>

</html>