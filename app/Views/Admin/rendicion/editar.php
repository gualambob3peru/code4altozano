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
        padding: 7px 10px;
        width: 100%;
    }

    .no-close .ui-dialog-titlebar-close {
        display: none;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script>
    let cuentas3 = JSON.parse('<?php echo json_encode($cuentas3) ?>');
    let empresas_total = JSON.parse('<?php echo json_encode($empresas_total) ?>');
    let ordenes = JSON.parse('<?php echo json_encode($ordenes) ?>');
    let cuentasObje = new Object();

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

    let total_ordenes = [];
    for (let i = 0; i < ordenes.length; i++) {
        let orden = {};
        orden.value = ordenes[i].id;
        orden.label = ordenes[i].codigo;

        total_ordenes.push(orden);
    }


    let total_empresas = [];

    for (let i = 0; i < empresas_total.length; i++) {
        let empresa3 = {};
        empresa3.value = empresas_total[i].id;
        if (empresas_total[i].ruc == "") {
            empresa3.label = empresas_total[i].nombre;
        } else {
            empresa3.label = empresas_total[i].ruc + " - " + empresas_total[i].nombre;
        }
        empresa3.ruc = empresas_total[i].ruc;
        empresa3.nombre = empresas_total[i].nombre;

        total_empresas.push(empresa3);
    }
    $(function() {


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
                    // .attr("required", true)
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
                    // .val("")
                    .attr("title", value + " No encontrado")
                // .tooltip("open");
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

                            $("#idBanco option[value='<?php echo $o_rendicion["idBanco_empresa"] ?>']").attr("selected", true);
                            $("#idBanco").change();
                        }

                    })
                    .catch(error => console.log('error', error));

                return false;
            }
        });

        $("#ejecutado_com").next().find(".ui-button").click();
        $("[data-value='ejecutado_com_" + <?php echo $o_rendicion["idEmpresaEje"] ?> + "']").click();


        $("#idOrden_com").combobox({
            source: total_ordenes,
            focus: function(event, ui) {

                $("#idOrden_com").next().find(".custom-combobox-input").val(ui.item.label);
                return false;
            },
            select: function(event, ui) {
                $("#idOrden").val(ui.item.value);
                $("#idOrden_com").next().find(".custom-combobox-input").val(ui.item.label);


                return false;
            }
        });

        $("#idOrden_com").next().find(".ui-button").click();
        $("[data-value='idOrden_com_" + <?php echo $o_rendicion["idOrden"] ?> + "']").click();

        $(".c_tipoSoli").click(function() {
            let val = $(this).val();
            if (val == 2 || val == 4) {
                $("#divOrden").css("display", "none");
                $("#idOrden").val("");
                $("#divOrden span input").val("");
            } else {
                $("#divOrden").css("display", "block");
            }
        });

        let v_c_tipoSoli = $(".c_tipoSoli:checked").val();
        if (v_c_tipoSoli == 2 || v_c_tipoSoli == 4) {
            $("#divOrden").css("display", "none");
            $("#idOrden").val("");
            $("#divOrden span input").val("");
        } else {
            $("#divOrden").css("display", "block");
        }


        $("body").on("click", ".btnAgregarProv", function() {
            let val = $(this).parents("tr").find(".n_t_pro .custom-combobox-input").val();
            let $this = $(this);
            $.ajax({
                url: "admin/empresa/ajaxAgregarEmpresaSin",
                type: "post",
                dataType: "json",
                data: {
                    nombre: val
                },
                error: function(ee) {
                    alert('No se pudo agregar');
                },
                success: function(response) {

                    if (response.state == 5) {
                        alert('Debe agregar un nombre');
                        $this.removeClass("btn-success").addClass("btn-info");
                    } else {
                        $this.parents("tr").eq(0).find(".n_l_td_pro").val(response.id);
                        $this.removeClass("btn-info").addClass("btn-success");
                    }
                }
            });
        });
    });
</script>

<form method="POST" action="" enctype="multipart/form-data">

    <div class="text-center mt-4 mb-4">
        <h3>RENDICI??N DE GASTOS</h3>
    </div>

    <div class="row">
        <div class="col-md-5">
            <img src="images/logo1.png" alt="">
        </div>
        <div class="col-md-3">
            <div class="mb-3 pe-3">

                <select name="idTipoOrden" id="idTipoOrden" class="form-select">
                    <option value="">Seleccionar</option>

                    <?php foreach ($tipoOrden as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>" <?= (($value["id"] == $o_rendicion["idTipoOrden"]) ? "selected" : "") ?>><?php echo $value["codigo"] . " - " . $value["descripcion"]  ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3 pe-5" id="divOrden">
                <input type="hidden" name="idOrden" id="idOrden">

                <input type="text" id="idOrden_com">

            </div>

            <div class="mb-3 pe-3">
                <select class="form-select" name="idMoneda" id="idMoneda" required>
                    <option <?= ((1 == $o_rendicion["idMoneda"]) ? "selected" : "") ?> value="1">Soles</option>
                    <option <?= ((2 == $o_rendicion["idMoneda"]) ? "selected" : "") ?>  value="2">D??lares</option>
                    <option <?= ((3 == $o_rendicion["idMoneda"]) ? "selected" : "") ?>  value="3">Euros</option>
                </select>

            </div>

        </div>
        <div class="col-md-4">
            <?php foreach ($tipoSolicitudRen_all as $key => $value) : ?>
                <div class="form-check">
                    <input class="form-check-input c_tipoSoli" <?= (($value["id"] == $o_rendicion["idTipoSolicitudRen"]) ? "checked" : "") ?> type="radio" name="idTipoSolicitud" value="<?= $value["id"] ?>" required>
                    <label class="form-check-label">
                        <?= $value["descripcion"] ?>
                    </label>
                </div>
            <?php endforeach; ?>


        </div>
    </div>

    <br>
    <br>
    <h5>Datos del solicitante</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

            <div class="mb-3 row">
                <label for="idEmpresa" class="col-sm-2 col-form-label">Empresa</label>
                <div class="col-sm-10">
                    <select class="form-select" name="idEmpresa" id="idEmpresa" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($empresas as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>" <?= (($value["id"] == $o_rendicion["idEmpresa"]) ? "selected" : "") ?>><?php echo $value["nombre"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <div class="mb-3 row">
                <label for="solicitado" class="col-sm-2 col-form-label">Solicitado por</label>
                <div class="col-sm-10">
                    <select class="form-select" name="solicitado" id="solicitado" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($personal as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>" <?= (($value["id"] == $o_rendicion["idPersonalSoli"]) ? "selected" : "") ?>><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="jefe" class="col-sm-2 col-form-label">Jefe inmediato</label>
                <div class="col-sm-10">
                    <select class="form-select" name="jefe" id="jefe" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($personal as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>" <?= (($value["id"] == $o_rendicion["idPersonalJefe"]) ? "selected" : "") ?>><?php echo $value["nombres"] . " " . $value["apellidoPaterno"] . " " . $value["apellidoMaterno"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

    <div id="divCostos">

        <hr>
        <h5>Detalles</h5>
        <button type="button" class="btn btn-success" id="btnAgregarDetalle">Agregar detalle</button>
        <table class="table table-striped" style="font-size: 14px;">
            <thead>
                <tr>
                    <th>N?? Doc</th>
                    <th>Proveedor</th>
                    <th>

                    </th>
                    <th>Detalle</th>
                    <th>Varios</th>
                    <th>Key</th>
                    <th>Centro Costo</th>
                    <th>Clase</th>

                    <th>Nivel 3</th>
                    <th>-</th>
                    <th>Monto</th>
                    <th>Acci??n</th>
                </tr>
            </thead>

            <tbody id="tbodyDetalles">

            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    
                    <th>Total</th>
                    <th style="text-align: right;" id="sumaTotal"></th>
                    <td style="font-weight: bold;" id="textTipoMoneda"></td>
                </tr>
            </tfoot>
        </table>



    </div>
    <br>
    <br>
    <br>
    <hr>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="mb-3 row">
                <label for="ejecutado" class="col-sm-4 col-form-label">Ejecutado por</label>
                <div class="col-sm-8 pe-5">
                    <input type="hidden" name="ejecutado" id="ejecutado">

                    <input type="text" id="ejecutado_com">

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
                <label for="idBanco" class="col-sm-4 col-form-label">Banco</label>
                <div class="col-sm-8 ">
                    <select class="form-select" name="idBanco" id="idBanco" >
                        <option value="" selected>Seleccionar</option>
                        <?php foreach ($banco as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="nroCuenta" class="col-sm-4 col-form-label">Cuenta N??</label>
                <div class="col-sm-8">
                    <input disabled class="form-control" type="text" name="nroCuenta" id="nroCuenta">
                </div>
            </div>
        </div>

        <div class="col-md-2"></div>
    </div>

    <h5>Referencia</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="mb-3 row">

                <label for="referencia" class="col-sm-2 col-form-label">Referencia</label>
                <div class="col-sm-10">
                    <input class="form-control" type="text" name="referencia" id="referencia" value="<?= $o_rendicion["referencia"] ?>" required>
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
                                <td><a target="_blank" href="uploads/rendicion/<?= $value["idRendicion"] ?>/<?= $value["imagen"] ?>"><?= $value["imagen"] ?></a></td>
                                <td><button elId="<?= $value["id"] ?>" class="btn btn-sm btn-danger btnModalEliImagen text-white" type="button"><i class="bi bi-trash  text-white"></i></button></td>
                            </tr>

                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-2"></div>

    </div>

    <hr>
    <h5>Autorizaci??n</h5>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="fondos" class="form-label">Disponibilidad de fondos</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="fondos" class="form-label">Autorizaci??n a continuar tr??mite</label>
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

<table style="display:none">
    <tbody id="miFilaDetalle">
        <tr>
            <td>
                <input type="hidden" class="td_total" name="varioscentros_t[]" value="">
                <input type="text" name="nro[]" class="form-control" required>

            </td>
            <td class="n_t_pro pe-5">

                <input type="hidden" name="proveedor[]" class="n_l_td_pro">

                <input type="text" class="n_td_pro">

            </td>
            <td>
                <button type="button" class="btn btn-info text-white btnAgregarProv"><i class="bi bi-plus-lg"></i></button>
            </td>
            <td><input type="text" name="detalle[]" class="form-control" required></td>
            <td> <button type="button" class="btnVarios btn btn-info text-white"><i class="bi bi-journal-plus"></i></button> </td>
            <td class="n_t_key pe-5">
                <input type="text" class="n_td_ke form-control"><input type="hidden" name="varioskeys[]" class="n_l_td_ke">
            </td>
            <td class="n_t_centro pe-5">
                <input type="text" class="n_td_ce form-control"><input type="hidden" name="variosCentros[]" class="n_l_td_ce">
            </td>
            <td class="n_t_clase pe-5">
                <select class="n_clase form-select">
                    <option value="">Seleccionar</option>
                </select>

            </td>
            <td class="n_t_cuenta pe-5">
                <input type="text" class="n_td_cu form-control"><input type="hidden" name="variosCuentas[]" class="n_l_td_cu">
            </td>
            <td class="td_cuenta1">-</td>
            <td><input type="text" name="monto[]" class="form-control t_monto"></td>

            <td>
                <button type="button" class="btn btn-danger n_td_delete text-white"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    </tbody>
</table>

<table style="display: none;">
    <tbody id="cloneTrKeyCentro">
        <tr>
            <td class="t_detalle pe-5">
                <input type="text" class="td_detalle form-control l_td_detalle">
            </td>
            <td class="t_key pe-5">
                <input type="text" class="td_ke form-control"><input type="hidden" class="l_td_ke">
            </td>
            <td class="t_centro pe-5">
                <input type="text" class="td_ce  form-control"><input type="hidden" class="l_td_ce">
            </td>
            <td class="t_clase pe-5">
                <select class="l_td_clase form-select">
                    <option value="">Seleccionar</option>
                </select>
            </td>
            <td class="t_cuenta pe-5">
                <input type="text" class="td_cu  form-control"><input type="hidden" class="l_td_cu">
            </td>
            <td class="t_monto pe-5">
                <input type="text" class="form-control l_td_monto">
            </td>
            <td>

                <button type="button" class="btn btn-danger btn-sm l_td_delete"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    </tbody>
</table>

<div id="divCloneModalVarios" style="display:none">
    <div class="modalVarios" title="Varios Centros" style="font-size: 14px;">
        <div>
            <button type="button" class="btn btn-info btn-sm btnAgregarCentro"><i class="bi bi-plus-lg"></i></button>
        </div>
        <div class="tablaCentro">
            <table class="table table-bordered">

                <thead>
                    <th>Detalle</th>
                    <th>Key</th>
                    <th>Centro</th>
                    <th>Clase</th>
                    <th>Nivel 3</th>
                    <th>Monto S./</th>
                    <th>Acci??n</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalEliminarArchivo" title="Eliminar archivo">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>??Desea eliminar archivo?</p>
</div>

<div id="msgExito" title="Eliminaci??n exitosa">
    <p>Se elimin?? con ??xito</p>
</div>
<div id="msgError" title="Error">
    <p>No se pudo eliminar, int??ntelo nuevamente</p>
</div>

<script>
    $(function() {

        $("body").on("keyup","#tbodyDetalles .t_monto,.ui-dialog .l_td_monto",function(){
            let suma = 0;
            $("#tbodyDetalles .t_monto,.ui-dialog .l_td_monto").each(function(){
                let cant = 0;
                if($(this).val()!="") {
                    suma += parseFloat($(this).val());
                }
            });
     
            $("#sumaTotal").text(suma.toFixed(2))
        });
        

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
        let items = JSON.parse('<?php echo json_encode($o_items) ?>');




        $("#btnAgregarDetalle").click(function() {
            let miFila = $("#miFilaDetalle").html();
            let miId = parseInt(Math.random() * 1000000);
            miFila = $(miFila).attr("id", "fila_m_" + miId);
            $(miFila).attr("elId", miId);
            $(miFila).find(".btnVarios").attr("elId", miId);



            let modal = $("#divCloneModalVarios").html();
            modal = $(modal).attr("id", "m_" + miId)
            $(modal).find(".btnAgregarCentro").attr("elId", miId)
            $("body").append($(modal));

            $("#m_" + miId).dialog({
                autoOpen: false,
                closeOnEscape: false,
                modal: true,
                width: 1000,
                dialogClass: "no-close",
                buttons: {
                    "Aceptar": function() {
                        let cuentas3 = $(this).find(".l_td_cu"),
                            cecos = $(this).find(".l_td_ce"),
                            keys = $(this).find(".l_td_ke"),
                            montos = $(this).find(".l_td_monto"),
                            detalles = $(this).find(".l_td_detalle"),
                            arr_centros = [];

                        for (let i = 0; i < cuentas3.length; i++) {
                            if (cuentas3.eq(i).val() != "") {
                                let cu3 = $(cuentas3.eq(i)).val(),
                                    ceco = $(cecos.eq(i)).val(),
                                    key = $(keys.eq(i)).val(),
                                    monto = $(montos.eq(i)).val(),
                                    detalle = $(detalles.eq(i)).val();

                                mifCeco = new Object();

                                mifCeco.key = key;
                                mifCeco.centro = ceco;
                                mifCeco.cuenta3 = cu3;
                                mifCeco.monto = monto;
                                mifCeco.detalle = detalle;

                                arr_centros.push(mifCeco);
                            }
                        }



                        $("#fila_m_" + miId).find(".td_total").val(JSON.stringify(arr_centros));
                        $(this).dialog("close");
                    }
                }
            });

            $(miFila).find(".n_td_pro").attr("id", "n_pro_" + miId);
            $(miFila).find(".n_l_td_pro").attr("id", "n_l_pro_" + miId);

            $(miFila).find(".n_td_ke").attr("id", "n_ke_" + miId);
            $(miFila).find(".n_l_td_ke").attr("id", "n_l_ke_" + miId);

            $(miFila).find(".n_td_ce").attr("id", "n_ce_" + miId);
            $(miFila).find(".n_l_td_ce").attr("id", "n_l_ce_" + miId);

            $(miFila).find(".n_clase").attr("id", "n_clase_" + miId);

            $(miFila).find(".n_td_cu").attr("id", "n_cu_" + miId);
            $(miFila).find(".n_l_td_cu").attr("id", "n_l_cu_" + miId);
            $("#tbodyDetalles").append(miFila);

            $("#n_pro_" + miId).combobox({
                source: total_empresas,
                focus: function(event, ui) {
                    if (ui.item.ruc == "") {
                        $("#n_pro_" + miId).val(ui.item.nombre);
                        $("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.nombre);
                    } else {
                        $("#n_pro_" + miId).val(ui.item.ruc + " - " + ui.item.nombre);
                        $("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);
                    }
                    // $("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);
                    return false;
                },
                select: function(event, ui) {
                    $("#n_l_pro_" + miId).val(ui.item.value);

                    if (ui.item.ruc == "") {
                        $("#n_pro_" + miId).val(ui.item.nombre);
                        $("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.nombre);
                    } else {
                        $("#n_pro_" + miId).val(ui.item.ruc + " - " + ui.item.nombre);
                        $("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);
                    }


                    //$("#n_pro_" + miId).next().find(".custom-combobox-input").val(ui.item.ruc + " - " + ui.item.nombre);

                    return false;
                }
            });

            $("#n_ke_" + miId).combobox({
                source: total_keys,
                focus: function(event, ui) {
                    $("#n_ke_" + miId).val(ui.item.descripcion);
                    $("#n_ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);
                    return false;
                },
                select: function(event, ui) {
                    $("#n_l_ke_" + miId).val(ui.item.id);
                    $("#n_ke_" + miId).val(ui.item.descripcion);
                    $("#n_ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);



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

                            $("#n_ce_" + miId).next(".custom-combobox").remove();
                            let miTr = $("#n_ce_" + miId).parent();
                            $("#n_ce_" + miId).remove();
                            miTr.prepend('<input type="text" id="n_ce_' + miId + '">');

                            $("#n_ce_" + miId).combobox({
                                source: total_centro,
                                focus: function(event, ui) {
                                    $("#n_ce_" + miId).val(ui.item.descripcion);
                                    $("#n_ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                    return false;
                                },
                                select: function(event, ui) {
                                    $("#n_l_ce_" + miId).val(ui.item.id);
                                    $("#n_ce_" + miId).val(ui.item.descripcion);
                                    $("#n_ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);

                                    let idCentro = ui.item.id;
                                    $.ajax({
                                        url: "admin/oc/getAjaxClase_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: idCentro
                                        },
                                        success: function(response) {

                                            //agregando el combo clase de costo
                                            let clases = response.respuesta;
                                            let options = '<option value="">Seleccionar</option>';

                                            for (let i = 0; i < clases.length; i++) {
                                                options += '<option value="' + clases[i]["ca_id"] + '">' + clases[i]["ca_codigo"] + ' - ' + clases[i]["ca_descripcion"] + '</option>';

                                            }
                                            $("#n_clase_" + miId).html(options);

                                            $("#n_clase_" + miId).change(function() {

                                                $.ajax({
                                                    url: "admin/oc/getAjaxCuenta3_centro_clase",
                                                    type: "POST",
                                                    dataType: "json",
                                                    data: {
                                                        idCentro: idCentro,
                                                        idClaseCosto: $(this).val(),
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


                                                        $("#n_cu_" + miId).next(".custom-combobox").remove();
                                                        let miTr = $("#n_cu_" + miId).parent();
                                                        $("#n_cu_" + miId).remove();
                                                        miTr.prepend('<input type="text" id="n_cu_' + miId + '">');



                                                        $("#n_cu_" + miId).combobox({
                                                            source: total_cuentas3,
                                                            focus: function(event, ui) {
                                                                $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            },
                                                            select: function(event, ui) {
                                                                $("#n_l_cu_" + miId).val(ui.item.c3_id);
                                                                $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                            return false;


                                        }
                                    });
                                    /* $.ajax({
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

                                             $("#n_cu_" + miId).next(".custom-combobox").remove();
                                             let miTr = $("#n_cu_" + miId).parent();
                                             $("#n_cu_" + miId).remove();
                                             miTr.prepend('<input type="text" id="n_cu_' + miId + '">');



                                             $("#n_cu_" + miId).combobox({
                                                 source: total_cuentas3,
                                                 focus: function(event, ui) {
                                                     $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                     $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                     return false;
                                                 },
                                                 select: function(event, ui) {
                                                     $("#n_l_cu_" + miId).val(ui.item.c3_id);
                                                     $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                     $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                     return false;
                                                 }
                                             });
                                         }
                                     });*/
                                    return false;
                                }
                            });
                        }
                    });
                    return false;
                }
            });

        });

        function miclickCentro(id, detalle, idKey, idCentro, idClaseCosto, idCuenta) {
            let miId = id;

            $("#n_ke_" + miId).next(".custom-combobox").remove();
            let miTr = $("#n_ke_" + miId).parent();
            $("#n_ke_" + miId).remove();
            miTr.prepend('<input type="text" id="n_ke_' + miId + '">');


            $("#n_ke_" + miId).combobox({
                source: total_keys,
                focus: function(event, ui) {
                    $("#n_ke_" + miId).val(ui.item.descripcion);
                    $("#n_ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);
                    return false;
                },
                select: function(event, ui) {
                    $("#n_l_ke_" + miId).val(ui.item.id);
                    $("#n_ke_" + miId).val(ui.item.descripcion);
                    $("#n_ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);



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

                            $("#n_ce_" + miId).next(".custom-combobox").remove();
                            let miTr = $("#n_ce_" + miId).parent();
                            $("#n_ce_" + miId).remove();
                            miTr.prepend('<input type="text" id="n_ce_' + miId + '">');

                            $("#n_ce_" + miId).combobox({
                                source: total_centro,
                                focus: function(event, ui) {
                                    $("#n_ce_" + miId).val(ui.item.descripcion);
                                    $("#n_ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                    return false;
                                },
                                select: function(event, ui) {
                                    $("#n_l_ce_" + miId).val(ui.item.id);
                                    $("#n_ce_" + miId).val(ui.item.descripcion);
                                    $("#n_ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);

                                    let idCentro = ui.item.id;
                                    $.ajax({
                                        url: "admin/oc/getAjaxClase_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: idCentro
                                        },
                                        success: function(response) {

                                            //agregando el combo clase de costo
                                            let clases = response.respuesta;
                                            let options = '<option value="">Seleccionar</option>';

                                            for (let i = 0; i < clases.length; i++) {
                                                options += '<option value="' + clases[i]["ca_id"] + '">' + clases[i]["ca_codigo"] + ' - ' + clases[i]["ca_descripcion"] + '</option>';

                                            }

                                            $("#n_clase_" + miId).html(options);

                                            $("#n_clase_" + miId).change(function() {

                                                $.ajax({
                                                    url: "admin/oc/getAjaxCuenta3_centro_clase",
                                                    type: "POST",
                                                    dataType: "json",
                                                    data: {
                                                        idCentro: idCentro,
                                                        idClaseCosto: $(this).val(),
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


                                                        $("#n_cu_" + miId).next(".custom-combobox").remove();
                                                        let miTr = $("#n_cu_" + miId).parent();
                                                        $("#n_cu_" + miId).remove();
                                                        miTr.prepend('<input type="text" id="n_cu_' + miId + '">');



                                                        $("#n_cu_" + miId).combobox({
                                                            source: total_cuentas3,
                                                            focus: function(event, ui) {
                                                                $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            },
                                                            select: function(event, ui) {
                                                                $("#n_l_cu_" + miId).val(ui.item.c3_id);
                                                                $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            }
                                                        });

                                                        $("#n_cu_" + miId).next().find(".ui-button").click();
                                                        $("[data-value=n_cu_" + miId + "_" + idCuenta + "]").click();
                                                    }
                                                });
                                            });

                                            $("#n_clase_" + miId + " option[value='" + idClaseCosto + "']").attr("selected", true);
                                            $("#n_clase_" + miId).change();
                                            return false;


                                        }
                                    });

                                    /*$.ajax({
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

                                            $("#n_cu_" + miId).next(".custom-combobox").remove();
                                            let miTr = $("#n_cu_" + miId).parent();
                                            $("#n_cu_" + miId).remove();
                                            miTr.prepend('<input type="text" id="n_cu_' + miId + '">');



                                            $("#n_cu_" + miId).combobox({
                                                source: total_cuentas3,
                                                focus: function(event, ui) {
                                                    $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                    return false;
                                                },
                                                select: function(event, ui) {
                                                    $("#n_l_cu_" + miId).val(ui.item.c3_id);
                                                    $("#n_cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#n_cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                    return false;
                                                }
                                            });

                                            $("#n_cu_" + miId).next().find(".ui-button").click();
                                            $("[data-value=n_cu_" + miId + "_" + idCuenta + "]").click();

                                        }
                                    });*/
                                    return false;
                                }
                            });

                            $("#n_ce_" + miId).next().find(".ui-button").click();
                            $("[data-value=n_ce_" + miId + "_" + idCentro + "]").click();

                        }
                    });
                    return false;
                }
            });

            $("#n_ke_" + miId).next().find(".ui-button").click();
            $("[data-value=n_ke_" + miId + "_" + idKey + "]").click();
        }



        function miclickModalCentro(idModal, detalle, idKey, idCentro, idClaseCosto, idCuenta, monto, ultimo) {

            let miId = parseInt(Math.random() * 1000000);
            let modalId = idModal;

            let fila = $("#cloneTrKeyCentro").html();
            fila = $(fila).attr("id", "fila_" + miId);

            $(fila).find(".td_ke").attr("id", "ke_" + miId);
            $(fila).find(".l_td_ke").attr("id", "l_ke_" + miId);

            $(fila).find(".td_ce").attr("id", "ce_" + miId);
            $(fila).find(".l_td_ce").attr("id", "l_ce_" + miId);

            $(fila).find(".l_td_clase").attr("id", "l_clase_" + miId);



            $(fila).find(".td_cu").attr("id", "cu_" + miId);
            $(fila).find(".l_td_cu").attr("id", "l_cu_" + miId);

            $(fila).find(".l_td_detalle").val(detalle);
            $(fila).find(".l_td_monto").val(monto);



            $("#m_" + modalId).find("tbody").append($(fila));

            $("#ke_" + miId).next(".custom-combobox").remove();
            let miTr = $("#ke_" + miId).parent();
            $("#ke_" + miId).remove();
            miTr.prepend('<input type="text" id="ke_' + miId + '">');


            $("#m_" + modalId).dialog("open");
            miTr.parent().attr("nuevo", "1");
            if (ultimo == 1) {
                miTr.parent().attr("ultimo", "1");
            }

            $("#ke_" + miId).combobox({
                source: total_keys,
                focus: function(event, ui) {
                    $("#ke_" + miId).val(ui.item.descripcion);
                    $("#ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);
                    return false;
                },
                select: function(event, ui) {
                    $("#l_ke_" + miId).val(ui.item.id);
                    $("#ke_" + miId).val(ui.item.descripcion);
                    $("#ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);



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

                            $("#ce_" + miId).next(".custom-combobox").remove();
                            let miTr = $("#ce_" + miId).parent();
                            $("#ce_" + miId).remove();
                            miTr.prepend('<input type="text" id="ce_' + miId + '">');

                            $("#ce_" + miId).combobox({
                                source: total_centro,
                                focus: function(event, ui) {
                                    $("#ce_" + miId).val(ui.item.descripcion);
                                    $("#ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                    return false;
                                },
                                select: function(event, ui) {
                                    $("#l_ce_" + miId).val(ui.item.id);
                                    $("#ce_" + miId).val(ui.item.descripcion);
                                    $("#ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);

                                    let idCentro = ui.item.id;
                                    $.ajax({
                                        url: "admin/oc/getAjaxClase_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: idCentro
                                        },
                                        success: function(response) {

                                            //agregando el combo clase de costo
                                            let clases = response.respuesta;
                                            let options = '<option value="">Seleccionar</option>';

                                            for (let i = 0; i < clases.length; i++) {
                                                options += '<option value="' + clases[i]["ca_id"] + '">' + clases[i]["ca_codigo"] + ' - ' + clases[i]["ca_descripcion"] + '</option>';

                                            }
                                            $("#l_clase_" + miId).html(options);

                                            $("#l_clase_" + miId).change(function() {

                                                $.ajax({
                                                    url: "admin/oc/getAjaxCuenta3_centro_clase",
                                                    type: "POST",
                                                    dataType: "json",
                                                    data: {
                                                        idCentro: idCentro,
                                                        idClaseCosto: $(this).val(),
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


                                                        $("#cu_" + miId).next(".custom-combobox").remove();
                                                        let miTr = $("#cu_" + miId).parent();
                                                        $("#cu_" + miId).remove();
                                                        miTr.prepend('<input type="text" id="cu_' + miId + '">');


                                                        $("#cu_" + miId).combobox({
                                                            source: total_cuentas3,
                                                            focus: function(event, ui) {
                                                                $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            },
                                                            select: function(event, ui) {
                                                                $("#l_cu_" + miId).val(ui.item.c3_id);
                                                                $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            }
                                                        });
                                                        if (miTr.parent().attr("nuevo") == "1") {

                                                            $("#cu_" + miId).next().find(".ui-button").click();
                                                            $("[data-value=cu_" + miId + "_" + idCuenta + "]").click();
                                                            if (miTr.parent().attr("ultimo") == "1") {
                                                                miTr.parent().attr("ultimo", "0");
                                                                $("#m_" + modalId).parent().find(".ui-dialog-buttonset button").click();
                                                            }
                                                        }
                                                    }
                                                });
                                            });
                                            console.log(idClaseCosto);
                                            console.log("#l_clase_" + miId);
                                            $("#l_clase_" + miId + " option[value='" + idClaseCosto + "']").attr("selected", true);
                                            $("#l_clase_" + miId).change();


                                            return false;


                                        }
                                    });

                                    /*$.ajax({
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

                                            $("#cu_" + miId).next(".custom-combobox").remove();
                                            let miTr = $("#cu_" + miId).parent();
                                            $("#cu_" + miId).remove();
                                            miTr.prepend('<input type="text" id="cu_' + miId + '">');



                                            $("#cu_" + miId).combobox({
                                                source: total_cuentas3,
                                                focus: function(event, ui) {
                                                    $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                    return false;
                                                },
                                                select: function(event, ui) {
                                                    $("#l_cu_" + miId).val(ui.item.c3_id);
                                                    $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);

                                                    miTr.parent().attr("nuevo", "0");


                                                    return false;
                                                }
                                            });

                                            if (miTr.parent().attr("nuevo") == "1") {

                                                $("#cu_" + miId).next().find(".ui-button").click();
                                                $("[data-value=cu_" + miId + "_" + idCuenta + "]").click();
                                                if (miTr.parent().attr("ultimo") == "1") {
                                                    miTr.parent().attr("ultimo", "0");
                                                    $("#m_" + modalId).parent().find(".ui-dialog-buttonset button").click();
                                                }
                                            }
                                        }
                                    });*/
                                    return false;
                                }
                            });
                            if (miTr.parent().attr("nuevo") == "1") {
                                $("#ce_" + miId).next().find(".ui-button").click();
                                $("[data-value=ce_" + miId + "_" + idCentro + "]").click();
                            }
                        }
                    });
                    return false;
                }
            });
            if (miTr.parent().attr("nuevo") == "1") {
                $("#ke_" + miId).next().find(".ui-button").click();
                $("[data-value=ke_" + miId + "_" + idKey + "]").click();
            }
        }

        $("body").on("click", ".btnVarios", function() {
            let elId = $(this).attr("elId");

            $("#m_" + elId).dialog("open");
        });

        $("body").on("click", ".btnAgregarCentro", function() {
            let miId = parseInt(Math.random() * 1000000);
            let modalId = $(this).attr("elId");

            let fila = $("#cloneTrKeyCentro").html();
            fila = $(fila).attr("id", "fila_" + miId);

            $(fila).find(".td_ke").attr("id", "ke_" + miId);
            $(fila).find(".l_td_ke").attr("id", "l_ke_" + miId);

            $(fila).find(".td_ce").attr("id", "ce_" + miId);
            $(fila).find(".l_td_ce").attr("id", "l_ce_" + miId);

            $(fila).find(".l_td_clase").attr("id", "l_clase_" + miId);

            $(fila).find(".td_cu").attr("id", "cu_" + miId);
            $(fila).find(".l_td_cu").attr("id", "l_cu_" + miId);

            $("#m_" + modalId).find("tbody").append($(fila));

            $("#ke_" + miId).combobox({
                source: total_keys,
                focus: function(event, ui) {
                    $("#ke_" + miId).val(ui.item.descripcion);
                    $("#ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);
                    return false;
                },
                select: function(event, ui) {
                    $("#l_ke_" + miId).val(ui.item.id);
                    $("#ke_" + miId).val(ui.item.descripcion);
                    $("#ke_" + miId).next().find(".custom-combobox-input").val(ui.item.descripcion);



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

                            $("#ce_" + miId).next(".custom-combobox").remove();
                            let miTr = $("#ce_" + miId).parent();
                            $("#ce_" + miId).remove();
                            miTr.prepend('<input type="text" id="ce_' + miId + '">');

                            $("#ce_" + miId).combobox({
                                source: total_centro,
                                focus: function(event, ui) {
                                    $("#ce_" + miId).val(ui.item.descripcion);
                                    $("#ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                    return false;
                                },
                                select: function(event, ui) {
                                    $("#l_ce_" + miId).val(ui.item.id);
                                    $("#ce_" + miId).val(ui.item.descripcion);
                                    $("#ce_" + miId).next().find(".custom-combobox-input").val(ui.item.codigo + " " + ui.item.descripcion);
                                    let idCentro = ui.item.id;
                                    $.ajax({
                                        url: "admin/oc/getAjaxClase_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: idCentro
                                        },
                                        success: function(response) {

                                            //agregando el combo clase de costo
                                            let clases = response.respuesta;
                                            let options = '<option value="">Seleccionar</option>';

                                            for (let i = 0; i < clases.length; i++) {
                                                options += '<option value="' + clases[i]["ca_id"] + '">' + clases[i]["ca_codigo"] + ' - ' + clases[i]["ca_descripcion"] + '</option>';

                                            }
                                            $("#l_clase_" + miId).html(options);

                                            $("#l_clase_" + miId).change(function() {

                                                $.ajax({
                                                    url: "admin/oc/getAjaxCuenta3_centro_clase",
                                                    type: "POST",
                                                    dataType: "json",
                                                    data: {
                                                        idCentro: idCentro,
                                                        idClaseCosto: $(this).val(),
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


                                                        $("#cu_" + miId).next(".custom-combobox").remove();
                                                        let miTr = $("#cu_" + miId).parent();
                                                        $("#cu_" + miId).remove();
                                                        miTr.prepend('<input type="text" id="cu_' + miId + '">');


                                                        $("#cu_" + miId).combobox({
                                                            source: total_cuentas3,
                                                            focus: function(event, ui) {
                                                                $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            },
                                                            select: function(event, ui) {
                                                                $("#l_cu_" + miId).val(ui.item.c3_id);
                                                                $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                                $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                                return false;
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                            return false;


                                        }
                                    });
                                    /*$.ajax({
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

                                            $("#cu_" + miId).next(".custom-combobox").remove();
                                            let miTr = $("#cu_" + miId).parent();
                                            $("#cu_" + miId).remove();
                                            miTr.prepend('<input type="text" id="cu_' + miId + '">');



                                            $("#cu_" + miId).combobox({
                                                source: total_cuentas3,
                                                focus: function(event, ui) {
                                                    $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                    return false;
                                                },
                                                select: function(event, ui) {
                                                    $("#l_cu_" + miId).val(ui.item.c3_id);
                                                    $("#cu_" + miId).val(ui.item.c3_descripcion);
                                                    $("#cu_" + miId).next().find(".custom-combobox-input").val(ui.item.c3_codigo + " " + ui.item.c3_descripcion);
                                                    return false;
                                                }
                                            });
                                        }
                                    });*/
                                    return false;
                                }
                            });
                        }
                    });
                    return false;
                }
            });


        });


        //agregando items
        for (let ind in items) {
            console.log(items[ind]);
            $("#btnAgregarDetalle").click();
            let miTr = $("#tbodyDetalles").find("tr").last();



            miTr.find("[name='nro[]']").val(items[ind].nroDoc);
            miTr.find("[name='varioscentros_t[]']").val(JSON.stringify(items[ind].centros));
            miTr.find("[name='detalle[]']").val(items[ind].detalle);
            miTr.find("[name='monto[]']").val(items[ind].monto);

            //miTr.find("[name='proveedor[]'] option[value='"+items[ind].idEmpresaProv+"']").attr("selected",true)

            $("#n_pro_" + miTr.attr("elId")).next().find(".ui-button").click();
            $("[data-value=n_pro_" + miTr.attr("elId") + "_" + items[ind].idEmpresaProv + "]").click();


            if (items[ind].idCentro != "0") {
                miclickCentro(miTr.attr("elId"), items[ind].detalle, items[ind].idKey, items[ind].idCentro, items[ind].cc_id, items[ind].idCuenta);
            } else {
                let centros = items[ind].centros;

                for (let ind in centros) {
                    if (ind == centros.length - 1) {
                        miclickModalCentro(miTr.attr("elId"), centros[ind].detalle, centros[ind].idKey, centros[ind].idCentro, centros[ind].cc_id, centros[ind].idCuenta, centros[ind].monto, 1);
                    } else {
                        miclickModalCentro(miTr.attr("elId"), centros[ind].detalle, centros[ind].idKey, centros[ind].idCentro, centros[ind].cc_id, centros[ind].idCuenta, centros[ind].monto, 0);
                    }
                }

            }
        }

        $("body").on("click", ".n_td_delete", function() {
            $(this).parents("tr").eq(0).remove();
        })

        $("body").on("click", ".l_td_delete", function() {
            $(this).parents("tr").eq(0).remove();
        })

        idBanco.onchange = function() {
            let nroCuenta_text = this.value;

            nroCuenta.value = this.selectedOptions[0].getAttribute('nroCuenta');
        }

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
                        url: "admin/rendicion/ajaxEliminarImagen",
                        data: {
                            idRendicionImagen: function() {
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

        $("#tbodyDetalles .t_monto").keyup();

        $("#idMoneda").change(function(){
            let val = $(this).val();

            if(val=="1"){
                $("#textTipoMoneda").text("S/.");
            }else if(val=="2"){
                $("#textTipoMoneda").text("USD");
            }else if(val=="3"){
                $("#textTipoMoneda").text("EUROS");
            }else{
                $("#textTipoMoneda").text("S/.");
            }
        });

        $("#idMoneda").change();        

    });
</script>