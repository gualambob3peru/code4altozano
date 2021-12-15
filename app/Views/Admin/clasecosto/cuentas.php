<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js"></script>

<style>
    .marcado {
        background: blue;
    }

    .liAcciones:hover .btnEditarCuenta {
        display: inline-flex;
    }

    .liAcciones:hover .btnEliminarCuenta {
        display: inline-flex;
    }

    .btnEditarCuenta {
        display: none;
    }

    .btnEliminarCuenta {
        display: none;
    }
</style>

<h3>Gestión de Cuentas</h3>
<hr>
<div class="row">
    <div class="col-md-3">
        <h5>Clase de costo</h5>
        <button id="btn_clasecosto" class="btn btn-success mb-4"><i class="bi bi-plus-lg"></i> Agregar</button>
        <div id="clase"></div>
    </div>
    <div class="col-md-3">
        <h5>Cuenta 1</h5>
        <button id="btn_cuenta1" class="btn btn-success mb-4"><i class="bi bi-plus-lg"></i> Agregar</button>
        <div id="cuenta1"></div>
    </div>
    <div class="col-md-3">
        <h5>Cuenta 2</h5>
        <button id="btn_cuenta2" class="btn btn-success mb-4"><i class="bi bi-plus-lg"></i> Agregar</button>
        <div id="cuenta2"></div>
    </div>
    <div class="col-md-3">
        <h5>Cuenta 3</h5>
        <button id="btn_cuenta3" class="btn btn-success mb-4"><i class="bi bi-plus-lg"></i> Agregar</button>
        <div id="cuenta3"></div>
    </div>
</div>


<?php

?>

<div class="modal" tabindex="-1" id="modalEliminar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnOkEliminar">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalAgregarClasecosto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_codigoClasecosto" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_descripcionClasecosto" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnAgregarClasecosto">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEditarClasecosto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="e_idClasecosto" value="">
                        <input type="text" class="form-control" id="e_codigoClasecosto" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="e_descripcionClasecosto" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEditarClasecosto">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEliminarClasecosto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="el_idClaseCosto" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEliminarClasecosto">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalAgregarCuenta1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_codigoCuenta1" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_descripcionCuenta1" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnAgregarCuenta1">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEditarCuenta1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="e_idCuenta1" value="">
                        <input type="text" class="form-control" id="e_codigoCuenta1" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="e_descripcionCuenta1" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEditarCuenta1">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEliminarCuenta1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="el_idCuenta1" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEliminarCuenta1">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalAgregarCuenta2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_codigoCuenta2" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_descripcionCuenta2" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnAgregarCuenta2">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEditarCuenta2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="e_idCuenta2" value="">
                        <input type="text" class="form-control" id="e_codigoCuenta2" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="e_descripcionCuenta2" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEditarCuenta2">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEliminarCuenta2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="el_idCuenta2" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEliminarCuenta2">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalAgregarCuenta3">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_codigoCuenta3" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="a_descripcionCuenta3" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnAgregarCuenta3">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEditarCuenta3">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar cuenta 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="e_idCuenta3" value="">
                        <input type="text" class="form-control" id="e_codigoCuenta3" placeholder="Código">

                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="e_descripcionCuenta3" placeholder="Descripción">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEditarCuenta3">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalEliminarCuenta3">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar clase de costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row">
                    <div class="col-sm-12">
                        <input type="hidden" id="el_idCuenta3" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnEliminarCuenta3">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<script>
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {}),
        modalAgregarClasecosto = new bootstrap.Modal(document.getElementById('modalAgregarClasecosto'), {}),
        modalEditarClasecosto = new bootstrap.Modal(document.getElementById('modalEditarClasecosto'), {}),
        modalEliminarClasecosto = new bootstrap.Modal(document.getElementById('modalEliminarClasecosto'), {}),
        modalAgregarCuenta1 = new bootstrap.Modal(document.getElementById('modalAgregarCuenta1'), {}),
        modalEditarCuenta1 = new bootstrap.Modal(document.getElementById('modalEditarCuenta1'), {}),
        modalEliminarCuenta1 = new bootstrap.Modal(document.getElementById('modalEliminarCuenta1'), {}),
        modalAgregarCuenta2 = new bootstrap.Modal(document.getElementById('modalAgregarCuenta2'), {}),
        modalEditarCuenta2 = new bootstrap.Modal(document.getElementById('modalEditarCuenta2'), {}),
        modalEliminarCuenta2 = new bootstrap.Modal(document.getElementById('modalEliminarCuenta2'), {}),
        modalAgregarCuenta3 = new bootstrap.Modal(document.getElementById('modalAgregarCuenta3'), {}),
        modalEditarCuenta3 = new bootstrap.Modal(document.getElementById('modalEditarCuenta3'), {}),
        modalEliminarCuenta3 = new bootstrap.Modal(document.getElementById('modalEliminarCuenta3'), {}),
        clasecostos = JSON.parse('<?php echo json_encode($clasecostos) ?>'),
        cuentas1_all = JSON.parse('<?php echo json_encode($cuenta1_all) ?>'),
        cuentas2_all = JSON.parse('<?php echo json_encode($cuenta2_all) ?>'),
        cuentas3_all = JSON.parse('<?php echo json_encode($cuenta3_all) ?>'),
        idClasesocial = 0,
        idCuenta1 = 0,
        idCuenta2 = 0,
        idCuenta3 = 0;

    let setClaseAjax = function() {
        fetch("admin/clasecosto/ajaxGetClase", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                let clasecostos = response.response;
                let div = '<ul class="list-group">';
                for (let i = 0; i < clasecostos.length; i++) {
                    div += '<li miId="' + clasecostos[i].id + '" class="list-group-item list-group-item-action f_clasecosto liAcciones">' + clasecostos[i].codigo + " - " + clasecostos[i].descripcion + ' <div class="btn-group float-end"> <button type="button" class="btn btn-warning btnEditarCuenta" miId="' + clasecostos[i].id + '" codigo="' + clasecostos[i].codigo + '" descripcion="' + clasecostos[i].descripcion + '"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-danger btnEliminarCuenta"  miId="' + clasecostos[i].id + '" codigo="' + clasecostos[i].codigo + '" descripcion="' + clasecostos[i].descripcion + '"><i class="bi bi-trash"></i></button></div> </li>';
                }
                div += '</ul>';



                document.getElementById('clase').innerHTML = div;
                document.getElementById('cuenta1').innerHTML = '';
                document.getElementById('cuenta2').innerHTML = '';
                document.getElementById('cuenta3').innerHTML = '';

                let elements = document.getElementsByClassName("f_clasecosto");

                for (let i = 0; i < elements.length; i++) {
                    let miId = elements[i].getAttribute('miId');
                    elements[i].onclick = function() {
                        let f_clasecosto = document.getElementsByClassName("f_clasecosto");
                        for (let index = 0; index < f_clasecosto.length; index++) {
                            f_clasecosto[index].classList.remove('bg-info');
                        }
                        this.classList.add('bg-info');
                        setCuenta1Ajax(miId);
                        idClasesocial = miId;
                        document.getElementById('cuenta2').innerHTML = '';
                        document.getElementById('cuenta3').innerHTML = '';
                    }
                }

                let btnEditar_all = document.querySelectorAll(".f_clasecosto .btnEditarCuenta");

                for (let i = 0; i < btnEditar_all.length; i++) {

                    btnEditar_all[i].onclick = function() {
                        e_codigoClasecosto.value = this.getAttribute('codigo');
                        e_descripcionClasecosto.value = this.getAttribute('descripcion');
                        e_idClasecosto.value = this.getAttribute('miId');
                        modalEditarClasecosto.show();
                    }
                }

                let btnEliminar_all = document.querySelectorAll(".f_clasecosto .btnEliminarCuenta");

                for (let i = 0; i < btnEliminar_all.length; i++) {
                    btnEliminar_all[i].onclick = function() {
                        el_idClaseCosto.value = this.getAttribute('miId');
                        modalEliminarClasecosto.show();
                    }
                }
                
                
            })
            .catch(error => console.log('error', error));
    }

    let setCuenta1Ajax = function(idCuenta) {
        var formdata = new FormData();
        formdata.append("idCuenta", idCuenta);

        fetch("admin/cuenta1/ajaxGetCuenta1", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                let cuentas1_all = response.response;
                let div = '<div class="list-group">';
                for (let i = 0; i < cuentas1_all.length; i++) {
                    if (cuentas1_all[i].idCuenta == idCuenta) {
                        div += '<li miId="' + cuentas1_all[i].id + '" class="list-group-item list-group-item-action f_cuenta1 liAcciones">' + cuentas1_all[i].codigo + " - " + cuentas1_all[i].descripcion + ' <div class="btn-group float-end"> <button type="button" class="btn btn-warning btnEditarCuenta" miId="' + cuentas1_all[i].id + '" codigo="' + cuentas1_all[i].codigo + '" descripcion="' + cuentas1_all[i].descripcion + '"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-danger btnEliminarCuenta"  miId="' + cuentas1_all[i].id + '" codigo="' + cuentas1_all[i].codigo + '" descripcion="' + cuentas1_all[i].descripcion + '"><i class="bi bi-trash"></i></button></div> </li>';
                    }
                }
                div += '</div>';
                if (!cuentas1_all.length) div = '<span>No existen cuentas...</span>';

                document.getElementById('cuenta1').innerHTML = div;
                document.getElementById('cuenta2').innerHTML = '';
                document.getElementById('cuenta3').innerHTML = '';

                let elements = document.getElementsByClassName("f_cuenta1");

                for (let i = 0; i < elements.length; i++) {
                    let miId = elements[i].getAttribute('miId');
                    elements[i].onclick = function() {
                        let f_cuenta1 = document.getElementsByClassName("f_cuenta1");
                        for (let index = 0; index < f_cuenta1.length; index++) {
                            f_cuenta1[index].classList.remove('bg-info');
                        }
                        this.classList.add('bg-info');
                        setCuenta2Ajax(miId);
                        idCuenta1 = miId;
                    }
                }

                let btnEditar_all = document.querySelectorAll(".f_cuenta1 .btnEditarCuenta");

                for (let i = 0; i < btnEditar_all.length; i++) {

                    btnEditar_all[i].onclick = function() {
                        e_codigoCuenta1.value = this.getAttribute('codigo');
                        e_descripcionCuenta1.value = this.getAttribute('descripcion');
                        e_idCuenta1.value = this.getAttribute('miId');
                        modalEditarCuenta1.show();
                    }
                }

                let btnEliminar_all = document.querySelectorAll(".f_cuenta1 .btnEliminarCuenta");

                for (let i = 0; i < btnEliminar_all.length; i++) {
                    btnEliminar_all[i].onclick = function() {
                        el_idCuenta1.value = this.getAttribute('miId');
                        modalEliminarCuenta1.show();
                    }
                }
            })
            .catch(error => console.log('error', error));
    }

    let setCuenta2Ajax = function(idCuenta) {
        var formdata = new FormData();
        formdata.append("idCuenta", idCuenta);

        fetch("admin/cuenta2/ajaxGetCuenta2", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                let cuentas2_all = response.response;
                let div = '<div class="list-group">';
                for (let i = 0; i < cuentas2_all.length; i++) {
                    if (cuentas2_all[i].idCuenta == idCuenta) {
                        div += '<li miId="' + cuentas2_all[i].id + '" class="list-group-item list-group-item-action f_cuenta2 liAcciones">' + cuentas2_all[i].codigo + " - " + cuentas2_all[i].descripcion + ' <div class="btn-group float-end"> <button type="button" class="btn btn-warning btnEditarCuenta" miId="' + cuentas2_all[i].id + '" codigo="' + cuentas2_all[i].codigo + '" descripcion="' + cuentas2_all[i].descripcion + '"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-danger btnEliminarCuenta"  miId="' + cuentas2_all[i].id + '" codigo="' + cuentas2_all[i].codigo + '" descripcion="' + cuentas2_all[i].descripcion + '"><i class="bi bi-trash"></i></button></div> </li>';
                    }
                }
                div += '</div>';
                if (!cuentas2_all.length) div = '<span>No existen cuentas...</span>';

                document.getElementById('cuenta2').innerHTML = div;
                document.getElementById('cuenta3').innerHTML = '';

                let elements = document.getElementsByClassName("f_cuenta2");

                for (let i = 0; i < elements.length; i++) {
                    let miId = elements[i].getAttribute('miId');
                    elements[i].onclick = function() {
                        let f_cuenta2 = document.getElementsByClassName("f_cuenta2");
                        for (let index = 0; index < f_cuenta2.length; index++) {
                            f_cuenta2[index].classList.remove('bg-info');
                        }
                        this.classList.add('bg-info');
                        setCuenta3Ajax(miId);
                        idCuenta2 = miId;
                    }
                }

                let btnEditar_all = document.querySelectorAll(".f_cuenta2 .btnEditarCuenta");

                for (let i = 0; i < btnEditar_all.length; i++) {

                    btnEditar_all[i].onclick = function() {
                        e_codigoCuenta2.value = this.getAttribute('codigo');
                        e_descripcionCuenta2.value = this.getAttribute('descripcion');
                        e_idCuenta2.value = this.getAttribute('miId');
                        modalEditarCuenta2.show();
                    }
                }

                let btnEliminar_all = document.querySelectorAll(".f_cuenta2 .btnEliminarCuenta");

                for (let i = 0; i < btnEliminar_all.length; i++) {
                    btnEliminar_all[i].onclick = function() {
                        el_idCuenta2.value = this.getAttribute('miId');
                        modalEliminarCuenta2.show();
                    }
                }
            })
            .catch(error => console.log('error', error));
    }

    let setCuenta3Ajax = function(idCuenta) {
        var formdata = new FormData();
        formdata.append("idCuenta", idCuenta);

        fetch("admin/cuenta3/ajaxGetCuenta3", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                let cuentas3_all = response.response;
                let div = '<div class="list-group">';
                for (let i = 0; i < cuentas3_all.length; i++) {
                    if (cuentas3_all[i].idCuenta == idCuenta) {
                        div += '<li miId="' + cuentas3_all[i].id + '" class="list-group-item list-group-item-action f_cuenta3 liAcciones">' + cuentas3_all[i].codigo + " - " + cuentas3_all[i].descripcion + ' <div class="btn-group float-end"> <button type="button" class="btn btn-warning btnEditarCuenta" miId="' + cuentas3_all[i].id + '" codigo="' + cuentas3_all[i].codigo + '" descripcion="' + cuentas3_all[i].descripcion + '"><i class="bi bi-pencil"></i></button> <button type="button" class="btn btn-danger btnEliminarCuenta"  miId="' + cuentas3_all[i].id + '" codigo="' + cuentas3_all[i].codigo + '" descripcion="' + cuentas3_all[i].descripcion + '"><i class="bi bi-trash"></i></button></div> </li>';
                    }
                }
                div += '</div>';
                if (!cuentas3_all.length) div = '<span>No existen cuentas...</span>';

                document.getElementById('cuenta3').innerHTML = div;

                let elements = document.getElementsByClassName("f_cuenta3");

                for (let i = 0; i < elements.length; i++) {
                    let miId = elements[i].getAttribute('miId');
                    elements[i].onclick = function() {
                        let f_cuenta3 = document.getElementsByClassName("f_cuenta3");
                        for (let index = 0; index < f_cuenta3.length; index++) {
                            f_cuenta3[index].classList.remove('bg-info');
                        }
                        this.classList.add('bg-info');

                        idCuenta3 = miId;
                    }
                }

                let btnEditar_all = document.querySelectorAll(".f_cuenta3 .btnEditarCuenta");

                for (let i = 0; i < btnEditar_all.length; i++) {

                    btnEditar_all[i].onclick = function() {
                        e_codigoCuenta3.value = this.getAttribute('codigo');
                        e_descripcionCuenta3.value = this.getAttribute('descripcion');
                        e_idCuenta3.value = this.getAttribute('miId');
                        modalEditarCuenta3.show();
                    }
                }

                let btnEliminar_all = document.querySelectorAll(".f_cuenta3 .btnEliminarCuenta");

                for (let i = 0; i < btnEliminar_all.length; i++) {
                    btnEliminar_all[i].onclick = function() {
                        el_idCuenta3.value = this.getAttribute('miId');
                        modalEliminarCuenta3.show();
                    }
                }
            })
            .catch(error => console.log('error', error));
    }

    btn_clasecosto.onclick = function() {
        modalAgregarClasecosto.show();
    }

    btnAgregarClasecosto.onclick = function() {
        var formdata = new FormData();
        formdata.append("codigo", a_codigoClasecosto.value);
        formdata.append("descripcion", a_descripcionClasecosto.value);

        fetch("admin/clasecosto/ajaxAgregar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalAgregarClasecosto.hide();
                    setClaseAjax();
                }
            })
            .catch(error => console.log('error', error));
    }

    btnEditarClasecosto.onclick = function() {
        var formdata = new FormData();
        formdata.append("id", e_idClasecosto.value);
        formdata.append("codigo", e_codigoClasecosto.value);
        formdata.append("descripcion", e_descripcionClasecosto.value);

        fetch("admin/clasecosto/ajaxEditar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalEditarClasecosto.hide();
                    setClaseAjax();
                }
            })
            .catch(error => console.log('error', error));
    }

    btn_cuenta1.onclick = function() {
        modalAgregarCuenta1.show();
    }

    btnAgregarCuenta1.onclick = function() {

        var formdata = new FormData();
        formdata.append("codigo", a_codigoCuenta1.value);
        formdata.append("descripcion", a_descripcionCuenta1.value);
        formdata.append("idCuenta", idClasesocial);

        fetch("admin/cuenta1/ajaxAgregar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalAgregarCuenta1.hide();
                    setCuenta1Ajax(idClasesocial);
                }
            })
            .catch(error => console.log('error', error));

    }

    btnEditarCuenta1.onclick = function() {

        var formdata = new FormData();
        formdata.append("codigo", e_codigoCuenta1.value);
        formdata.append("descripcion", e_descripcionCuenta1.value);
        formdata.append("id", e_idCuenta1.value);

        fetch("admin/cuenta1/ajaxEditar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalEditarCuenta1.hide();
                    setCuenta1Ajax(idClasesocial);
                }
            })
            .catch(error => console.log('error', error));

    }

    btn_cuenta2.onclick = function() {
        modalAgregarCuenta2.show();
    }

    btnAgregarCuenta2.onclick = function() {

        var formdata = new FormData();
        formdata.append("codigo", a_codigoCuenta2.value);
        formdata.append("descripcion", a_descripcionCuenta2.value);
        formdata.append("idCuenta", idCuenta1);

        fetch("admin/cuenta2/ajaxAgregar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalAgregarCuenta2.hide();
                    setCuenta2Ajax(idCuenta1);
                }
            })
            .catch(error => console.log('error', error));

    }

    btnEditarCuenta2.onclick = function() {

        var formdata = new FormData();
        formdata.append("codigo", e_codigoCuenta2.value);
        formdata.append("descripcion", e_descripcionCuenta2.value);
        formdata.append("id", e_idCuenta2.value);

        fetch("admin/cuenta2/ajaxEditar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalEditarCuenta2.hide();
                    setCuenta2Ajax(idCuenta1);
                }
            })
            .catch(error => console.log('error', error));

    }

    btn_cuenta3.onclick = function() {
        modalAgregarCuenta3.show();
    }

    btnAgregarCuenta3.onclick = function() {

        var formdata = new FormData();
        formdata.append("codigo", a_codigoCuenta3.value);
        formdata.append("descripcion", a_descripcionCuenta3.value);
        formdata.append("idCuenta", idCuenta2);

        fetch("admin/cuenta3/ajaxAgregar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalAgregarCuenta3.hide();
                    setCuenta3Ajax(idCuenta2);
                }
            })
            .catch(error => console.log('error', error));

    }

    btnEditarCuenta3.onclick = function() {
        var formdata = new FormData();
        formdata.append("codigo", e_codigoCuenta3.value);
        formdata.append("descripcion", e_descripcionCuenta3.value);
        formdata.append("id", e_idCuenta3.value);

        fetch("admin/cuenta3/ajaxEditar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_codigo + " " + error.error_descripcion)

                } else {
                    modalEditarCuenta3.hide();
                    setCuenta3Ajax(idCuenta2);
                }
            })
            .catch(error => console.log('error', error));

    }

    btnEliminarClasecosto.onclick = function(){
        var formdata = new FormData();
        formdata.append("id", el_idClaseCosto.value);

        fetch("admin/clasecosto/ajaxEliminar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_id)

                } else {
                    modalEliminarClasecosto.hide();
                    setClaseAjax();
                }
            })
            .catch(error => console.log('error', error));
    }

    btnEliminarCuenta1.onclick = function(){
        var formdata = new FormData();
        formdata.append("id", el_idCuenta1.value);

        fetch("admin/cuenta1/ajaxEliminar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_id)

                } else {
                    modalEliminarCuenta1.hide();
                    setCuenta1Ajax(idClasesocial);
                }
            })
            .catch(error => console.log('error', error));
    }

    btnEliminarCuenta2.onclick = function(){
        var formdata = new FormData();
        formdata.append("id", el_idCuenta2.value);

        fetch("admin/cuenta2/ajaxEliminar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_id)

                } else {
                    modalEliminarCuenta2.hide();
                    setCuenta2Ajax(idCuenta1);
                }
            })
            .catch(error => console.log('error', error));
    }

    btnEliminarCuenta3.onclick = function(){
        var formdata = new FormData();
        formdata.append("id", el_idCuenta3.value);

        fetch("admin/cuenta3/ajaxEliminar", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                if (response.response == 0) {
                    let error = response.data_error;
                    alert(error.error_id)

                } else {
                    modalEliminarCuenta3.hide();
                    setCuenta3Ajax(idCuenta2);
                }
            })
            .catch(error => console.log('error', error));
    }


    setClaseAjax();
</script>