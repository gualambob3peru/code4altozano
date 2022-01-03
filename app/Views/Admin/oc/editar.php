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

                $this = this;
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
                        select: this.options.select,
                        create: function() {
                            let miId = $this.element[0].getAttribute("id") + "_";
                            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                                return $("<li>")
                                    .attr("data-value", miId + item.value)
                                    .append(item.label)
                                    .appendTo(ul);
                            };
                        }
                    })
                    .tooltip({
                        classes: {
                            "ui-tooltip": "ui-state-highlight"
                        }
                    })

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

        let f_eje = 1;
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

                        if (f_eje) {
                            f_eje = 0;
                            $("#idBanco option[value='<?php echo $o_orden["idBanco_empresa"] ?>']").attr("selected", true);
                            $("#idBanco").change();
                        }

                    })
                    .catch(error => console.log('error', error));

                return false;
            }
        });

        $("#ejecutado_com").next().find(".ui-button").click();
        $("[data-value='ejecutado_com_" + <?php echo $o_orden["idEmpresaEje"] ?> + "']").click();

    });
</script>

<form method="POST" action="" enctype="multipart/form-data">

    <div class="text-center mb-4">
        <h4 >
        <span class="<?= (($o_orden["estado"]==1)?"bg-success":"bg-warning")?>" style="padding: 7px;">

            Orden de Compra / Servicio
        </span>
    </h4>
    </div>

    <div class="row">
        <div class="col-md-5">
            <img src="images/logo1.png" alt="" style="height:50px">
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <input type="hidden" name="nombre" value="nombre">
                <select name="idTipoOrden" id="idTipoOrden" class="form-select">
                    <option value="">Seleccionar</option>

                    <?php foreach ($tipoOrden as $key => $value) : ?>
                        <option <?= (($value["id"] == $o_orden["idTipoOrden"]) ? "selected" : "") ?> value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] . " - " . $value["descripcion"]  ?></option>
                    <?php endforeach; ?>

                </select>
            </div>

        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault1" value="1" required <?= (("1" == $o_orden["idTipoSolicitud"]) ? "checked" : "") ?>>
                <label class="form-check-label" for="flexRadioDefault1">
                    Anticipo
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault2" value="2" required <?= (("2" == $o_orden["idTipoSolicitud"]) ? "checked" : "") ?>>
                <label class="form-check-label" for="flexRadioDefault2">
                    Entrega a rendir
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault3" value="3" required <?= (("3" == $o_orden["idTipoSolicitud"]) ? "checked" : "") ?>>
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
                                <option value="">Seleccionar</option>
                                <?php foreach ($empresas as $key => $value) : ?>
                                    <option <?= (($value["id"] == $o_orden["idEmpresa"]) ? "selected" : "") ?> value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
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
                                <option value="">Seleccionar</option>
                                <?php foreach ($keys as $key => $value) : ?>
                                    <option <?= (($value["id"] == $o_key["id"]) ? "selected" : "") ?> value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
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
                                    <option <?= (($value["id"] == $o_orden["idPersonalSoli"]) ? "selected" : "") ?> value="<?php echo $value["id"] ?>"><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
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
                                    <option <?= (($value["id"] == $o_orden["idPersonalJefe"]) ? "selected" : "") ?> value="<?php echo $value["id"] ?>"><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
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
                    <button type="button" class="btn btn-success ms-4 mb-2 text-white" id="agregarCentroVarios"><i class="bi bi-plus text-white "></i> Centro</button>
                    <table class="table table-striped table-bordered ms-4">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Centro</th>
                                <th>%</th>
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
                <textarea class="form-control" id="objeto" name="objeto" rows="3" required><?php echo $o_orden["objeto"] ?></textarea>
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

    <button type="button" class="btn btn-success  text-white" id="btnAgregarDetalles"><i class="bi bi-plus text-white"></i> Detalle</button>

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
                    <input class="form-control inputMoneda" type="number" name="moneda[]">
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
                    <input class="form-control" type="text" name="referencia" id="referencia" required value="<?= $o_orden["referencia"] ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="formFile" class="col-sm-2 col-form-label">Doc. Adjunto</label>
                <div class="col-sm-10">

                    <input class="form-control" name="docs[]" type="file" id="formFile" multiple>
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-sm-12">
                    <table>

                        <?php foreach ($o_images as $key => $value) : ?>
                            <tr>
                                <td>Doc <?= ($key + 1) ?> : </td>
                                <td><a target="_blank" href="uploads/<?= $value["idOrden"] ?>/<?= $value["imagen"] ?>">Archivo</a></td>
                                <td><button elId="<?= $value["id"] ?>" class="btn btn-sm btn-danger btnModalEliImagen text-white" type="button"><i class="bi bi-trash  text-white"></i></button></td>
                            </tr>

                        <?php endforeach; ?>
                    </table>
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
        <button type="submit" class="btn btn-lg btn-success text-white" name="submit" value="submit">Guardar</button>

    </div>
    <br>
    <br>
    <br>
    <br>

    <div id="modalEliminarArchivo" title="Eliminar archivo">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>¿Desea eliminar archivo?</p>
    </div>

    <div id="msgExito" title="Eliminación exitosa">
        <p>Se eliminó con éxito</p>
    </div>
    <div id="msgError" title="Error">
        <p>No se pudo eliminar, inténtelo nuevamente</p>
    </div>
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

    let f_cue3 = 1;
    $("#idCentroCosto").change(function() {
        idClaseCosto.value = "";
        idCuenta1.value = "";
        idCuenta2.value = "";
        if ($(this).find("option:selected").attr("codigo") == "VariosCAD") {
            $("#divVariosCentro").css("display", "block");
            if (f_cue3 && $("#tbodyvarios tr").length == 0) {
                let centros = JSON.parse('<?php echo json_encode($o_centros) ?>');
                for (ind in centros) {
                    miClickCentro(centros[ind].idCentro, centros[ind].idKey, centros[ind].porcentaje);
                }
            }
        } else {
            $("#divVariosCentro").css("display", "none")
            let cuentas3 = "";
            let idCentro = $(this).val();
            $.ajax({
                url: "admin/oc/getAjaxCuenta3_centro",
                type: "POST",
                dataType: "json",
                data: {
                    idCentro: idCentro
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
                    if (f_cue3) {
                        f_cue3 = 0;
                        $("#idCuenta3_auto").next().find(".ui-button").click();
                        $("[data-value='idCuenta3_auto_" + <?php echo $o_orden["idCuenta"] ?> + "']").click();
                    }
                }
            });


        }
    });

    function miClickCentro(idCentroCosto, idKey, porcentaje) {
        let miId = parseInt(Math.random() * 1000000);
        let fila = '<tr>';

        fila += '<td class="pe-5"><input class="form-control" type="text" id="i_' + miId + '"><input type="hidden" name="varioskeys[]" id="l_' + miId + '"></td>';
        fila += '<td class="pe-5"><input class="form-control" type="text" id="ci_' + miId + '"><input type="hidden" name="varioscentros[]" id="cl_' + miId + '"></td>';
        fila += '<td><input class="form-control" type="text" name="porcentajecentro[]" value="' + porcentaje + '"></td>';
        fila += '<td> <button class="btn btn-danger btnEliminarCentroFila"><i class="bi bi-trash text-white"></i></button> </td>';


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

                                return false;
                            }
                        });

                        $("#ci_" + miId).next().find(".ui-button").click();
                        $("[data-value=ci_" + miId + "_" + idCentroCosto + "]").click();
                    }
                });
                return false;
            }
        });

        $("#i_" + miId).next().find(".ui-button").click();
        $("[data-value=i_" + miId + "_" + idKey + "]").click();
    }
    $("#agregarCentroVarios").click(function() {
        let miId = parseInt(Math.random() * 1000000);
        let fila = '<tr>';

        fila += '<td class="pe-5"><input  type="text" id="i_' + miId + '"><input type="hidden" name="varioskeys[]" id="l_' + miId + '"></td>';
        fila += '<td class="pe-5"><input  type="text" id="ci_' + miId + '"><input type="hidden" name="varioscentros[]" id="cl_' + miId + '"></td>';
        fila += '<td><input type="text" name="porcentajecentro[]"></td>';
        fila += '<td> <button class="btn btn-danger btnEliminarCentroFila"><i class="bi bi-trash text-white"></i></button> </td>';


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
    let f_ce = 1;
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
                    myOption.setAttribute('codigo',response.keys[i].codigo);

                    idCentroCosto.add(myOption);

                   
                }
                if (f_ce) {
                    $("#idCentroCosto option[value = '<?php echo $o_orden["idCentroCosto"] ?>']").attr("selected", true);
                    $("#idCentroCosto").change();
                    f_ce = 0;
                }

            })
            .catch(error => console.log('error', error));
    }
    //Iniciando datos key y centro
    idKey.onchange();


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

        miDiv.innerHTML = '<div class="col-sm-9">   <input class="form-control" type="text" name="detalle[]"></div><div class="col-sm-2">    <input class="form-control inputMoneda" type="number" name="moneda[]"></div> <div class="col-sm-1">    <button class="btn btn-danger btnBorrarDetalle" id="idBtnDe' + idBtnDe + '" value="Borrar"> <i class="bi bi-trash text-white"></i> </button></div>';

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
            input.onkeyup = function(ee) {
                sumaTotal();
            }
        });


    }

    let sumaTotal = function() {
        totalDetalle.value = 0;
        document.querySelectorAll(".inputMoneda").forEach(input2 => {
            input2.value = input2.value || 0;
            totalDetalle.value = parseFloat(totalDetalle.value) + parseFloat(input2.value);
        });
    }

    document.querySelector(".inputMoneda").onkeyup = function(ee) {
        totalDetalle.value = this.value;
    }

    idTipoOrden.onchange = function() {

        if (this.value > 5) { //desaparece
            divCostos.style.display = "none";
        } else {
            divCostos.style.display = "block";
        }
    }

    //DETALLES
    let detalles = JSON.parse('<?php echo json_encode($o_detalles) ?>');
    let cantDetalles = detalles.length - 1;
    $("[name='detalle[]']").val(detalles[0].descripcion);
    $("[name='moneda[]']").val(detalles[0].monto);

    for (let i = 1; i < detalles.length; i++) {
        $("#btnAgregarDetalles").click();
        $("[name='detalle[]']").eq(-1).val(detalles[i].descripcion);
        $("[name='moneda[]']").eq(-1).val(detalles[i].monto);
    }
    $("[name='moneda[]']").keyup();


    $("#msgExito").dialog({
        autoOpen: false,
        buttons: {
            "Aceptar": function() {
                $(this).dialog("close");
            }
        }
    });
    $("#msgError").dialog({
        autoOpen: false,
        buttons: {
            "Aceptar": function() {
                $(this).dialog("close");
            }
        }
    })



    let modalELiminarArchivo = $("#modalEliminarArchivo").dialog({
        resizable: false,
        autoOpen: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Aceptar": function() {
                $.ajax({
                    url: "admin/oc/ajaxEliminarImagen",
                    data: {
                        idOrdenImagen: function() {
                            return $("#modalEliminarArchivo").attr("elId")
                        }
                    },
                    dataType: "json",
                    type: "post",
                    error: function(ee) {
                        $("#modalEliminarArchivo").dialog("close");
                        $("#msgError").dialog("open");
                    },
                    success: function(response) {
                        if (response.response == 1) {
                            $("#modalEliminarArchivo").dialog("close");
                            $(".btnModalEliImagen[elId='" + $("#modalEliminarArchivo").attr("elId") + "']").parents("tr").remove();
                            console.log(".btnModalEliImagen[elId='" + $("#modalEliminarArchivo").attr("elId") + "']");
                            $("#msgExito").dialog("open");
                        } else {
                            $("#modalEliminarArchivo").dialog("close");
                            $("#msgError").dialog("open");
                        }
                    }

                });
            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        }
    });

    $(".btnModalEliImagen").click(function() {
        $("#modalEliminarArchivo").attr("elId", $(this).attr("elId"));
        modalELiminarArchivo.dialog('open');
    });
</script>