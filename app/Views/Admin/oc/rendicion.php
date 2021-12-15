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
</style>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
<script>
    $(function() {

        let cuentas3 = JSON.parse('<?php echo json_encode($cuentas3) ?>');
        let empresas_total = JSON.parse('<?php echo json_encode($empresas_total) ?>');
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

        $("#idCuenta3_auto").combobox({
            source : total_cuentas3,
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
        $("#ejecutado_com").combobox({
            source : total_empresas,
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
                            idBanco.add(new Option(response.response[i].descripcion, response.response[i].nroCuenta));
                        }
                        nroCuenta.value = '';

                    })
                    .catch(error => console.log('error', error));

                return false;
            }
        });

    });
</script>

<form method="POST" action="">

    <div class="text-center mt-4 mb-4">
        <h3>RENDICIÓN DE GASTOS</h3>
    </div>

    <div class="row">
        <div class="col-md-5">
            <img src="images/logo1.png" alt="">
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <input type="hidden" name="nombre" value="nombre">
                <select name="idTipoOrden" id="idTipoOrden" class="form-select">
                    <option value="">Seleccionar</option>

                    <?php foreach ($tipoOrden as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] . " - " . $value["descripcion"]  ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <input type="hidden" name="corden" value="corden">
                <select name="corden" id="corden" class="form-select">
                    <option value="">Seleccionar Orden</option>
                    <?php foreach ($ordenes as $key => $value) : ?>
                        <option value="<?php echo $value["id"] ?>"><?php echo $value["codigo"] ?></option>
                    <?php endforeach; ?>    
                   
                </select>
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

            <!-- <div class="mb-3 row">
                <label for="idKey" class="col-sm-2 col-form-label">Key</label>
                <div class="col-sm-10">
                    <select class="form-select" name="idKey" id="idKey" required>
                        <option value="" selected>Seleccionar</option>
                        <?php foreach ($keys as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div> -->

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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nº Doc</th>
                    <th>Proveedor</th>
                    <th>Detalle</th>
                    <th>Key</th>
                    <th>Centro Costo</th>
                    <th>Clase Costo</th>
                    <th>Cuenta 3</th>
                    <th>Cuenta 1</th>
                    <th>Monto</th>
                </tr>
            </thead>

            <tbody id="tbodyDetalles">
                <tr>
                    <td><input type="text" name="nro[]"></td>
                    <td>
                        <select name="proveedor[]">
                            <option value="" selected>Seleccionar</option>
                        <?php foreach ($empresas_total as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="detalle[]"></td>
                    <td>
                        <select name="keys[]" class="t_key">
                            <option value="" selected>Seleccionar</option>
                        <?php foreach ($keys as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="td_centro">
                        <select name="centro[]" class="t_centro">
                            <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_clase">
                        <select name="clase[]" class="t_clase">
                        <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_cuenta3">
                        <select name="cuenta3[]" class="t_cuenta3">
                        <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_cuenta1">-</td>
                    <td><input type="text" name="monto[]"></td>
                </tr>
            </tbody>
        </table>

        <table style="display:none">
            <tbody id="miFilaDetalle">
            <tr>
                    <td><input type="text" name="nro[]"></td>
                    <td>
                        <select name="proveedor[]">
                            <option value="" selected>Seleccionar</option>
                        <?php foreach ($empresas_total as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["nombre"] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="detalle[]"></td>
                    <td>
                        <select name="keys[]" class="t_key">
                            <option value="" selected>Seleccionar</option>
                        <?php foreach ($keys as $key => $value) : ?>
                            <option value="<?php echo $value["id"] ?>"><?php echo $value["descripcion"] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="td_centro">
                        <select name="centro[]" class="t_centro">
                            <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_clase">
                        <select name="clase[]" class="t_clase">
                        <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_cuenta3">
                        <select name="cuenta3[]" class="t_cuenta3">
                        <option value="" selected>Seleccionar</option>
                        </select>
                    </td>
                    <td class="td_cuenta1">-</td>
                    <td><input type="text" name="monto[]"></td>
                </tr>
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
                <label for="idBanco" class="col-sm-4 col-form-label">Banco</label>
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

                    <input class="form-control" type="file" id="formFile">
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

<script>
    $("#btnAgregarDetalle").click(function(){
        let miFila = $("#miFilaDetalle").html();
        $("#tbodyDetalles").append(miFila);
    });

    $("body").on("change",".t_key",function(){
        let val = $(this).val();
        let select = $(this).parents("tr").eq(0).find(".td_centro select");
        $.ajax({
            url:"admin/centro/ajaxGet_key",
            data : {"idKey": val},
            dataType:"json",
            type:"post",
            success : function(response){
               
                console.log(select);
                let options = '<option value="">Seleccionar</option>';
                for (let i = 0; i < response.keys.length; i++) {
                    options += '<option value="'+ response.keys[i].id +'">'+ response.keys[i].descripcion +'</option>';
                } 
                console.log(options);
                $(select).html(options);
            }
        });
    });

    $("body").on("change",".t_centro",function(){
        let val = $(this).val();
        let select = $(this).parents("tr").eq(0).find(".td_clase select");
        $.ajax({
            url:"admin/clasecosto/ajaxGetCentro",
            data : {"idCentro": val},
            dataType:"json",
            type:"post",
            success : function(response){
               
                console.log(response);
                let options = '<option value="">Seleccionar</option>';
                for (let i = 0; i < response.clasecosto_centro.length; i++) {
                    options += '<option value="'+ response.clasecosto_centro[i].id +'">'+ response.clasecosto_centro[i].clasecosto_descripcion +'</option>';
                } 
                console.log(options);
                $(select).html(options);
            }
        });
    });
    
    $("body").on("change",".t_clase",function(){
        let val = $(this).val();
        let select = $(this).parents("tr").eq(0).find(".td_cuenta3 select");
        $.ajax({
            url:"admin/cuenta3/ajaxGetClase",
            data : {"idCuenta": val},
            dataType:"json",
            type:"post",
            success : function(response){
               
                console.log(response);
                let options = '<option value="">Seleccionar</option>';
                for (let i = 0; i < response.response.length; i++) {
                    options += '<option idCuenta1="'+response.response[i].cuenta1_descripcion+'" value="'+ response.response[i].id +'">'+ response.response[i].descripcion +'</option>';
                } 
                console.log(options);
                $(select).html(options);
            }
        });
    });
    
    $("body").on("change",".t_cuenta3",function(){
        let val = $(this).find("option:selected").attr("idCuenta1");
        console.log(val);
        $(this).parents("tr").find(".td_cuenta1").text(val);
      
    });





  



</script>