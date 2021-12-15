
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js"></script>

<h4>Key : <?php echo $f_model["descripcion"] ?></h4>
<hr>
<h5>Agregar Centro</h5>
<div class="row">
    <div class="col-md-6">
        <form action="<?= base_url() ?>/admin/centro/agregar" method="POST">



            <div class="input-group mb-3">
                <input type="text" name="codigo" class="form-control" placeholder="Código">
                <input type="text" name="descripcion" class="form-control" placeholder="Centro de Costo">
                <input type="text" name="monto" class="form-control" placeholder="Monto">
                <input type="hidden" name="idKey" value="<?= $idKey ?>">
                <button type="submit" name="submit" class="btn btn-success" value="fl"><i class="bi bi-file-plus"></i>Agregar</button>

            </div>
        </form>
    </div>
</div>


<h5>Listado de Centros</h5>
<table class="table table-bordered">
    <tr>
        <th>Código</th>
        <th>Descripción</th>
        <th>Monto</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($centros as $key => $value) : ?>
        <tr>
            <td><?= $value->codigo; ?></td>
            <td><?= $value->descripcion; ?></td>
            <td><?= $value->monto; ?></td>
            <td>
                <!--                 <a href="admin/<?= $table ?>/editar/<?= $value->id ?>" class="btn btn-info">Editar</a>
                <a href="admin/<?= $table ?>/centro/<?= $value->id ?>" class="btn btn-success">Centro de Costo</a> -->
                <button elId="<?= $value->id ?>" class="btn btn-danger btnEliminar"><i class="bi bi-trash"></i></button>

                <button elId="<?= $value->id ?>" class="btn btn-info btnModalClaseCosto"><i class="bi bi-card-checklist"></i> Clase de costo</button>
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

<div class="modal" tabindex="-1" id="modalClaseCosto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clase de Costo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="listaClaseCosto">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-primary" id="btnClaseOk">Aceptar</a>
            </div>
        </div>
    </div>
</div>

<script>
    let modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'), {});
    let modalClaseCosto = new bootstrap.Modal(document.getElementById('modalClaseCosto'), {});
    let btnEliminarAll = document.getElementsByClassName('btnEliminar');
    let btnModalClaseCostoAll = document.getElementsByClassName('btnModalClaseCosto');
    let idKey = '<?= $idKey ?>';

    for (let i = 0; i < btnEliminarAll.length; i++) {
        btnEliminarAll[i].onclick = function() {
            let id = this.getAttribute('elId');

            document.getElementById('btnOkEliminar').setAttribute('href', 'admin/<?= $table ?>/centroEliminar/' + id + '/' + idKey);
            modalEliminar.show();
        }
    }

    for (let i = 0; i < btnModalClaseCostoAll.length; i++) {
        btnModalClaseCostoAll[i].onclick = function() {
            let id = this.getAttribute('elId');

            var formdata = new FormData();
            formdata.append("idCentro", id);
            btnClaseOk.setAttribute("idCentro",id);

            fetch("admin/clasecosto/ajaxGetCentro", {
                    method: 'POST',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: formdata,
                    redirect: 'follow'
                })
                .then(response => response.json())
                .then(function(response) {
                    console.log(response);
                    let clasecosto = response.clasecosto,
                        clasecosto_centro = response.clasecosto_centro,
                        lista_html = '';

                    for(ind in clasecosto){
                        let esta = 0;
                        for(ind2 in clasecosto_centro){
                            if(clasecosto_centro[ind2].idClaseCosto == clasecosto[ind].id){
                                esta=1;
                            }
                        }
                        let checked = "";
                        if(esta==1) checked = "checked";

                        lista_html += '<div class="form-check">  <input '+checked+'  class="form-check-input filaClaseCosto" type="checkbox" value="'+ clasecosto[ind].id +'">  <label class="form-check-label" for="flexCheckDefault">' + clasecosto[ind].descripcion + ' </label></div>';

                     
                    }

                    listaClaseCosto.innerHTML = lista_html;
                    modalClaseCosto.show();
                })
                .catch(error => console.log('error', error));
        }
    }

    btnClaseOk.onclick = function(){
        let filaClaseCosto = document.getElementsByClassName('filaClaseCosto');
        let idCentro =this.getAttribute('idCentro');
        var formdata = new FormData();
        let valores = [];
        for (let i = 0; i < filaClaseCosto.length; i++) {
            if(filaClaseCosto[i].checked){
                valores.push(filaClaseCosto[i].value)
            }
        }
        
        formdata.append("clases", valores);
        formdata.append("idCentro", idCentro);

        fetch("admin/clasecosto/updateCentro", {
            method: 'POST',
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formdata,
            redirect: 'follow'
        })
        .then(response => response.json())
        .then(function(response) {
            console.log(response);
            modalClaseCosto.hide();
        })
        .catch(error => console.log('error', error));


    }
</script>