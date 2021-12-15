<h3>Listado de Centros</h3>

<p>
    <form action="<?= base_url() ?>/admin/key/centroAgregar" method="POST">
        <div class="input-group mb-3">
            <input type="submit" name="submit" value="Agregar" class="btn btn-success">
            <input type="text" name="descripcion" class="form-control" placeholder="Centro de Costo">
            <input type="hidden" name="idKey" value="<?= $idKey?>">
        </div>
    </form>
</p>

<table class="table table-bordered">
    <tr>
        <th>Descripción</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($centros as $key => $value) : ?>
        <tr> 
            <td><?= $value->descripcion; ?></td>
            <td>
<!--                 <a href="admin/<?= $table ?>/editar/<?= $value->id ?>" class="btn btn-info">Editar</a>
                <a href="admin/<?= $table ?>/centro/<?= $value->id ?>" class="btn btn-success">Centro de Costo</a> -->
                <button elId="<?= $value->id ?>" class="btn btn-danger btnEliminar"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


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

<script>
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {});
    let btnEliminarAll = document.getElementsByClassName('btnEliminar');
    let idKey = '<?= $idKey ?>';

    for(let i=0; i<btnEliminarAll.length;i++){
        btnEliminarAll[i].onclick = function(){
            let id = this.getAttribute('elId');

            document.getElementById('btnOkEliminar').setAttribute('href','admin/<?= $table?>/centroEliminar/'+id + '/'+ idKey);
            modalEliminar.show();

        }
    }
   
</script>