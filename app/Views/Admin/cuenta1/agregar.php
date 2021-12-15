<div class="container">
    <h3>Agregar Cuenta 1</h3>
    <?php $validation = \Config\Services::validation(); ?>
    <form action="" method="POST">
        <div class="mb-3 row">
            <label for="codigo" class="col-sm-2 col-form-label">Código</label>
            <div class="col-sm-10">
                <input type="text" name="codigo" class="form-control" id="codigo" value="">
                <?php if ($validation->getError('codigo')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('codigo'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
            <div class="col-sm-10">
                <input type="text" name="descripcion" class="form-control" id="descripcion" value="">
                <?php if ($validation->getError('descripcion')) { ?>
                    <div class='alert alert-danger mt-2'>
                        <?= $error = $validation->getError('descripcion'); ?>
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