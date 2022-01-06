<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



<div class="container">

    <div class="text-end">
        <button id="descargar" class="btn btn-danger mb-4 text-white"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
    </div>
    <div id="htmlpdf">
        <img src="images/logo1.png" alt="">
        <form method="POST" action="">

            <div class="text-center mt-1 mb-4">
                <h4>
                    <span class="<?= (($orden["estado"] == 1) ? "bg-success" : "bg-warning") ?>" style="padding: 7px;">

                        Orden de Compra - <?= $orden["codigo"] ?>
                    </span>
                </h4>

            </div>
            <div class="text-center mt-4 mb-4 f14">
                <div>
                    Fecha : <?= substr($orden["fecha"], 0, 10) ?>
                </div>
                <div>
                    Área : <?= $tipoOrden["descripcion"] ?>
                </div>
                <div>
                    Tipo solicitud : <?= $tipoSolicitud["descripcion"] ?>
                </div>
            </div>


            <br>
            <br>
            <h6>Datos del solicitado</h6>

            <table class="table table-bordered f14">
                <tr>
                    <td>Empresa</td>
                    <td><?php echo $empresa["nombre"] ?></td>
                </tr>
                <tr>
                    <td>Key</td>
                    <td><?php echo $key->key_descripcion ?></td>
                </tr>
                <tr>
                    <td>Solicitado</td>
                    <td><?php echo $personalSoli["nombres"] ?></td>
                </tr>
                <tr>
                    <td>Jefe inmediato</td>
                    <td><?php echo $personalJefe["nombres"] ?></td>
                </tr>
            </table>



            <div id="divCostos">

                <hr>
                <h6>Datos del contrato</h6>

                <?php if ($key->codigo_centro == "VariosCAD") : ?>
                    <table class="table table-bordered f14">
                        <thead>
                            <tr>
                                <th>Centro</th>
                                <th>Nivel 3</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orden_centros as $key => $value) : ?>
                                <tr>
                                    <td><?= $value->codigo_centro . " " . $value->descripcion_centro; ?></td>
                                    <td><?= $value->c3_codigo." ".$value->c3_descripcion; ?></td>
                                    <td><?= $value->porcentaje; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else : ?>
                    <table class="table table-bordered f14">

                        <tbody>


                            <tr>
                                <td>Centro de Costo</td>
                                <td><?php echo $key->codigo_centro . " " . $key->descripcion_centro ?></td>
                            </tr>
                            <tr>
                                <td>Cuenta Nivel 1</td>
                                <td><?php echo $cuenta->c1_codigo . " " . $cuenta->c1_descripcion ?></td>
                            </tr>
                            <tr>
                                <td>Cuenta Nivel 2</td>
                                <td><?php echo $cuenta->c2_codigo . " " . $cuenta->c2_descripcion ?></td>
                            </tr>
                            <tr>
                                <td>Cuenta Nivel 3</td>
                                <td><?php echo $cuenta->c3_codigo . " " . $cuenta->c3_descripcion ?></td>
                            </tr>


                        </tbody>
                    </table>

                <?php endif; ?>


            </div>
            <hr>
            <h6>Objeto del contrato</h6>
            <div>
                <?php
                echo $orden["objeto"];
                ?>
            </div>


            <hr>
            <h6>Datos del contratista</h6>
            <div>
                <table class="table table-bordered f14">
                    <tbody>
                        <tr>
                            <td>Ejecutado</td>
                            <td><?php echo $ejecutado["ruc"] . " " . $ejecutado["nombre"] ?></td>
                        </tr>
                        <tr>
                            <td>Banco</td>
                            <td><?php echo $banco->descripcion ?></td>
                        </tr>
                        <tr>
                            <td>Nº Cuenta</td>
                            <td><?php echo $banco->nroCuenta ?></td>
                        </tr>
                        <tr>
                            <td>Moneda</td>
                            <td><?php echo $banco->moneda_simbolo." ".$banco->moneda_descripcion ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>


            <hr>
            <h6>Detalle del contrato</h6>

            <div>
                <table class="table table-bordered f14">

                    <tbody>
                        <?php foreach ($ordenDetalle as $key => $value) : ?>
                            <tr>
                                <td><?php echo $value->descripcion ?></td>
                                <td><?php echo $value->monto ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="text-end">Total</td>
                            <td>
                                <?php echo $orden["importe"] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end">Moneda</td>
                            <td><?= $moneda["descripcion"] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr>
            <h6>Referencia</h6>
            <div>
                <?php echo $orden["referencia"] ?>
            </div>
            <h6>Documentos</h6>
            <?php foreach ($images as $key => $value) : ?>
                <div class="f14">
                    Doc <?= $key + 1 ?>: <a target="_blank" href="uploads/<?= $value->idOrden ?>/<?= $value->imagen ?>"><?= $value->imagen ?></a>
                </div>
            <?php endforeach; ?>

            <br>
            <br>
            <br>

            <br>
            <br>
            <br>
            <br>
        </form>
    </div>
</div>

<script>
    $(function() {
        let codigo = '<?php echo $orden["codigo"] ?>';
        $("#descargar").click(function() {
            var element = document.getElementById('htmlpdf');
            var opt = {
                margin: 1,
                filename: codigo + '.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 1
                },
            };

            html2pdf().set(opt).from(element).save();

        });
    });
</script>