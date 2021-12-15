<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="https://altozano.pe/public/frontend/img/icon-altozano.png">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <base href="<?= base_url() ?>/">
    <title>Altozano</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/estilo.css">

    <!--   <script src="js/main.js"></script> -->

    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }

        .miNav {
            background: #002d5d;
        }

        .miNav a {
            color: white;
        }

        .miDrop {
            list-style-type: none;
            padding-top: 8px;
        }

        .miDrop:hover a {
            color: white !important;
        }

        .miDrop a {
            color: white !important;
            cursor: pointer;
            text-decoration: none;
        }

        .miDrop:hover ul {
            display: block;
        }
    </style>
</head>

<body>
    <?php

    ?>
    <nav class="navbar navbar-expand-lg miNav navbar-dark" style="height: 39px">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin">Ordenes</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="admin/oc/rendicion">Rendición</a>
                    </li>

                    <ul class="miDrop">

                        <li>
                            <a class="text-white">
                            <i class="bi bi-caret-down-fill"></i> Reportes
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="admin/oc/reporteOrdenes">Reporte Ordenes</a></li>
                                <li><a class="dropdown-item" href="admin/oc/reporteFinanzas">Reporte Finanzas</a></li>
                                <li><a class="dropdown-item" href="admin/oc/reporteTesoreria">Reporte Tesorería</a></li>
                            </ul>
                        </li>
                    </ul>



                    <?php if ($_SESSION["personal"]["idCargo"] == "2") : ?>
                        <ul class="miDrop">

                            <li>
                                <a class="text-white">
                                <i class="bi bi-caret-down-fill"></i> Maestros
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="admin/empresa">Empresas</a></li>
                                    <li><a class="dropdown-item" href="admin/personal">Personal</a></li>
                                    <li><a class="dropdown-item" href="admin/key">Key</a></li>
                                    <li><a class="dropdown-item" href="admin/banco">Bancos</a></li>

                                    <li><a class="dropdown-item" href="admin/clasecosto">Clase costo</a></li>
                                    <!--      <li><a class="dropdown-item" href="admin/cuenta1">Cuentas 1</a></li>
                                    <li><a class="dropdown-item" href="admin/cuenta2">Cuentas 2</a></li>
                                    <li><a class="dropdown-item" href="admin/cuenta3">Cuentas 3</a></li> -->
                                    <li><a class="dropdown-item" href="admin/clasecosto/cuentas">Cuentas Total</a></li>

                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>


                </ul>


                <ul class="miDrop">

                    <li>
                        <a class="text-white">
                            <?php
                            if ($_SESSION["personal"]["idCargo"] == "1") {
                                $cargo = "GERENCIA";
                            } else if ($_SESSION["personal"]["idCargo"] == "2") {
                                $cargo = "FINANZAS";
                            } else {
                                $cargo = "JEFE DIRECTO";
                            }
                            ?>

                            <?= $_SESSION["personal"]["nombres"] . " " . $_SESSION["personal"]["apellidoPaterno"] . " " . $_SESSION["personal"]["apellidoMaterno"] . " - " . $cargo ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="admin/logout"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br>
    <div class="container-fluid">
        <?php echo $body ?>
    </div>


</body>

</html>