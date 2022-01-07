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
    .no-close .ui-dialog-titlebar-close {
  display: none;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script>
    $(function() {

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

        let total_empresas = [];

        for (let i = 0; i < empresas_total.length; i++) {
            let empresa3 = {};
            empresa3.value = empresas_total[i].id;
            empresa3.label = empresas_total[i].ruc + " - " + empresas_total[i].nombre;
            empresa3.ruc = empresas_total[i].ruc;
            empresa3.nombre = empresas_total[i].nombre;

            total_empresas.push(empresa3);
        }

        let total_ordenes = [];
        for (let i = 0; i < ordenes.length; i++) {
            let orden = {};
            orden.value = ordenes[i].id;
            orden.label = ordenes[i].codigo;

            total_ordenes.push(orden);
        }

        $.widget("custom.combobox", {
            _create: function() {
                console.log(this);
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

    });
</script>

<form method="POST"  action="" enctype="multipart/form-data">

    <div class="text-center mt-4 mb-4">
        <h3>RENDICIÓN DE GASTOS</h3>
    </div>

    <div class="row">
        <div class="col-md-5">
            <img src="images/logo1.png" alt="">
        </div>
        <div class="col-md-3">
            <div class="mb-3 pe-3">
                <input type="hidden" name="nombre" value="nombre">
                <select name="idTipoOrden" id="idTipoOrden" class="form-select" required>
                    <option value="">Seleccionar</option>

                    <?php foreach ($tipoOrden as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] . " - " . $value["descripcion"]  ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3 pe-5">
                <input type="hidden" name="idOrden" id="idOrden">

                <input type="text" id="idOrden_com">

                <!-- <input type="hidden" name="otro" value="idOrden">

                <select name="idOrden" id="idOrden" class="form-select">
                    <option value="">Seleccionar Orden</option>
                    <?php foreach ($ordenes as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] ?></option>
                    <?php endforeach; ?>

                </select> -->
            </div>
            <!-- <div class="mb-3">
                <input class="form-control" type="date" name="fecha" placeholder="Fecha" required>
            </div> -->
            <!--  <div class="mb-3">

                <input class="form-control" type="text" name="numero" placeholder="Número">
            </div> -->
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault1" checked value="1" required>
                <label class="form-check-label" for="flexRadioDefault1">
                    OC / Entrega a rendir
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault2" value="2" required>
                <label class="form-check-label" for="flexRadioDefault2">
                    Caja chica
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault3" value="3" required>
                <label class="form-check-label" for="flexRadioDefault3">
                    Reembolso
                </label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="idTipoSolicitud" id="flexRadioDefault3" value="4" required>
                <label class="form-check-label" for="flexRadioDefault3">
                    Tarjeta de crédito
                </label>
            </div>
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
                        <option value="" selected>Seleccionar</option>
                        <?php foreach ($empresas as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


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
        <div class="col-md-2"></div>
    </div>

    <div id="divCostos">

        <hr>
        <h5>Detalles</h5>
        <button type="button" class="btn btn-success" id="btnAgregarDetalle">Agregar detalle</button>
        <table class="table table-striped" style="font-size: 14px;">
            <thead>
                <tr>
                    <th>Nº Doc</th>
                    <th>Proveedor</th>
                    <th>Detalle</th>
                    <th>Varios</th>
                    <th>Key</th>
                    <th>Centro Costo</th>

                    <th>Nivel 3</th>
                    <th>Nivel 1</th>
                    <th>Monto</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody id="tbodyDetalles">

            </tbody>
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
                <label for="idBanco" class="col-sm-4 col-form-label">Banco</label>
                <div class="col-sm-8 ">
                    <select class="form-select" name="idBanco" id="idBanco" required>
                        <option value="" selected>Seleccionar</option>
                        <?php foreach ($banco as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="nroCuenta" class="col-sm-4 col-form-label">Cuenta Nº</label>
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
    <h5>Autorización</h5>

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

<table style="display:none">
            <tbody id="miFilaDetalle">
                <tr>
                    <td>
                        <input type="hidden" class="td_total" name="varioscentros_t[]" value="">
                        <input type="text" name="nro[]" class="form-control">

                    </td>
                    <td class="n_t_prov pe-5">
                    <input type="text" class="n_td_prov form-control">
                    <input type="hidden" name="proveedor[]" class="n_l_td_prov">

                       <!--  <select name="proveedor[]"  class="form-select">
                            <option value="" selected>Seleccionar</option>
                            <?php foreach ($empresas_total as $key => $value) : ?>
                                <option value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
                            <?php endforeach; ?>
                        </select> -->
                    </td>
                    <td><input type="text" name="detalle[]"  class="form-control"></td>
                    <td> <button type="button" class="btnVarios btn btn-info"><i class="bi bi-journal-plus"></i></button> </td>
                    <td class="n_t_key pe-5">
                        <input type="text" class="n_td_ke form-control"><input type="hidden" name="varioskeys[]" class="n_l_td_ke">
                    </td>
                    <td class="n_t_centro pe-5">
                        <input type="text" class="n_td_ce form-control" ><input type="hidden" name="variosCentros[]" class="n_l_td_ce">
                    </td>
                    <td class="n_t_cuenta pe-5">
                        <input type="text" class="n_td_cu form-control"><input type="hidden" name="variosCuentas[]" class="n_l_td_cu">
                    </td>
                    <td class="td_cuenta1">-</td>
                    <td><input type="text" name="monto[]" class="form-control"></td>

                    <td>
                        <button class="btn btn-danger n_td_delete"><i class="bi bi-trash"></i></button>
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
            <td class="t_cuenta pe-5">
                <input type="text" class="td_cu  form-control"><input type="hidden" class="l_td_cu">
            </td>
            <td class="t_monto pe-5">
                <input type="text" class="form-control l_td_monto">
            </td>
            <td>

                <button class="btn btn-danger btn-sm l_td_delete"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    </tbody>
</table>

<div id="divCloneModalVarios" style="display:none">
    <div class="modalVarios" title="Varios Centros" style="font-size: 14px;">
        <div>
            <button type="button" class="btn btn-info btn-sm btnAgregarCentro"><i class="bi bi-plus-lg"></i></button>
        </div>
        <div id="tablaCentro">
            <table class="table table-bordered">

                <thead>
                    <th>Detalle</th>
                    <th>Key</th>
                    <th>Centro</th>
                    <th>Nivel 3</th>
                    <th>Monto S./</th>
                    <th>Acción</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function(){

        let keys_all = JSON.parse('<?php echo json_encode($keys) ?>');
        let proveedor_all = JSON.parse('<?php echo json_encode($empresas_total) ?>');
    
        let total_keys = [];
    
        for (let i = 0; i < keys_all.length; i++) {
            let key = {};
            key.label = keys_all[i].descripcion;
            key.value = keys_all[i].id;
    
            key.id = keys_all[i].id;
            key.descripcion = keys_all[i].descripcion;
    
            total_keys.push(key);
        }

        let total_proveedor = [];
        for (let i = 0; i < proveedor_all.length; i++) {
            let proveedor = {};
            proveedor.label = proveedor_all[i].nombre;
            proveedor.value = proveedor_all[i].id;
    
            proveedor.id = proveedor_all[i].id;
            proveedor.nombre = proveedor_all[i].nombre;
    
            total_proveedor.push(proveedor);
        }
    
        let cuentas3 = JSON.parse('<?php echo json_encode($cuentas3) ?>');
    
    
    
    
        $("#btnAgregarDetalle").click(function() {
            let miFila = $("#miFilaDetalle").html();
            let miId = parseInt(Math.random() * 1000000);
            miFila = $(miFila).attr("id", "fila_m_" + miId);
            $(miFila).attr("elId", miId);
            $(miFila).find(".btnVarios").attr("elId", miId);
    
            //proveedor
            $(miFila).find(".n_td_prov").attr("id", "n_prov_" + miId);
            $(miFila).find(".n_l_td_prov").attr("id", "n_l_prov_" + miId);
    
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
                        console.log(cuentas3);
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
    
                        console.log(arr_centros);
    
    
                        $("#fila_m_" + miId).find(".td_total").val(JSON.stringify(arr_centros));
                        $(this).dialog("close");
                    }
                }
            });
    
            $(miFila).find(".n_td_ke").attr("id", "n_ke_" + miId);
            $(miFila).find(".n_l_td_ke").attr("id", "n_l_ke_" + miId);
    
            $(miFila).find(".n_td_ce").attr("id", "n_ce_" + miId);
            $(miFila).find(".n_l_td_ce").attr("id", "n_l_ce_" + miId);
    
            $(miFila).find(".n_td_cu").attr("id", "n_cu_" + miId);
            $(miFila).find(".n_l_td_cu").attr("id", "n_l_cu_" + miId);
            $("#tbodyDetalles").append(miFila);

            $("#n_prov_" + miId).combobox({
                source: total_proveedor,
                focus: function(event, ui) {
                    $("#n_prov_" + miId).val(ui.item.nombre);
                    $("#n_prov_" + miId).next().find(".custom-combobox-input").val(ui.item.nombre);
                    return false;
                },
                select: function(event, ui) {
                    $("#n_l_prov_" + miId).val(ui.item.id);
                    $("#n_prov_" + miId).val(ui.item.nombre);
                    $("#n_prov_" + miId).next().find(".custom-combobox-input").val(ui.item.nombre);

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
    
                                    $.ajax({
                                        url: "admin/oc/getAjaxCuenta3_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: ui.item.id
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
                                    return false;
                                }
                            });
                        }
                    });
                    return false;
                }
            });
    
        });
    
    
    
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
    
                                    $.ajax({
                                        url: "admin/oc/getAjaxCuenta3_centro",
                                        type: "POST",
                                        dataType: "json",
                                        data: {
                                            idCentro: ui.item.id
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
                                    return false;
                                }
                            });
                        }
                    });
                    return false;
                }
            });
    
    
        });
        $("#btnAgregarDetalle").click();

        $("body").on("click",".n_td_delete",function(){
            $(this).parents("tr").eq(0).remove();
        })
        
        $("body").on("click",".l_td_delete",function(){
            $(this).parents("tr").eq(0).remove();
        })

        idBanco.onchange = function() {
            let nroCuenta_text = this.value;

            nroCuenta.value = this.selectedOptions[0].getAttribute('nroCuenta');
        }
        
    });
</script>