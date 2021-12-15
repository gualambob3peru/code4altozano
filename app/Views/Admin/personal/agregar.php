<div class="container">
    <h3>Editar <?= $nombre?></h3>
    <?php $validation = \Config\Services::validation(); ?>
    <form action="" method="POST">
    <div class="mb-3 row">
            <label for="nombres" class="col-sm-2 col-form-label">Nombres</label>
            <div class="col-sm-10">
                <input type="text" name="nombres" class="form-control" id="nombres" value="">
                <?php if ($validation->getError('nombres')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('nombres'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="mb-3 row">
            <label for="apellidoPaterno" class="col-sm-2 col-form-label">Apellido Paterno</label>
            <div class="col-sm-10">
                <input type="text" name="apellidoPaterno" class="form-control" id="apellidoPaterno" value="">
                <?php if ($validation->getError('apellidoPaterno')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('apellidoPaterno'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="apellidoMaterno" class="col-sm-2 col-form-label">Apellido Materno</label>
            <div class="col-sm-10">
                <input type="text" name="apellidoMaterno" class="form-control" id="apellidoMaterno" value="">
                <?php if ($validation->getError('apellidoMaterno')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('apellidoMaterno'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="idTipoDocumento" class="col-sm-2 col-form-label">Tipo de documento</label>
            <div class="col-sm-10">
                <select type="text" name="idTipoDocumento" class="form-select" id="idTipoDocumento">
                    <option value="">Seleccionar</option>
                <?php foreach($tipoDocumentos as $key=>$value): ?>
                    <option value="<?= $value->id ?>"><?= $value->descripcion ?></option>
                <?php endforeach; ?>
                </select>
                <?php if ($validation->getError('idTipoDocumento')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('idTipoDocumento'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="mb-3 row">
            <label for="nroDocumento" class="col-sm-2 col-form-label">Número de documento</label>
            <div class="col-sm-10">
                <input type="text" name="nroDocumento" class="form-control" id="nroDocumento" value="">
                <?php if ($validation->getError('nroDocumento')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('nroDocumento'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="idCargo" class="col-sm-2 col-form-label">Cargo</label>
            <div class="col-sm-10">
                <select type="text" name="idCargo" class="form-select" id="idCargo">
                    <option value="">Seleccionar</option>
                <?php foreach($cargos as $key=>$value): ?>
                    <option value="<?= $value->id ?>"><?= $value->descripcion ?></option>
                <?php endforeach; ?>
                </select>
                <?php if ($validation->getError('idCargo')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('idCargo'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
  
     


        

        <div class="mb-3">
            <input type="submit" name="submit" value="Guardar" class="btn btn-success">
        </div>
    </form>

</div>

<script>

</script>