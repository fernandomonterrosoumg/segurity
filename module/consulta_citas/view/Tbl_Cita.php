<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>EMRApp</title>

<link rel="stylesheet" href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" />
<link rel="stylesheet" href="/EMRApp/include/css_lib/dataTable/2.1.0.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="icon" type="image/webp" href="/EMRApp/include/Images/esculapio1.webp">
</head>

<body>
    <div id="app">
        <div class="card">
            <div class="card-header" style="padding: 12px;">
                <div class="row" style="margin: 0;">
                    <div class="col-6"><img src="/EMRApp/include/Images/esculapio1.webp" alt="" width="116px" class=""></div>
                    <div class="col-6 title-p-1">
                        <h1 class="title-card" style="margin-right: 10px;">Tabla de Pacientes</h1>
                        <img src="/EMRApp/include/Images/find-patients.webp" alt="" width="65px">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="example" class="table table-striped" style="width: 100%" ref="tableCitas">
                    <thead>
                        <tr>
                            <th>Cita</th>
                            <th>Id - DPI</th>
                            <th>Nombre de Paciente</th>
                            <th>Fecha de Cita</th>
                            <th>Doctor que Atendio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cita in citas">
                            <td>{{cita.CM_CORR}}-{{cita.CM_ANIO}}</td>
                            <td>{{cita.PAC_CORR}}-{{cita.PAC_DOCNUM}}</td>
                            <td>{{cita.NOMBRE}}</td>
                            <td>{{cita.CM_FECHA_CITA}}</td>
                            <td>{{cita.DOCTOR}}</td>
                            <td><a class="btn btn-primary" :href="`verCita.php?cm_corr=${cita.CM_CORR}&cm_anio=${cita.CM_ANIO}`">Ver</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/EMRApp/include/js_lib/JQuery/3.7.1.js"></script>
    <script src="/EMRApp/include/js_lib/bootstrap/5.0.2.js"></script>
    <script src="/EMRApp/include/js_lib/dataTable/2.1.0.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
    <script src="/EMRApp/include/js_lib/VUEjs/3.4.33.js"></script>
    <script src="/EMRApp/module/consulta_citas/js/Tbl_Cita.js"></script>
    
</body>

</html>