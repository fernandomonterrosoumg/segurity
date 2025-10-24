<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EMRApp</title>

    <link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
    <link rel="icon" type="image/webp" href="/EMRApp/include/Images/esculapio1.webp">
    <link rel="stylesheet" href="/EMRApp/module/ingreso_paciente/css/Frm_Paciente.css">
</head>

<body>

    <div id="app">

        <form class="needs-validation" action="" novalidate>
            <div class="card">
                <div class="card-header" style="padding: 12px;">
                    <div class="row align-items-center" style="margin: 0;">
                        <div class="col-6 d-flex align-items-center">
                            <img style="border-radius: 50%; border: 4px solid white; object-fit: cover; width: 105px; height: 125px;"
                                :src="paciente.PAC_FOTO ? `/EMRApp/adjunto/FotosPacientes/${paciente.PAC_FOTO}?t=${timestamp}` : '/EMRApp/include/Images/user.png'"
                                alt="Imagen Paciente">

                            <!-- Ajuste del texto a la derecha de la imagen -->
                            <div class="ms-3 ajustar texto">
                                <h1 class="title-card text-start" style="margin: 0;">
                                    {{paciente.NOMBRE}}
                                </h1>
                            </div>
                        </div>

                        <div class="col-6 title-p-1">
                            <h1 class="title-card" style="margin-right: 10px; font-size: 70px">
                                {{paciente.EDAD}} Años
                            </h1>
                        </div>
                    </div>

                </div>

                <div class="card-body">

                    <div class="form-seccion-1">
                        <div class="form-title">
                            <h2>Datos Personales</h2>
                        </div>

                        <div class="mb-3">
                            <label for="validationCustom01 validationCustom02 validationCustom03" class="form-label">Nombres del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Primer Nombre" v-model="paciente.PAC_NOMBRE1" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom02" placeholder="Segundo Nombre" v-model="paciente.PAC_NOMBRE2" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom03" placeholder="Tercer Nombre (Opcional)" v-model="paciente.PAC_NOMBRE3" :disabled="disabled">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="validationCustom04 validationCustom05 validationCustom06" class="form-label">Apellidos del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom04" placeholder="Primer Apellido" v-model="paciente.PAC_APELLIDO1" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom05" placeholder="Segundo Apellido" v-model="paciente.PAC_APELLIDO2" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" id="validationCustom06" placeholder="Apellido de Casado (Opcional)" v-model="paciente.PAC_APELLIDOC" :disabled="disabled">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="validationCustom07" class="form-label">Documento de Identificación <span style="color: red; font-weight: bold;">*</span></label>
                                    <input type="text" class="form-control" id="validationCustom07" placeholder="Ingresar correctamente, ya que no se admiten cambios." v-model="paciente.PAC_DOCNUM" disabled>
                                </div>
                                <div class="col">
                                    <label for="formDate" class="form-label">Fecha de Naciemiento <span style="color: red; font-weight: bold;">*</span></label>
                                    <input type="text" class="form-control" id="formDate" v-model="paciente.PAC_FEC_NAC" disabled>
                                </div>
                            </div>
                        </div>

                        <div v-if="!disabled" class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="formFile" class="form-label">Foto del Paciente</label>
                                    <input class="form-control" type="file" id="formFile" accept="image/*" :disabled="disabled">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="validationCustom08" class="form-label">Ocupación</label>
                                    <input type="text" class="form-control" id="validationCustom08" placeholder="Ej. Programador" v-model="paciente.PAC_OCUPACION" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <label for="validationCustom09" class="form-label">Nacionalidad <span style="color: red; font-weight: bold;">*</span></label>
                                    <input type="text" class="form-control" id="validationCustom09" placeholder="Ej. Programador" v-model="paciente.NAC_DESC" disabled>
                                </div>
                                <div class="col">
                                    <label for="validationCustom10" class="form-label">Estado Civil <span style="color: red; font-weight: bold;">*</span></label>
                                    <template v-if="disabled">
                                        <input type="text" class="form-control" id="validationCustom09" placeholder="Ej. Programador" v-model="paciente.EC_DESC" disabled>
                                    </template>
                                    <template v-else>
                                        <select class="form-select" id="validationCustom10 autoSizingSelect" v-model="paciente.EC_ID" :required="required">
                                            <option disabledd selected value="">--Seleccionar--</option>
                                            <option v-for="ECOption in estadoCivilOptions" :value="ECOption.EC_ID">{{ECOption.EC_DESC}}</option>
                                        </select>
                                    </template>

                                </div>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <div class="form-seccion-2">
                        <div class="form-title">
                            <h2>Datos de Contacto</h2>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="validationCustom11" class="form-label">Teléfono del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                                    <input type="number" min="0" class="form-control" id="validationCustom11" placeholder="Ej. 15353242" v-model="paciente.PAC_TELEFONO" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <label for="inputEmail4" class="form-label">Correo del Paciente <span style="color: red; font-weight: bold;">*</span></label>
                                    <input type="email" class="form-control" id="inputEmail4" placeholder="Ej. ejemplo@email.com" v-model="paciente.PAC_CORREO" :required="required" :disabled="disabled">
                                </div>
                                <div class="col">
                                    <label for="validationCustom12" class="form-label">Contacto de Emergencia</label>
                                    <input type="number" min="0" class="form-control" id="validationCustom12" v-model="paciente.PAC_TELEFONO_EMG" placeholder="Ej. 12345678" :disabled="disabled">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="validationCustom13" class="form-label">Dirección de Domicilio</label>
                                    <input type="text" class="form-control" id="validationCustom13" v-model="paciente.PAC_DIRECCION" placeholder="Ej. 7a Ave. 'A' 17-78 Col. Reyna Barrios Z. 13" :disabled="disabled">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-seccion-3">
                        <div class="form-title">
                            <h2>Datos Vitales y Biométricos</h2>
                            <div class="mb-3">
                                <div class="row">

                                    <div class="col">
                                        <label for="validationCustom14" class="form-label">Genero <span style="color: red; font-weight: bold;">*</span></label>
                                        <input type="text" class="form-control" id="validationCustom21" v-model="genero" disabled>
                                    </div>

                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <label for="validationCustom15" class="form-label">Peso del Paciente</label>
                                        <div class="input-group">
                                            <input type="number" min="0" step="0.01" class="form-control" id="validationCustom15" v-model="paciente.DC_PESOLB_ACTUAL" placeholder="Ej. 145.5" :disabled="disabled">
                                            <div class="input-group-text">lb</div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <label for="validationCustom16" class="form-label">Altura del Paciente</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control" id="validationCustom16" v-model="paciente.DC_ALTURACM_ACTUAL" placeholder="Ej. 170" :disabled="disabled">
                                            <div class="input-group-text">cm</div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <label for="validationCustom22" class="form-label">Nivel de Glucosa</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="validationCustom22" v-model="paciente.DC_ULT_MED_GLUCOSAMGDL" placeholder="Ej. De 90 a 130" :disabled="disabled">
                                            <div class="input-group-text">mg/dl</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row">

                                    <div class="col">
                                        <label for="validationCustom17" class="form-label">Tipo de Sangre</label>
                                        <input type="text" class="form-control" id="validationCustom20" v-model="paciente.TIPO_SANGRE" disabled>
                                    </div>

                                    <div class="col">
                                        <label for="validationCustom18" class="form-label">Presión Arterial</label>
                                        <div class="input-group">
                                            <input type="text" min="0" class="form-control" id="validationCustom18" v-model="paciente.DC_ULT_MED_PA_MMHG" placeholder="Ej. 120/80" :disabled="disabled">
                                            <div class="input-group-text">mmHg</div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <label for="validationCustom19" class="form-label">Frecuencia Cardiaca</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control" id="validationCustom19" v-model="paciente.DC_ULT_MED_FC_LPM" placeholder="Ej. 100" :disabled="disabled">
                                            <div class="input-group-text">LPM</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <div v-if="disabled">
                        <button type="button" style="margin-right: 5px;"  class="btn btn-outline-primary" @click="disabled=false">Editar</button>
                        <button type="button" style="margin-left: 5px;"  class="btn btn-outline-secondary" @click="ingresarCita">Ingresar Cita</button>
                    </div>
                    <div v-else>
                        <button type="button" class="btn btn-outline-success" style="margin-right: 5px;" @click="updPaciente">Actualizar</button>
                        <button type="button" class="btn btn-outline-danger" style="margin-left: 5px;" @click="disabled=true">Cancelar</button>
                    </div>
                </div>
            </div>
        </form>

        <!--ANIMACIÓN DE CARGA -->
        <div v-if="this.mostrarAnimacion" class="centrar-sobreponer">
            <div class="spinner-box">
            <div class="blue-orbit leo"></div>

            <div class="green-orbit leo"></div>

            <div class="red-orbit leo"></div>

            <div class="white-orbit w1 leo"></div>
            <div class="white-orbit w2 leo"></div>
            <div class="white-orbit w3 leo"></div>
            </div>
        </div>

    </div>

    <script src="/EMRApp/include/js_lib/JQuery/3.7.1.js"></script>
    <script src="/EMRApp/include/js_lib/bootstrap/5.0.2.js"></script>
    <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
    <script src="/EMRApp/include/js_lib/VUEjs/3.4.33.js"></script>
    <script src="/EMRApp/module/consulta_pacientes/js/Dtl_Paciente.js"></script>
</body>

</html>