<?php if (isset($msg)) { ?>
    <div class='alert alert-danger mt-2'>
        <?= $error = $validation->getError('email'); ?>
    </div>
<?php } ?>

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?php $validation = \Config\Services::validation(); ?>
            <form method="POST">
                <div class="text-center mt-5">
                    <img src="images/logo1.png" alt="" style="">
                </div>
                <div class="mb-3 mt-4">
                    <label for="email" class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" id="email">

                    <?php if ($validation->getError('email')) { ?>
                        <div class='alert alert-danger mt-2'>
                            <?= $error = $validation->getError('email'); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" id="password">

                    <?php if ($validation->getError('password')) { ?>
                        <div class='alert alert-danger mt-2'>
                            <?= $error = $validation->getError('password'); ?>
                        </div>
                    <?php } ?>
                </div>

                <button type="submit" name="submit" class="btn btn-success" value="Submit">Entrar</button>
            </form>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>