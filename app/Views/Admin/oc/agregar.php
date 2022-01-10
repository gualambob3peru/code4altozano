<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

<style>
    .custom-combobox {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }

    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
        width: 100%;
    }

    #divVariosCentro {
        display: none;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script>
    $(function() {



        let empresas_total = JSON.parse('<?php echo json_encode($empresas_total) ?>');
        let cuentasObje = new Object();




        let total_empresas = [];

        for (let i = 0; i < empresas_total.length; i++) {
            let empresa3 = {};
            empresa3.value = empresas_total[i].id;
            empresa3.label = empresas_total[i].ruc + " - " + empresas_total[i].nombre;
            empresa3.ruc = empresas_total[i].ruc;
            empresa3.nombre = empresas_total[i].nombre;

            total_empresas.push(empresa3);
        }

        $.widget("custom.combobox", {
            _create: function() {

                this.wrapper = $("<span>")
                    .addClass("custom-combobox")
                    .insertAfter(this.element);

                this.element.hide();
                this._createAutocomplete();
                this._createShowAllButton();
            },

            _createAutocomplete: function() {
                var selected = this.element.children(":selected"),
                    value = selected.val() ? selected.text() : "";

                this.input = $("<input>")
                    .appendTo(this.wrapper)
                    .val(value)
                    .attr("title", "")
                    .addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: this.options.source,
                        focus: this.options.focus,
                        select: this.options.select
                    })
                    .tooltip({
                        classes: {
                            "ui-tooltip": "ui-state-highlight"
                        }
                    });

                this._on(this.input, {
                    autocompleteselect: function(event, ui) {

                    },

                    autocompletechange: "_removeIfInvalid"
                });
            },

            _createShowAllButton: function() {
                var input = this.input,
                    wasOpen = false;

                $("<a>")
                    .attr("tabIndex", -1)
                    .attr("title", "Mostrar todo")
                    .tooltip()
                    .appendTo(this.wrapper)
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass("ui-corner-all")
                    .addClass("custom-combobox-toggle ui-corner-right")
                    .on("mousedown", function() {
                        wasOpen = input.autocomplete("widget").is(":visible");
                    })
                    .on("click", function() {
                        input.trigger("focus");

                        // Close if already visible
                        if (wasOpen) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete("search", "");
                    });
            },


            _removeIfInvalid: function(event, ui) {

                // Selected an item, nothing to do
                if (ui.item) {
                    return;
                }

                // Search for a match (case-insensitive)
                var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                this.element.children("option").each(function() {
                    if ($(this).text().toLowerCase() === valueLowerCase) {
                        this.selected = valid = true;
                        return false;
                    }
                });

                // Found a match, nothing to do
                if (valid) {
                    return;
                }

                // Remove invalid value
                this.input
                    .val("")
                    .attr("title", value + " No encontrado")
                    .tooltip("open");
                this.element.val("");
                this._delay(function() {
                    this.input.tooltip("close").attr("title", "");
                }, 2500);
                this.input.autocomplete("instance").term = "";
            },

            _destroy: function() {
                this.wrapper.remove();
                this.element.show();
            }
        });


        $("#ejecutado_com").combobox({
            source: total_empresas,
            focus: function(event, ui) {

                $("#ejecutado_com").next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);
                return false;
            },
            select: function(event, ui) {
                $("#ejecutado").val(ui.item.value);
                $("#ejecutado_com").next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);

                let idEmpresa = ui.item.value;
                let miRuc = ui.item.ruc;
                ruc.value = miRuc;

                var formdata = new FormData();
                formdata.append("idEmpresa", idEmpresa);

                fetch("admin/banco/ajaxGet_cuentas", {
                        method: 'POST',
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: formdata,
                        redirect: 'follow'
                    })
                    .then(response => response.json())
                    .then(function(response) {
                        document.querySelectorAll("#idBanco option").forEach(option => option.remove());
                        idBanco.add(new Option("Seleccionar", ""));

                        for (let i = 0; i < response.response.length; i++) {
                            let res = response.response[i];
                            let opcion = new Option(res.descripcion + " - " + res.nroCuenta.substr(-4) + " - " + res.simbolo, res.id);
                            opcion.setAttribute("nroCuenta", res.nroCuenta);
                            idBanco.add(opcion);
                        }
                        nroCuenta.value = '';

                    })
                    .catch(error => console.log('error', error));

                return false;
            }
        });

    });
</script>

<form method="POST" action="" enctype="multipart/form-data">

    <div class="text-center mb-4">
        <h4>Orden de Compra / Servicio</h4>
    </div>

    <div class="row">
        <div class="col-md-5">
            <img src="images/logo1.png" alt="">
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <input type="hidden" name="nombre" value="nombre">
                <select name="idTipoOrden" id="idTipoOrden" class="form-select" required>
                    <option value="">Seleccionar</option>

                    <?php foreach ($tipoOrden as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] . " - " . $value["descripcion"]  ?></option>
                    <?php endforeach; ?>

                </select>
            </div>

        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault1" value="1" required>
                <label class="form-check-label" for="flexRadioDefault1">
                    Anticipo
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault2" value="2" required>
                <label class="form-check-label" for="flexRadioDefault2">
                    Entrega a rendir
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault3" value="3" required>
                <label class="form-check-label" for="flexRadioDefault3">
                    Contrato
                </label>
            </div>


        </div>
    </div>


    <h5>1. Datos del solicitante</h5>

    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="idEmpresa" class="col-sm-2 col-form-label">Empresa</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="idEmpresa" id="idEmpresa" required>
                                <option value="" selected>Seleccionar</option>
                                <?php foreach ($empresas as $key => $value) : ?>
                                    <option value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="mb-3 row">
                        <label for="idKey" class="col-sm-2 col-form-label">Key</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="idKey" id="idKey" required>
                                <option value="" selected>Seleccionar</option>
                                <?php foreach ($keys as $key => $value) : ?>
                                    <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">


                    <div class="mb-3 row">
                        <label for="solicitado" class="col-sm-2 col-form-label">Solicitado por</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="solicitado" id="solicitado" required>
                                <option value="" selected>Seleccionar</option>
                                <?php foreach ($personal as $key => $value) : ?>
                                    <option value="<?php echo $value["id"] ?>"><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="mb-3 row">
                        <label for="jefe" class="col-sm-2 col-form-label">Jefe inmediato</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="jefe" id="jefe" required>
                                <option value="" selected>Seleccionar</option>
                                <?php foreach ($personal as $key => $value) : ?>
                                    <option value="<?php echo $value["id"] ?>"><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>







        </div>
        <div class="col-md-1"></div>
    </div>

    <div id="divCostos">

        <hr>
        <h5>2. Datos del contrato</h5>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <div class="mb-3 row">
                    <label for="idCentroCosto" class="col-sm-3 col-form-label">Centro de costo</label>
                    <div class="col-sm-9">
                        <select class="form-select" name="idCentroCosto" id="idCentroCosto">
                            <option value="" selected>Seleccionar</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="idCuenta3" class="col-sm-3 col-form-label">Cuenta de Nivel 3</label>
                    <div class="col-sm-9" id="divCuenta3">
                        <input type="hidden" name="idCuenta3" id="idCuenta3">



                        <input type="text" class="form form-control" id="idCuenta3_auto">

                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="idClasecosto" class="col-sm-3 col-form-label">Clase de costo</label>
                    <div class="col-sm-9">
                        <input class="form-control" name="idClaseCosto" id="idClaseCosto" disabled>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="idCuenta1" class="col-sm-3 col-form-label">Cuenta de Nivel 1</label>
                    <div class="col-sm-9">
                        <input class="form-control" name="idCuenta1" id="idCuenta1" disabled>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="idCuenta2" class="col-sm-3 col-form-label">Cuenta de Nivel 2</label>
                    <div class="col-sm-9">
                        <input class="form-control" name="idCuenta2" id="idCuenta2" disabled>

                    </div>
                </div>



            </div>
            <div class="col-md-6">
                <div id="divVariosCentro">
                    <button type="button" class="btn btn-success ms-4 mb-2 text-white" id="agregarCentroVarios">+ Centro</button>
                    <table class="table table-striped table-bordered ms-4">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Centro</th>
                                <th>Nivel 3</th>
                                <th style="width:68px">%</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyvarios">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <hr>
    <h5>3. Objeto del contrato</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="mb-3">
                <textarea class="form-control" id="objeto" name="objeto" rows="3" required></textarea>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

    <hr>
    <h5>4. Datos del contratista</h5>


    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="mb-3 row">
                <label for="ejecutado" class="col-sm-4 col-form-label">Ejecutado por</label>
                <div class="col-sm-8">
                    <input type="hidden" name="ejecutado" id="ejecutado">

                    <input type="text" id="ejecutado_com">
                    <!--   <select class="form-select" name="ejecutado_com" id="ejecutado_com" required>
                        
                    </select> -->
                </div>
            </div>
            <div class="mb-3 row">
                <label for="ruc" class="col-sm-4 col-form-label">RUC / DOI</label>
                <div class="col-sm-8">
                    <input disabled class="form-control" type="text" name="ruc" id="ruc">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3 row">
                <label for="idBanco" class="col-sm-4 col-form-label ps-5">Banco</label>
                <div class="col-sm-8">
                    <select class="form-select" name="idBanco" id="idBanco" required>
                        <option value="" selected>Seleccionar</option>
                        <?php foreach ($banco as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="nroCuenta" class="col-sm-4 col-form-label ps-5">Cuenta Nº</label>
                <div class="col-sm-8">
                    <input disabled class="form-control" type="text" name="nroCuenta" id="nroCuenta">
                </div>
            </div>
        </div>

        <div class="col-md-2"></div>
    </div>

    <hr>
    <h5>5. Detalle del contrato</h5>

    <button type="button" class="btn btn-success" id="btnAgregarDetalles"><i class="bi bi-plus"></i> Detalle</button>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="mb-3 row">

                <div class="col-sm-9">

                </div>
                <div class="col-sm-2">
                    <select class="form-select" name="idMoneda" id="idMoneda" required>
                        <option value="1">Soles</option>
                        <option value="2">Dólares</option>
                        <option value="3">Euros</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8" id="divDetalles">


            <div class="mb-3 row">

                <div class="col-sm-9">
                    <input class="form-control" type="text" name="detalle[]">
                </div>
                <div class="col-sm-2">
                    <input class="form-control inputMoneda" type="text" name="moneda[]">
                </div>
            </div>
        </div>

        <div class="col-md-2"></div>

    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="mb-3 row">

                <div class="col-sm-8">

                </div>
                <div class="col-sm-1">
                    <label class="col-form-label">Total</label>
                </div>
                <div class="col-sm-2">
                    <input class="form-control" id="totalDetalle" type="text">
                </div>
            </div>
        </div>
    </div>

    <hr>
    <h5>6. Referencia</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="mb-3 row">

                <label for="referencia" class="col-sm-2 col-form-label">Referencia</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="referencia" id="referencia" required>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="formFile" class="col-sm-2 col-form-label">Doc. Adjunto</label>
                <div class="col-sm-10">

                    <input class="form-control" name="docs[]" type="file" id="formFile" multiple>
                </div>
            </div>
            <div class="mb-3">
            </div>
        </div>

        <div class="col-md-2"></div>

    </div>

    <hr>
    <h5>7. Autorización</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="fondos" class="form-label">Disponibilidad de fondos</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="fondos" class="form-label">Autorización a continuar trámite</label>
            </div>
        </div>

        <div class="col-md-2"></div>

    </div>
    <br>
    <br>
    <br>
    <div class="text-center">
        <button type="submit" class="btn btn-lg btn-success" name="submit" value="submit">Guardar</button>

    </div>
    <br>
    <br>
    <br>
    <br>
</form>

<script>
    let keys_all = JSON.parse('<?php echo json_encode($keys) ?>');

    let total_keys = [];

    for (let i = 0; i < keys_all.length; i++) {
        let key = {};
        key.label = keys_all[i].descripcion;
        key.value = keys_all[i].id;

        key.id = keys_all[i].id;
        key.descripcion = keys_all[i].descripcion;

        total_keys.push(key);
    }

    let cuentas3 = JSON.parse('<?php echo json_encode($cuentas3) ?>');

    $("#idCentroCosto").change(function() {

        if ($(this).find("option:selected").attr("codigo") == "VariosCAD")
            $("#divVariosCentro").css("display", "block")
        else {
            $("#divVariosCentro").css("display", "none")
            let cuentas3 = "";
            let idCentro = $(this).val();

            $.ajax({
                url: "admin/oc/getAjaxClase_centro",
                type: "POST",
                dataType: "json",
                data: {
                    idCentro: idCentro
                },
                success: function(response) {     
                    console.log(response);
                }
            });
            
            $.ajax({
                url: "admin/oc/getAjaxCuenta3_centro",
                type: "POST",
                dataType: "json",
                data: {
                    idCentro: idCentro
                },
                success: function(response) {

                    console.log(response);
                    cuentas3 = response.response;
                    let total_cuentas3 = [];

                    for (let i = 0; i < cuentas3.length; i++) {
                        let cuenta3 = {};
                        cuenta3.value = cuentas3[i].c3_id;
                        cuenta3.label = cuentas3[i].c3_codigo + " - " + cuentas3[i].c3_descripcion;

                        cuenta3.c3_id = cuentas3[i].c3_id;
                        cuenta3.c3_codigo = cuentas3[i].c3_codigo;
                        cuenta3.c3_descripcion = cuentas3[i].c3_descripcion;

                        cuenta3.c2_id = cuentas3[i].c2_id;
                        cuenta3.c2_codigo = cuentas3[i].c2_codigo;
                        cuenta3.c2_descripcion = cuentas3[i].c2_descripcion;

                        cuenta3.c1_id = cuentas3[i].c1_id;
                        cuenta3.c1_codigo = cuentas3[i].c1_codigo;
                        cuenta3.c1_descripcion = cuentas3[i].c1_descripcion;

                        cuenta3.ca_id = cuentas3[i].ca_id;
                        cuenta3.ca_codigo = cuentas3[i].ca_codigo;
                        cuenta3.ca_descripcion = cuentas3[i].ca_descripcion;

                        total_cuentas3.push(cuenta3);
                    }
                    $("#divCuenta3").html('<input type="hidden" name="idCuenta3" id="idCuenta3"><input type="text" class="form form-control" id="idCuenta3_auto">');

                    $("#idCuenta3_auto").combobox({
                        source: total_cuentas3,
                        focus: function(event, ui) {
                            $("#idCuenta3_auto").val(ui.item.c3_codigo + " - " + ui.item.c3_descripcion);
                            $("#idCuenta3_auto").next().find(".custom-combobox-input").val(ui.item.c3_codigo + " - " + ui.item.c3_descripcion);
                            return false;
                        },
                        select: function(event, ui) {
                            $("#idCuenta3").val(ui.item.c3_id);
                            $("#idCuenta3_auto").val(ui.item.c3_codigo + " - " + ui.item.c3_descripcion);
                            $("#idCuenta3_auto").next().find(".custom-combobox-input").val(ui.item.c3_codigo + " - " + ui.item.c3_descripcion);
                            $("#idCuenta1").val(ui.item.c1_codigo + " - " + ui.item.c1_descripcion);
                            $("#idCuenta2").val(ui.item.c2_codigo + " - " + ui.item.c2_descripcion);
                            $("#idClaseCosto").val(ui.item.ca_codigo + " - " + ui.item.ca_descripcion);
                            return false;
                        }
                    });
                }
            });
            console.log(cuentas3);

        }
    });

    $("#agregarCentroVarios").click(function() {
        let miId = parseInt(Math.random() * 1000000);
        let fila = '<tr>';

        fila += '<td class="pe-5"><input  class="form-control" type="text" id="i_' + miId + '"><input type="hidden" name="varioskeys[]" id="l_' + miId + '"></td>';
        fila += '<td class="pe-5"><input  class="form-control" type="text" id="ci_' + miId + '"><input type="hidden" name="varioscentros[]" id="cl_' + miId + '"></td>';
        fila += '<td class="pe-5"><input  class="form-control" type="text" id="cui_' + miId + '"><input type="hidden" name="varioscuentas[]" id="cul_' + miId + '"></td>';
        fila += '<td><input class="form-control" type="text" name="porcentajecentro[]"></td>';
        fila += '<td> <button class="btn btn-danger btnEliminarCentroFila"><i class="bi bi-trash"></i></button> </td>';


        fila += '</tr>';
        $("#tbodyvarios").append(fila);

        $("#i_" + miId).combobox({
            source: total_keys,
            focus: function(event, ui) {
                $("#i_" + miId).val(ui.item.descripcion);
                $("#i_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);
                return false;
            },
            select: function(event, ui) {
                $("#l_" + miId).val(ui.item.id);
                $("#i_" + miId).val(ui.item.descripcion);
                $("#i_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);



                $.ajax({
                    url: "admin/centro/ajaxGet_key",
                    data: {
                        idKey: ui.item.id
                    },
                    type: "post",
                    dataType: "json",
                    success: function(response) {

                        let total_centro = [];

                        for (let i = 0; i < response.keys.length; i++) {
                            let key = {};
                            key.label = response.keys[i].codigo + " " + response.keys[i].descripcion;
                            key.value = response.keys[i].id;

                            key.id = response.keys[i].id;
                            key.descripcion = response.keys[i].descripcion;
                            key.codigo = response.keys[i].codigo;

                            total_centro.push(key);
                        }

                        $("#ci_" + miId).next(".custom-combobox").remove();
                        let miTr = $("#ci_" + miId).parent();
                        $("#ci_" + miId).remove();
                        miTr.prepend('<input type="text" id="ci_' + miId + '">');

                        $("#ci_" + miId).combobox({
                            source: total_centro,
                            focus: function(event, ui) {
                                $("#ci_" + miId).val(ui.item.descripcion);
                                $("#ci_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                return false;
                            },
                            select: function(event, ui) {
                                $("#cl_" + miId).val(ui.item.id);
                                $("#ci_" + miId).val(ui.item.descripcion);
                                $("#ci_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);

                                $.ajax({
                                    url: "admin/oc/getAjaxCuenta3_centro",
                                    type: "POST",
                                    dataType: "json",
                                    data: {
                                        idCentro: ui.item.id
                                    },
                                    success: function(response) {
                                        cuentas3 = response.response;
                                        let total_cuentas3 = [];

                                        for (let i = 0; i < cuentas3.length; i++) {
                                            let cuenta3 = {};
                                            cuenta3.value = cuentas3[i].c3_id;
                                            cuenta3.label = cuentas3[i].c3_codigo + " - " + cuentas3[i].c3_descripcion;

                                            cuenta3.c3_id = cuentas3[i].c3_id;
                                            cuenta3.c3_codigo = cuentas3[i].c3_codigo;
                                            cuenta3.c3_descripcion = cuentas3[i].c3_descripcion;

                                            cuenta3.c2_id = cuentas3[i].c2_id;
                                            cuenta3.c2_codigo = cuentas3[i].c2_codigo;
                                            cuenta3.c2_descripcion = cuentas3[i].c2_descripcion;

                                            cuenta3.c1_id = cuentas3[i].c1_id;
                                            cuenta3.c1_codigo = cuentas3[i].c1_codigo;
                                            cuenta3.c1_descripcion = cuentas3[i].c1_descripcion;

                                            cuenta3.ca_id = cuentas3[i].ca_id;
                                            cuenta3.ca_codigo = cuentas3[i].ca_codigo;
                                            cuenta3.ca_descripcion = cuentas3[i].ca_descripcion;

                                            total_cuentas3.push(cuenta3);
                                        }

                                        $("#cui_" + miId).next(".custom-combobox").remove();
                                        let miTr = $("#cui_" + miId).parent();
                                        $("#cui_" + miId).remove();
                                        miTr.prepend('<input type="text" id="cui_' + miId + '">');



                                        $("#cui_" + miId).combobox({
                                            source: total_cuentas3,
                                            focus: function(event, ui) {
                                                $("#cui_" + miId).val(ui.item.c3_descripcion);
                                                $("#cui_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                return false;
                                            },
                                            select: function(event, ui) {
                                                $("#cul_" + miId).val(ui.item.c3_id);
                                                $("#cui_" + miId).val(ui.item.c3_descripcion);
                                                $("#cui_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                return false;
                                            }
                                        });
                                    }
                                });

                                return false;
                            }
                        });
                    }
                });
                return false;
            }
        });

    });

    $("body").on("click", ".btnEliminarCentroFila", function() {
        $(this).parents("tr").remove();
    });

    idKey.onchange = function() {
        let idKey = this.value;

        var formdata = new FormData();
        formdata.append("idKey", idKey);

        fetch("admin/centro/ajaxGet_key", {
                method: 'POST',
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formdata,
                redirect: 'follow'
            })
            .then(response => response.json())
            .then(function(response) {
                document.querySelectorAll("#idCentroCosto option").forEach(option => option.remove());
                idCentroCosto.add(new Option("Seleccionar", ""));

                for (let i = 0; i < response.keys.length; i++) {
                    let myOption = new Option(response.keys[i].codigo + " - " + response.keys[i].descripcion, response.keys[i].id);
                    myOption.setAttribute('codigo', response.keys[i].codigo);

                    idCentroCosto.add(myOption);
                }

            })
            .catch(error => console.log('error', error));
    }

    idClaseCosto.onchange = function() {
        let idClaseCosto = this.value;

        var formdata = new FormData();
        formdata.append("idCuenta", idClaseCosto);

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
                document.querySelectorAll("#idCuenta1 option").forEach(option => option.remove());
                idCuenta1.add(new Option("Seleccionar", ""));

                for (let i = 0; i < response.response.length; i++) {
                    idCuenta1.add(new Option(response.response[i].descripcion, response.response[i].id));
                }

            })
            .catch(error => console.log('error', error));
    }

    idCuenta3.onchange = function(ee) {
        console.log(ee);
    }

    idCuenta1.onchange = function() {
        let idCuenta1 = this.value;

        var formdata = new FormData();
        formdata.append("idCuenta", idCuenta1);

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
                document.querySelectorAll("#idCuenta2 option").forEach(option => option.remove());
                idCuenta2.add(new Option("Seleccionar", ""));

                for (let i = 0; i < response.response.length; i++) {
                    let opcion = new Option(response.response[i].descripcion, response.response[i].id);

                    idCuenta2.add(opcion);
                }

            })
            .catch(error => console.log('error', error));
    }

    idCuenta2.onchange = function() {
        let idCuenta2 = this.value;

        var formdata = new FormData();
        formdata.append("idCuenta", idCuenta2);

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
                document.querySelectorAll("#idCuenta3 option").forEach(option => option.remove());
                idCuenta3.add(new Option("Seleccionar", ""));

                for (let i = 0; i < response.response.length; i++) {
                    idCuenta3.add(new Option(response.response[i].descripcion, response.response[i].id));
                }

            })
            .catch(error => console.log('error', error));
    }

    idBanco.onchange = function() {
        let nroCuenta_text = this.value;

        nroCuenta.value = this.selectedOptions[0].getAttribute('nroCuenta');
    }

    btnAgregarDetalles.onclick = function() {
        let idBtnDe = (Math.random() * 1000000).toFixed(0);

        let miDiv = document.createElement('div');
        miDiv.classList.add('mb-3');
        miDiv.classList.add('row');
        miDiv.classList.add('filaDetalle');

        miDiv.innerHTML = '<div class="col-sm-9">   <input class="form-control" type="text" name="detalle[]"></div><div class="col-sm-2">    <input class="form-control inputMoneda" type="text" name="moneda[]"></div> <div class="col-sm-1">    <button class="btn btn-danger btnBorrarDetalle" id="idBtnDe' + idBtnDe + '" value="Borrar"> <i class="bi bi-trash"></i> </button></div>';

        //divDetalles.innerHTML += '<div class="mb-3 row filaDetalle"><div class="col-sm-9">   <input class="form-control" type="text" name="detalle[]"></div><div class="col-sm-2">    <input class="form-control inputMoneda" type="number" name="moneda[]"></div> <div class="col-sm-1">    <button class="btn btn-danger btnBorrarDetalle" id="idBtnDe' + idBtnDe + '" value="Borrar"> <i class="bi bi-trash"></i> </button></div> </div>';

        divDetalles.appendChild(miDiv);


        let borrar = document.querySelectorAll(".btnBorrarDetalle");

        for (let index = 0; index < borrar.length; index++) {
            borrar[index].onclick = function() {
                borrar[index].parentElement.parentElement.remove();
                sumaTotal();
            }
        }

        let inputMonedas = document.querySelectorAll(".inputMoneda");

        inputMonedas.forEach(input => {
            input.onblur = function(ee) {
                if(isNaN(this.value.replace(/,/g,''))){
                    this.value = 0;
                }
                this.value = convFormt(this.value);;
                sumaTotal();
            }
        });


    }

    let inputMonedas = document.querySelectorAll(".inputMoneda");
  
    inputMonedas.forEach(input => {
    
        input.onblur = function() {
            if(isNaN(this.value.replace(/,/g,''))){
                this.value = 0;
            }
            this.value = convFormt(this.value);
            sumaTotal();
        }
    });

    let sumaTotal = function() {
        totalDetalle.value = 0;
        document.querySelectorAll(".inputMoneda").forEach(input2 => {
            input2.value = input2.value || 0;
            let valorInput = input2.value.replace(/,/g,'');
            let valorTotal = totalDetalle.value.replace(/,/g,'');

            totalDetalle.value = parseFloat(valorTotal) + parseFloat(valorInput);
        });

        totalDetalle.value = convFormt(totalDetalle.value);
    
    }
    let convFormt = function(number){
        return (parseFloat(number.replace(/,/g,''))).toLocaleString('en-US', {minimumFractionDigits:2});
    }

    

    idTipoOrden.onchange = function() {

        if (this.value > 5) { //desaparece
            divCostos.style.display = "none";
        } else {
            divCostos.style.display = "block";
        }
    }

  
</script>