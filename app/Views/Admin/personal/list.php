<h3>Listado de <?= $lista ?></h3>

<p>
    <a href="admin/<?= $lista ?>/agregar" class="btn btn-warning"><i class="bi bi-plus-lg"></i></a>
</p>

<table class="table table-bordered">
    <tr>
        <th>Nombres</th>

        <th>Cargo</th>
        <th>Tipo Documento</th>
        <th>Nro Documento</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($lista_datos as $key => $value) : ?>
        <tr>
            <td><?= $value->nombres. " ". $value->apellidoPaterno." ".$value->apellidoMaterno; ?></td>
            <td><?= $value->cargo_desc; ?></td>
            <td><?= $value->tipo_desc; ?></td>
            <td><?= $value->nroDocumento; ?></td>
            <td>
                <a href="admin/<?= $table ?>/editar/<?= $value->id ?>" class="btn btn-info"><i class="bi bi-pencil"></i></a>
                <button elId="<?= $value->id ?>" class="btn btn-danger btnEliminar"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<div class="modal" tabindex="-1" id="modalEliminar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">¿Desea Eliminar?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Eliminar</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnOkEliminar">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<script>
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {});
    let btnEliminarAll = document.getElementsByClassName('btnEliminar');
    
    for(let i=0; i<btnEliminarAll.length;i++){
        btnEliminarAll[i].onclick = function(){
            let id = this.getAttribute('elId');

            document.getElementById('btnOkEliminar').setAttribute('href','admin/<?= $table?>/eliminar/'+id);
            modalEliminar.show();

        }
    }
   
</script>