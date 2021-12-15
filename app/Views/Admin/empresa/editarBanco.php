<div class="container">
    <h3>Editar Banco de <?= $empresa["nombre"]?></h3>
    <?php $validation = \Config\Services::validation(); ?>
    <form action="" method="POST">
        <input type="hidden" name="idEmpresa" value="<?= $empresa["id"]?>">

        <div class="mb-3 row">
            <label for="idBanco" class="col-sm-2 col-form-label">Banco</label>
            <div class="col-sm-10">
                

                <select name="idBanco" id="idBanco" class="form-select">
                    <option value="">Seleccionar banco</option>
                    <?php foreach($bancos as $key=>$value): ?>
                    <option <?php echo (( $banco->idBanco == $value["id"] )?"selected":"") ?> value="<?= $value["id"] ?>"><?= $value["descripcion"] ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($validation->getError('idBanco')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('idBanco'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="idMoneda" class="col-sm-2 col-form-label">Banco</label>
            <div class="col-sm-10">
                

                <select name="idMoneda" id="idMoneda" class="form-select">
                    <option value="">Seleccionar banco</option>
                    <?php foreach($monedas as $key=>$value): ?>
                    <option <?php echo (( $banco->idMoneda == $value["id"] )?"selected":"") ?> value="<?= $value["id"] ?>"><?= $value["descripcion"] ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($validation->getError('idMoneda')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('idMoneda'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="nroCuenta" class="col-sm-2 col-form-label">Nº Cuenta</label>
            <div class="col-sm-10">
                <input type="text" name="nroCuenta" id="nroCuenta" class="form-control" value="<?= $banco->nroCuenta ?>">
                <?php if ($validation->getError('nroCuenta')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('nroCuenta'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="mb-3">
            <button class="btn btn-success" name="submit" value="submit"><i class="bi bi-save"></i> Guardar</button>
      
        </div>
    </form>

</div>

<script>

</script>