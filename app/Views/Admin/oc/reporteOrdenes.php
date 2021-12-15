<h4>Ordenes</h4>
<form action="admin/oc/getExcelOrdenes" method="POST">
    <div class="row">
        <div class="col-md-4">
            <div class="input-group">

                <span class="input-group-text">Fecha</span>
                <input type="date" name="fechaInicio" id="fechaInicio" aria-label="First name" class="form-control">
                <input type="date" name="fechaFinal" id="fechaFinal" aria-label="Last name" class="form-control">
                <button type="submit" class="btn btn-success" id="descargar">Descargar</button>

            </div>
        </div>
    </div>
</form>
<script>
    /* window.onload = function(){
        descargar.onclick = function(){
            //location.href = "admin/oc/getExcelOrdenes";
            idEmpresa = 2;
            var formdata = new FormData();
                formdata.append("idEmpresa", idEmpresa);

                fetch("admin/oc/getExcelOrdenes", {
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
                    })
                    .catch(error => console.log('error', error));
        }
    } */
</script>