<div class="container">
    <h3>Editar <?= $nombre?></h3>
    <?php $validation = \Config\Services::validation(); ?>
    <form action="" method="POST">
        <div class="mb-3 row">
            <label for="nombre" class="col-sm-2 col-form-label">Razón social</label>
            <div class="col-sm-10">
                <input type="text" name="nombre" class="form-control" id="nombre" required>
                <?php if ($validation->getError('nombre')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('nombre'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="idTipoEmpresa" class="col-sm-2 col-form-label">Tipo de empresa</label>
            <div class="col-sm-10">
                <select type="text" name="idTipoEmpresa" class="form-select" id="idTipoEmpresa" required>
                    <option value="">Seleccionar</option>
                <?php foreach($tipoEmpresas as $key=>$value): ?>
                    <option value="<?= $value->id ?>"><?= $value->descripcion ?></option>
                <?php endforeach; ?>
                </select>
                <?php if ($validation->getError('idTipoEmpresa')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('idTipoEmpresa'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="mb-3 row">
            <label for="ruc" class="col-sm-2 col-form-label">RUC</label>
            <div class="col-sm-10">
                <input type="text" name="ruc" class="form-control" id="ruc" required>
                <?php if ($validation->getError('ruc')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('ruc'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="direccion" class="col-sm-2 col-form-label">Dirección</label>
            <div class="col-sm-10">
                <input type="text" name="direccion" class="form-control" id="direccion" required>
                <?php if ($validation->getError('direccion')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('direccion'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
  
     


        

        <div class="mb-3">
            <button class="btn btn-success text-white" name="submit" value="submit"><i class="bi bi-save"></i> Guardar</button>
      
        </div>
    </form>

</div>

<script>

</script>