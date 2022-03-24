
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
                <h4>Rendición - <?= $o_rendicion["codigo"] ?></h4>
            </div>
            <div class="text-center mt-4 mb-4 f14">
                <div>
                Fecha : <?= substr($o_rendicion["created_at"],0,10) ?>  
                </div>    
                <div>
                Área : <?= $o_rendicion["tipoOrden_descripcion"] ?>  
                </div>
                <div>
                Tipo solicitud : <?= $o_rendicion["tsr_descripcion"] ?> 
                </div>

                <?php if($o_rendicion["o_codigo"] != ""):?>
                    <div>
                Orden: <?= $o_rendicion["o_codigo"] ?> 
                </div>
                <?php endif?>
            </div>
        
        
           
            <h6>Datos del solicitado</h6>
        
            <table class="table table-bordered f14">
                <tr>
                    <td>Empresa</td>
                    <td><?php echo $o_rendicion["empresa_nombre"]." - ".$o_rendicion["empresa_ruc"] ?></td>
                </tr>
            
                <tr>
                    <td>Solicitado</td>
                    <td><?php echo $o_rendicion["pes_nombres"]." ".$o_rendicion["pes_apellidoPaterno"]." ".$o_rendicion["pes_apellidoMaterno"] ?></td>
                </tr>
                <tr>
                    <td>Jefe inmediato</td>
                    <td><?php echo $o_rendicion["pej_nombres"]." ".$o_rendicion["pej_apellidoPaterno"]." ".$o_rendicion["pej_apellidoMaterno"] ?></td>
                </tr>
            </table>
        
        
        
            <div id="divCostos">
        
                <hr>
                <h6>Datos de rendición</h6>
                
                <table class="table table-bordered" style="font-size:13px">
                    <thead>
                        <tr>
                            <th>Nº Doc</th>
                            <th>Proveedor</th>
                            <th>Detalle</th>
                           
                            <th>Key</th>
                            <th>Ceco</th>
                            <th>Nivel 3</th>
                         
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($o_items as $key=>$value): ?>
                            <?php if($value["centros"] == null): ?>
                                <tr>
                                    <td style="width: 150px;"><?= $value["nroDoc"]; ?></td>
                                    <td><?= $value["emp_nombre"]." ".$value["emp_ruc"]; ?></td>
                                    <td><?= $value["detalle"]; ?></td>
                                   
                                    <td><?= $value["k_descripcion"]; ?></td>
                                    <td><?= $value["c_codigo"]." ".$value["c_descripcion"]; ?></td>
                                    <td><?= $value["c3_codigo"]." ".$value["c3_descripcion"]; ?></td>
                                 
                                    <td><?= $value["monto"]; ?></td>
                               
                                </tr>
                            <?php else: ?>
                                <tr >
                                    <td style="width:150px" rowspan="<?php echo count($value["centros"]) ?>"><?= $value["nroDoc"]; ?></td>
                                    <td rowspan="<?php echo count($value["centros"]) ?>"><?= $value["emp_nombre"]." ".$value["emp_ruc"]; ?></td>
                                    <td rowspan="<?php echo count($value["centros"]) ?>"><?= $value["detalle"]; ?></td>
                                  
                                    <?php foreach($value["centros"] as $key2=>$value2): ?>
                                           
                                        <?php if ($key2==0): ?>
                                            <td><?= $value2["k_descripcion"] ?></td>
                                            <td><?= $value2["c_codigo"]." ".$value2["c_descripcion"]; ?></td>
                                            <td><?= $value2["c3_codigo"]." ".$value2["c3_descripcion"]; ?></td>
                                            <td><?= $value2["monto"] ?></td>
                           
                                        <?php endif; ?>
                                    <?php endforeach; ?>  
                                </tr>
                                
                                <?php foreach($value["centros"] as $key3=>$value3): ?>
                                    
                                    
                                    <?php if ($key3!=0): ?>
                                        <tr>
                                            <td><?= $value3["k_descripcion"] ?></td>
                                            <td><?= $value3["c_codigo"]." ".$value3["c_descripcion"]; ?></td>
                                            <td><?= $value3["c3_codigo"]." ".$value3["c3_descripcion"]; ?></td>
                                            <td><?= $value3["monto"] ?></td>
                                        </tr>
                                    <?php endif; ?>
                    
                                    
                                <?php endforeach; ?>
                                        
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <th>Total</th>
                            <th><?php echo $o_rendicion["m_descripcion"] ?></th>
                            <th><?php echo $o_rendicion["importe"]?></th>
                        </tr>
                    </tfoot>
                </table>
                
        
                
            </div>
        
        
        
            <hr>
            <h6>Datos del contratista</h6>
                <div>
                    <table class="table table-bordered f14">
                        <tbody>
                            <tr>
                                <td>Ejecutado</td>
                                <td><?php echo $o_rendicion["eje_nombre"]. " " . $o_rendicion["eje_ruc"] ?></td>
                            </tr>
                            <tr>
                                <td>Banco</td>
                                <td><?php echo $o_rendicion["b_descripcion"] ?></td>
                            </tr>
                            <tr>
                                <td>Nº Cuenta</td>
                                <td><?php echo $o_rendicion["be_nroCuenta"] ?></td>
                            </tr>
                        </tbody>
                    </table>
                      
                    </div>
        
        
          
            
           
            <hr>
            <h6>Referencia</h6>
                <div>
                    <?php echo $o_rendicion["referencia"] ?>
                </div>
            <h6>Documentos</h6>
                <?php foreach($o_images as $key=>$value): ?>
                    <div class="f14">
                        Doc <?= $key+1 ?>: <a target="_blank" href="uploads/rendicion/<?= $value["idRendicion"] ?>/<?= $value["imagen"]?>"><?= $value["imagen"]?></a>
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
    $(function(){
        let codigo = '<?php echo $o_rendicion["codigo"] ?>';
        $("#descargar").click(function(){
            var element = document.getElementById('htmlpdf');
            var opt = {
                margin: [0, 0, 0, 0],
                filename:     codigo+'.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2 },
                jsPDF: {unit: 'pt', format: 'a2', orientation: 'portrait'}
            };

            html2pdf().set(opt).from(element).save();

        });
    });
</script>

