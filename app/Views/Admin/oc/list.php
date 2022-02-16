
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>


    <script>
    $(function() {

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


        $('#miTabla').DataTable({
            "order": [[ 1, "desc" ]],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "",
                "sInfoEmpty": "",
                "sInfoFiltered": "",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Ãšltimo",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            dom: 'Bfrtip',
            buttons: [
               
            ]
        });

    });
    </script>


<h3>Listado de Ordenes de compra</h3>

<a class="btn btn-success text-white" href="admin/oc/agregar"><i class="bi bi-plus-lg"></i> Orden de compra</a>

<table class="table" id="miTabla" style="font-size:14px">
    <thead>

        <tr>
            <th>Número</th>
            <th>Fecha</th> 
            <th>Empresa</th> 
            <th>Solicitante</th> 
            <th>Proveedor</th> 
            <th>Moneda</th> 
            <th>Importe</th> 
            <th>Ceco</th> 
            <th>Nivel 3</th> 
            
            <th>Tipo solicitud</th>
            <th>Estado</th> 
            <th>Acciones</th> 
            
        </tr>
    </thead>
    <tbody>

        <?php foreach($ocs as $key=>$value): ?>
        <tr>
            <td><span><?= $value->orden_codigo; ?> </span></td>
            <td style="width: 100px;"><span><?= substr($value->created_at,0,10); ?></span></td>
            <td><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value->empresa_nombre; ?>"><?= substr($value->empresa_nombre,0,20); ?></span></td>
            <td><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value->peso_nombres." ".$value->peso_apellidoPaterno." ".$value->peso_apellidoMaterno; ?>"><?= substr($value->peso_nombres." ".$value->peso_apellidoPaterno." ".$value->peso_apellidoMaterno,0,17)."..." ; ?></span></td>
            <td><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value->eje_nombre; ?>"><?= substr($value->eje_nombre,0,20); ?></span></td>
            <td><?= $value->moneda_descripcion; ?></td>
            <td><?= number_format($value->importe,2); ?></td>
            <td><span data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $value->centro_desc; ?>"><?= substr($value->centro_desc,0,17)."..."; ?></td>
            <td><?= $value->cuenta3_cod."<br>".substr($value->cuenta3_desc,0,14)."..."; ?></td>
            <td><?= $value->tipoSolicitud_desc; ?></td>
            <td>
                <?php 
                    if($value->orden_estado=="1"){
                       
                        echo '<i style="font-size:17px" class="bi bi-check-circle-fill text-success" data-bs-toggle="tooltip" data-bs-placement="top"  style<="title="Aprobado"></i>';
                    }else if($value->orden_estado=="2"){
                        echo '<i style="font-size:17px" class="bi bi-exclamation-circle-fill text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Pendiente para Jefe Directo"></i>';
                       
                    }else if($value->orden_estado=="3"){
                        echo '<i style="font-size:17px" class="bi bi-exclamation-circle-fill text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Pendiente para Finanzas"></i>';
                    
                       
                    }else if($value->orden_estado=="4"){
                        echo '<i style="font-size:17px" class="bi bi-exclamation-circle-fill text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Pendiente para Gerencia"></i>';
                       
                    }
                ?>
            </td>
        <td style="width: 150px;">
            <div class="input-group">

                 <?php 
                    if(($value->idPersonal==$_SESSION["personal"]["id"] &&  $value->orden_estado == 2) || $_SESSION["personal"]["idCargo"] == "1" ||$_SESSION["personal"]["idCargo"] == "2"){
                        echo '<a data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Orden" href="admin/oc/editar/'. $value->id.'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>  ';
                    }
                
                ?>
                
                   
                <?php 
                    if($value->orden_estado=="1"){
                      
                    }else if($value->orden_estado=="2"){
                      
                        if($_SESSION["personal"]["idCargo"]=="3"){
                            echo " <button data-bs-toggle='tooltip' data-bs-placement='top' title='Aprobar Orden' elId='".$value->id."' class='text-white btn btn-success btn-sm btnAprobar'><i class='bi bi-check-lg'></i></button>";
                        }
                        if($_SESSION["personal"]["idCargo"]=="1"){
                            echo " <button data-bs-toggle='tooltip' data-bs-placement='top' title='Aprobar Orden' elId='".$value->id."' class='text-white btn btn-success btn-sm btnAprobar'><i class='bi bi-check-lg'></i></button>";
                        }
                    }else if($value->orden_estado=="3"){
                      
                        if($_SESSION["personal"]["idCargo"]=="2"){
                            echo " <button data-bs-toggle='tooltip' data-bs-placement='top' title='Aprobar Orden' elId='".$value->id."' class='text-white btn btn-success btn-sm btnAprobar'><i class='bi bi-check-lg'></i></button>";
                        }
                        if($_SESSION["personal"]["idCargo"]=="1"){
                            echo " <button data-bs-toggle='tooltip' data-bs-placement='top' title='Aprobar Orden' elId='".$value->id."' class='text-white btn btn-success btn-sm btnAprobar'><i class='bi bi-check-lg'></i></button>";
                        }
                    }else if($value->orden_estado=="4"){
                      
                        if($_SESSION["personal"]["idCargo"]=="1"){
                            echo " <button data-bs-toggle='tooltip' data-bs-placement='top' title='Aprobar Orden' elId='".$value->id."' class='text-white btn btn-success btn-sm btnAprobar'><i class='bi bi-check-lg'></i></button>";
                        }
                    }
                ?>
 
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Ver Detalles de la orden" elId="<?= $value->id ?>" href="admin/oc/ver/<?= $value->id ?>" class="text-white btn btn-info btn-sm"><i class="bi bi-eye"></i></a>

                <?php 
                    if(($value->idPersonal==$_SESSION["personal"]["id"] &&  $value->orden_estado == 2) || $_SESSION["personal"]["idCargo"] == "1" ||$_SESSION["personal"]["idCargo"] == "2"){
                        echo '<button data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar la orden" type="button" elId="'.$value->id.'" class="text-white btn btn-danger btn-sm btnEliminar"><i class="bi bi-trash"></i></button>';
                    }
                
                ?>
                
            </div>
        </td>
    </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal" tabindex="-1" id="modalEliminar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-2 ms-2">
                    <div class="col-md-12">

                        <span class="badge bg-danger">Escriba la palabra eliminar para eliminar</span>
                    </div>
                </div>
                    
                <div class="row mt-2 ms-2 text-center">
                    <input type="text" id="inputEliminar" class="form-control ">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnOkEliminar">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="modalAprobar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Desea aprobar OC?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnOkAprobar">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<script>
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {});
    let modalAprobar = new bootstrap.Modal(document.getElementById('modalAprobar'), {});
    let btnEliminarAll = document.getElementsByClassName('btnEliminar');
    let btnAprobarAll = document.getElementsByClassName('btnAprobar');
    
    for(let i=0; i<btnEliminarAll.length;i++){
        btnEliminarAll[i].onclick = function(){
            let id = this.getAttribute('elId');

            document.getElementById('btnOkEliminar').setAttribute('href','admin/oc/eliminar/'+id);
            modalEliminar.show();

        }
    }

    for(let i=0; i<btnAprobarAll.length;i++){
        btnAprobarAll[i].onclick = function(){
            let id = this.getAttribute('elId');

            document.getElementById('btnOkAprobar').setAttribute('href','admin/oc/aprobar/'+id);
            modalAprobar.show();

        }
    }

    btnOkEliminar.onclick = function(e){
        if(inputEliminar.value == "eliminar"){
            return true;
        }else{
            return false;
        }
    }
   
   
</script>