<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BancoModel;
use App\Models\CentroModel;
use App\Models\ClasecostoModel;
use App\Models\Cuenta3Model;
use App\Models\EmpresaModel;
use App\Models\KeyModel;
use App\Models\OcModel;

use App\Models\MonedaModel;
use App\Models\OrdenDetalleModel;
use App\Models\PersonalModel;
use App\Models\RendicionItemCentroModel;
use App\Models\RendicionItemModel;
use App\Models\TipoOrdenModel;
use App\Models\TipoSolicitudModel;
use App\Models\RendicionModel;
use App\Models\TipoSolicitudRenModel;
use Config\Database;

class Rendicion extends BaseController
{
    private $db = "";
    private $table = "rendicion";
    private $nombre = "rendicion";
    private $lista = "rendiciones";
    private $dataView = [];
    private $model = "";

    public function __construct()
    {
        $this->db =  Database::connect();
        $this->dataView = [
            "lista" => $this->lista,
            "table" => $this->table,
            "nombre" => $this->nombre,
        ];
        $this->model = new RendicionModel();
    }

    public function index()
    {
        $data = $this->dataView;

        $data["rendiciones"] = (new RendicionModel())->getList();
      
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/rendicion/list', $data);
        
    }

    public function agregar()
    {
        $cuentas3 = $this->db->table("cuenta3 c3")
            ->select('c3.id c3_id, c3.descripcion c3_descripcion, c3.codigo c3_codigo, c2.id c2_id, c2.descripcion c2_descripcion, c2.codigo c2_codigo,c1.id c1_id, c1.descripcion c1_descripcion, c1.codigo c1_codigo, ca.id ca_id, ca.descripcion ca_descripcion, ca.codigo ca_codigo')
            ->join("cuenta2 c2", 'c3.idCuenta = c2.id')
            ->join("cuenta1 c1", 'c2.idCuenta = c1.id')
            ->join("clasecosto ca", 'c1.idCuenta = ca.id')

            ->where("c3.estado", "1")
            ->get()->getResult();

        $data =  [
            'empresas'   => (new EmpresaModel())->where("estado", "1")->where("idTipoEmpresa", "1")->findAll(),
            'empresas_total'   => (new EmpresaModel())->where("estado", "1")->findAll(),
            'keys'       => (new KeyModel())->where("estado", "1")->findAll(),
            'personal'   => (new PersonalModel())->where("estado", "1")->findAll(),
            'claseCosto'   => (new ClasecostoModel())->where("estado", "1")->findAll(),
            'banco'   => (new BancoModel())->where("estado", "1")->findAll(),
            'tipoOrden' => (new TipoOrdenModel())->where("estado", "1")->findAll(),
            'ordenes' => $this->db->table("orden")->select("id,codigo")->where("estado!=", "5")->get()->getResultArray(),
            'cuentas3' => $cuentas3,
        ];

        if ($this->request->getPost('submit')) {
            $rules = [
                'idEmpresa' => 'required'
            ];

            $errors = [
                'ruc' => [
                    'validateRuc' => 'Este RUC ya está registrado'
                ]
            ];

           

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $data['validation'] = $this->validator;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/rendicion/agregar', $data);
            } else {

         

                /*$cantOrden = $this->db->table("rendicion")
                    ->select('count(*) cant')
                    ->where("idTipoOrden", $this->request->getVar('idTipoOrden'))
                    ->get()->getRow();

                $cantOrden = $cantOrden->cant + 1;

                if ($cantOrden < 10) {
                    $cantOrden = "00" . $cantOrden;
                } else if ($cantOrden < 100) {
                    $cantOrden = "0" . $cantOrden;
                }

                $tipoOrden = $this->db->table("tipoOrden")
                    ->where("id", $this->request->getVar('idTipoOrden'))
                    ->get()->getRow();

                $codTipoOrden = $tipoOrden->codigo;

                $codigo = "Rend.".$codTipoOrden . $cantOrden . "-2021";*/

                $idTipoOrden = $this->request->getVar('idTipoOrden');
                $tipoOrden = $this->db->table("tipoOrden")
                            ->where("id", $idTipoOrden)
                            ->get()->getRow();

                $codTipoOrden = $tipoOrden->codigo;

                $a = $this->db->table("rendicion")
                    ->where("idTipoOrden",$idTipoOrden)
                    ->like("codigo",date("Y"), "both")
                    ->orderBy("id","desc")
                    ->get()->getRowArray();

            
                if($a){
                    $arr = explode("-",$a["codigo"]);
                    $num = substr($arr[0], -3);

                    $cantOrden = $num + 1;

                    if ($cantOrden < 10) {
                        $cantOrden = "00" . $cantOrden;
                    } else if ($cantOrden < 100) {
                        $cantOrden = "0" . $cantOrden;
                    }

                    $codigo = "Rend.".$codTipoOrden.$cantOrden."-".date("Y");
            
                }else{
                    $codigo = "Rend.".$codTipoOrden."001-".date("Y");
                }

                //calculando importe total
                $importeTotal = 0;
                $arr_v = $this->request->getVar('varioscentros_t');
                
                foreach($arr_v as $key => $value ){
                  

                    if(json_decode($value)){
                        $centros = json_decode($value);
                        foreach($centros as $key2 => $value2){
                            $importeTotal += $value2->monto;
                        }
                    }else{ //un solo centro
                        $importeTotal += floatval($this->request->getVar('monto')[$key]);
                    }
                }
        
                $datosInsert = [
                    "codigo" => $codigo,
                    "idPersonal" => $_SESSION["personal"]["id"],
                    "importe" => $importeTotal,
                    "idTipoSolicitudRen" => $this->request->getVar('idTipoSolicitud'),
                    "idTipoOrden" => $this->request->getVar('idTipoOrden'),
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idPersonalSoli" => $this->request->getVar('solicitado'),
                    "idPersonalJefe" => $this->request->getVar('jefe'),
                    "idOrden" => $this->request->getVar('idOrden'),
                    "idEmpresaEje" => $this->request->getVar('ejecutado'),
                    "idBanco_empresa" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "referencia" => $this->request->getVar('referencia'),
                ];
                $newRendicion = new RendicionModel();
                $newRendicion->save($datosInsert);
                $miIdRendicion = $newRendicion->getInsertID();

                //agregando Items
                $arr_v = $this->request->getVar('varioscentros_t');
                
                foreach($arr_v as $key => $value ){
                    $datosItem = [
                        "idRendicion" => $miIdRendicion,
                        "nroDoc" =>$this->request->getVar('nro')[$key],
                        "idEmpresaProv" =>$this->request->getVar('proveedor')[$key],
                        "detalle" =>$this->request->getVar('detalle')[$key],
                        "idCentro" =>$this->request->getVar('variosCentros')[$key],
                        "idCuenta" =>$this->request->getVar('variosCuentas')[$key],
                        "monto" =>$this->request->getVar('monto')[$key]
                    ];
                    $newRendicionItem = new RendicionItemModel();
                    $newRendicionItem->save($datosItem);
                    $idRendicionItem = $newRendicionItem->getInsertID();

                    if(json_decode($value)){ // varios centros
                        $centros = json_decode($value);
                        foreach($centros as $key2 => $value2){
                            $itemCentro = [
                                "idRendicionItem" => $idRendicionItem,
                                "detalle" => $value2->detalle,
                                "idCentro" => $value2->centro,
                                "idCuenta" => $value2->cuenta3,
                                "monto" => $value2->monto,
                            ];
                            $newRendicionItemCentro = new RendicionItemCentroModel();
                            $newRendicionItemCentro->save($itemCentro);
                        }
                    }
                }

                if ($_FILES["docs"]["name"][0] != "") {



                    foreach ($this->request->getFileMultiple('docs') as $file) {


                        $name = $file->getClientName();
                        $file->move('uploads/rendicion/'.$miIdRendicion,$name);


                        $data = [
                            'name' =>  $file->getClientName(),
                            'type'  => $file->getClientMimeType()
                        ];

                        $this->db->table("rendicion_imagen")
                            ->insert(array(
                                "idRendicion" => $miIdRendicion,
                                "imagen" => $name
                            ));

                        $msg = 'Files has been uploaded';
                    }
                }

                return redirect()->to(site_url('admin/rendicion/index'));
            }
        } else {
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/rendicion/agregar', $data);
        }
    }

    public function aprobar($idOrden)
    {
        if ($_SESSION["personal"]["idCargo"] == "3") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '3'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/rendicion/index'));
        } else if ($_SESSION["personal"]["idCargo"] == "2") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '4'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/rendicion/index'));
        } else if ($_SESSION["personal"]["idCargo"] == "1") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '1'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/rendicion/index'));
        }
    }

    public function editar($idRendicion)
    {

        $cuentas3 = $this->db->table("cuenta3 c3")
            ->select('c3.id c3_id, c3.descripcion c3_descripcion, c3.codigo c3_codigo, c2.id c2_id, c2.descripcion c2_descripcion, c2.codigo c2_codigo,c1.id c1_id, c1.descripcion c1_descripcion, c1.codigo c1_codigo, ca.id ca_id, ca.descripcion ca_descripcion, ca.codigo ca_codigo')
            ->join("cuenta2 c2", 'c3.idCuenta = c2.id')
            ->join("cuenta1 c1", 'c2.idCuenta = c1.id')
            ->join("clasecosto ca", 'c1.idCuenta = ca.id')

            ->where("c3.estado", "1")
            ->get()->getResult();

        $data =  [
            'empresas'   => (new EmpresaModel())->where("estado", "1")->where("idTipoEmpresa", "1")->findAll(),
            'empresas_total'   => (new EmpresaModel())->where("estado", "1")->findAll(),
            'keys'       => (new KeyModel())->where("estado", "1")->findAll(),
            'personal'   => (new PersonalModel())->where("estado", "1")->findAll(),
            'claseCosto'   => (new ClasecostoModel())->where("estado", "1")->findAll(),
            'banco'   => (new BancoModel())->where("estado", "1")->findAll(),
            'tipoOrden' => (new TipoOrdenModel())->where("estado", "1")->findAll(),
            'ordenes' => $this->db->table("orden")->select("id,codigo")->where("estado!=", "5")->get()->getResultArray(),
            'tipoSolicitudRen_all' => (new TipoSolicitudRenModel())->where("estado","1")->findAll(),
            'cuentas3' => $cuentas3,
        ];

        if ($this->request->getPost('submit')) {
         

            $rules = [
                'idEmpresa' => 'required'
            ];

            $errors = [
                'ruc' => [
                    'validateRuc' => 'Este RUC ya está registrado'
                ]
            ];

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $data['validation'] = $this->validator;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/rendicion/editar', $data);
            } else {

                //hallando monto
                //calculando importe total
                $importeTotal = 0;
                $arr_v = $this->request->getVar('varioscentros_t');
                
                foreach($arr_v as $key => $value ){
                  

                    if(json_decode($value)){
                        $centros = json_decode($value);
                        foreach($centros as $key2 => $value2){
                            $importeTotal += $value2->monto;
                        }
                    }else{ //un solo centro
                        $importeTotal += floatval($this->request->getVar('monto')[$key]);
                    }
                }

                $datosInsert = [
                    "id" => $idRendicion,
                    "idPersonal" => $_SESSION["personal"]["id"],
                    "importe" => $importeTotal,
                    "idTipoSolicitudRen" => $this->request->getVar('idTipoSolicitud'),
                    "idTipoOrden" => $this->request->getVar('idTipoOrden'),
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idPersonalSoli" => $this->request->getVar('solicitado'),
                    "idPersonalJefe" => $this->request->getVar('jefe'),
                    "idOrden" => $this->request->getVar('idOrden'),
                    "idEmpresaEje" => $this->request->getVar('ejecutado'),
                    "idBanco_empresa" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "referencia" => $this->request->getVar('referencia'),
                ];
                $newRendicion = new RendicionModel();
                $newRendicion->save($datosInsert);
           
                //borrando items anteriores
                $itemCentroBorrar = $this->db->table("rendicion_item")
                    ->where("idRendicion",$idRendicion)
                    ->get()->getResultArray();
                
                foreach ($itemCentroBorrar as $key => $value) {
                    $idRendicionItem = $value["id"];
                    $this->db->table("rendicion_itemcentro")
                    ->where("idRendicionItem",$idRendicionItem)
                    ->delete();
                }
                
                $this->db->table("rendicion_item")
                    ->where("idRendicion",$idRendicion)
                    ->delete();

                

                //agregando Items

                $arr_v = $this->request->getVar('varioscentros_t');
                
                foreach($arr_v as $key => $value ){
                    $datosItem = [
                        "idRendicion" => $idRendicion,
                        "nroDoc" =>$this->request->getVar('nro')[$key],
                        "idEmpresaProv" =>$this->request->getVar('proveedor')[$key],
                        "detalle" =>$this->request->getVar('detalle')[$key],
                        "idCentro" =>$this->request->getVar('variosCentros')[$key],
                        "idCuenta" =>$this->request->getVar('variosCuentas')[$key],
                        "monto" =>$this->request->getVar('monto')[$key]
                    ];
                    $newRendicionItem = new RendicionItemModel();
                    $newRendicionItem->save($datosItem);
                    $idRendicionItem = $newRendicionItem->getInsertID();

                    if(json_decode($value)){ // varios centros
                        $centros = json_decode($value);
                        foreach($centros as $key2 => $value2){
                            $itemCentro = [
                                "idRendicionItem" => $idRendicionItem,
                                "detalle" => $value2->detalle,
                                "idCentro" => $value2->centro,
                                "idCuenta" => $value2->cuenta3,
                                "monto" => $value2->monto,
                            ];
                            $newRendicionItemCentro = new RendicionItemCentroModel();
                            $newRendicionItemCentro->save($itemCentro);
                        }
                    }
                }

                if ($_FILES["docs"]["name"][0] != "") {



                    foreach ($this->request->getFileMultiple('docs') as $file) {


                        $name = $file->getClientName();
                        $file->move('uploads/rendicion/'.$idRendicion,$name);


                        $data = [
                            'name' =>  $file->getClientName(),
                            'type'  => $file->getClientMimeType()
                        ];

                        $this->db->table("rendicion_imagen")
                            ->insert(array(
                                "idRendicion" => $idRendicion,
                                "imagen" => $name
                            ));

                        $msg = 'Files has been uploaded';
                    }
                }



                return redirect()->to(site_url('admin/rendicion/index'));
            }
        } else {
            $rendicion = (new RendicionModel())->find($idRendicion);
            $data["o_rendicion"] =$rendicion;


            $data["o_items"] = (new RendicionItemModel())
                ->get_all($idRendicion);

            //Para varios centros        
            foreach ($data["o_items"] as $key => $value) {
                $data["o_items"][$key]["centros"] = (new RendicionItemCentroModel())->get_all($value["id"]);
            }

            

          
            $data["o_images"] = $this->db->table("rendicion_imagen")
                ->where("idRendicion",$idRendicion)->get()->getResultArray();

               
           
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/rendicion/editar', $data);
        }
    }
    public function ajaxEliminarImagen(){
        $idRendicionImagen = $this->request->getVar('idRendicionImagen');
        $rendicion_imagen = $this->db->table("rendicion_imagen")->where("id",$idRendicionImagen)->get()->getRowArray();

        if($rendicion_imagen){

            $this->db->table("rendicion_imagen")->where("id",$idRendicionImagen)->delete();
            
            if (file_exists("uploads/rendicion/".$rendicion_imagen["idRendicion"]."/".$rendicion_imagen["imagen"])) {
                unlink("uploads/rendicion/".$rendicion_imagen["idRendicion"]."/".$rendicion_imagen["imagen"]);
                echo json_encode(array("response"=>1));
            } else {
                echo json_encode(array("response"=>0));
            }
        }else{
            echo json_encode(array("response"=>0));
        }
    }

    public function getAjaxCuenta3_centro()
    {
        $idCentro = $this->request->getVar('idCentro');

        $cuentas3 = $this->db->table("centro_clase cc")
            ->select('c3.id c3_id, c3.descripcion c3_descripcion, c3.codigo c3_codigo, c2.id c2_id, c2.descripcion c2_descripcion, c2.codigo c2_codigo,c1.id c1_id, c1.descripcion c1_descripcion, c1.codigo c1_codigo, ca.id ca_id, ca.descripcion ca_descripcion, ca.codigo ca_codigo')
            ->join("clasecosto ca", 'cc.idClaseCosto = ca.id')
            ->join("cuenta1 c1", 'ca.id = c1.idCuenta')
            ->join("cuenta2 c2", 'c1.id = c2.idCuenta')
            ->join("cuenta3 c3", 'c2.id = c3.idCuenta')


            ->where("cc.idCentro = " . $idCentro)
            ->where("c3.estado", "1")

            ->get()->getResult();

        echo json_encode(array("response" => $cuentas3));
    }

    public function ver($idRendicion)
    {
        $rendicion = (new RendicionModel())->get_id($idRendicion);
        $data["o_rendicion"] =$rendicion;


        $data["o_items"] = (new RendicionItemModel())
            ->get_all($idRendicion);

        //Para varios centros        
        foreach ($data["o_items"] as $key => $value) {
            $data["o_items"][$key]["centros"] = (new RendicionItemCentroModel())->get_all($value["id"]);
        }

        $data["o_images"] = $this->db->table("rendicion_imagen")
            ->where("idRendicion",$idRendicion)->get()->getResultArray();

   

        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/rendicion/ver', $data);
    }

    public function reporteOrdenes()
    {
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/rendicion/reporteOrdenes');
    }

    public function reporteFinanzas()
    {
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/rendicion/reporteFinanzas');
    }

   

    public function getExcelFinanzas()
    {
        $fechaInicio = $_POST["fechaInicio"];
        $fechaFinal = $_POST["fechaFinal"]; 
        


        $filename = $fechaInicio . "_" . $fechaFinal . "_finanzas_reporte.xls";

        header('Content-Encoding: UTF-8');
        header("Content-type: application/x-msdownload; charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        echo "\xEF\xBB\xBF";
        

        $rendicionTotal = $this->db->table("rendicion")
            ->where("created_at > ", $fechaInicio)
            ->where("created_at < ", $fechaFinal)
            ->where("estado !=","5")->get()->getResultArray();

        $tabla = "";
        $tabla .= "<table>
            <tr>
                <th bgcolor='#00b050'><font color='white'>Empresa</font></th>
                <th bgcolor='#00b050'><font color='white'>Num OC</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Solicitante</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Área</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Beneficiario</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Año registro</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Mes registro</fotn></th>
                <th bgcolor='#002060'><font color='white'>Fecha</fotn></th>
                <th bgcolor='#002060'><font color='white'>Moneda</fotn></th>
                <th bgcolor='#002060'><font color='white'>Tipo de Cambio</fotn></th>

                
                <th bgcolor='#00b050'><font color='white'>Total S/</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Total $</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Ceco</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Detalle Ceco</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Nivel 3</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Nivel 1</fotn></th>
            </tr>
        </table>";
      
        foreach($rendicionTotal as $key => $value){
            //buscando si tiene centros
            $idRendicion = $value["id"];
            
            $items = $this->db->table("rendicion_item ri")
            ->select('ri.id ri_id, ri.nroDoc,ri.idEmpresaProv,ri.detalle,ri.monto,c1.descripcion cuenta1_descripcion, c3.descripcion cuenta3_descripcion, c3.codigo cuenta3_codigo,cc.descripcion centro_descripcion, cc.codigo centro_codigo,e.nombre e_nombre')
            
            ->join("empresa e","e.id = ri.idEmpresaProv")
            ->join("centro cc","cc.id = ri.idCentro","left")
            ->join("cuenta3 c3","c3.id = ri.idCuenta","left")
            ->join("cuenta2 c2","c2.id = c3.idCuenta","left")
            ->join("cuenta1 c1","c1.id = c2.idCuenta","left")
            ->where("ri.idRendicion",$idRendicion)
            ->get()->getResultArray();

           

            $orden = $this->db->table("rendicion o")
                    ->select("o.id,e.nombre empresa_nombre, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,o.created_at orden_fecha, m.simbolo, o.importe")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("orden","orden.id = o.idOrden")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")
                    ->where("o.created_at > ", $fechaInicio)
                    ->where("o.created_at < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id =", $idRendicion)
                    ->get()->getRowArray();


                
            foreach ($items as $key2 => $item) {
            

                $centros = $this->db->table("rendicion_itemcentro ric")
                    ->select('ric.id ric_id,ric.detalle,ric.monto,c1.descripcion cuenta1_descripcion, c3.descripcion cuenta3_descripcion, c3.codigo cuenta3_codigo,cc.descripcion centro_descripcion, cc.codigo centro_codigo')
        
                    ->join("centro cc","cc.id = ric.idCentro","left")
                    ->join("cuenta3 c3","c3.id = ric.idCuenta","left")
                    ->join("cuenta2 c2","c2.id = c3.idCuenta","left")
                    ->join("cuenta1 c1","c1.id = c2.idCuenta","left")
                    ->where("ric.idRendicionItem",$item["ri_id"])
                    ->get()->getResultArray();
                    
               
                if(count($centros)){//tiene varios centros el item
                
                    foreach ($centros as $key => $centro) {
                        $tabla .=  "<table class='table'>";
                        $tabla .= "<tr>";
                        $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
                        $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
                        $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
                        $tabla .= "<td>" . $orden["to_descripcion"] . "</td>";
                        $tabla .= "<td>" . $item["e_nombre"] . "</td>";
                        $tabla .= "<td>" . substr($orden["orden_fecha"],0,4) . "</td>";
                        $tabla .= "<td>" . substr($orden["orden_fecha"],5,2) . "</td>";
                        $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
                        $tabla .= "<td>" . $orden["simbolo"] . "</td>";
                        $tabla .= "<td>0.00</td>";
                        $tabla .= "<td>" . $centro["monto"]  . "</td>";
                        $tabla .= "<td>0.00</td>";
                        $tabla .= "<td>".$centro["centro_codigo"]."</td>";
                        $tabla .= "<td>" . $centro["centro_descripcion"]  . "</td>";
                        $tabla .= "<td>" . $centro["cuenta3_codigo"] . "</td>";
                        $tabla .= "<td>" . $centro["cuenta3_descripcion"] . "</td>";
                        $tabla .= "</tr>";
                        $tabla .= "</table>"; 
                    }
                }else{
              
                    $tabla .=  "<table class='table'>";
                    $tabla .= "<tr>";
                    $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
                    $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
                    $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
                    $tabla .= "<td>" . $orden["to_descripcion"] . "</td>";
                    $tabla .= "<td>" . $item["e_nombre"] . "</td>";
                    $tabla .= "<td>" . substr($orden["orden_fecha"],0,4) . "</td>";
                    $tabla .= "<td>" . substr($orden["orden_fecha"],5,2) . "</td>";
                    $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
                    $tabla .= "<td>" . $orden["simbolo"] . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>" . $item["monto"]  . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>".$item["centro_codigo"]."</td>";
                    $tabla .= "<td>" . $item["centro_descripcion"]  . "</td>";
                    $tabla .= "<td>" . $item["cuenta3_codigo"] . "</td>";
                    $tabla .= "<td>" . $item["cuenta3_descripcion"] . "</td>";
        
                    $tabla .= "</tr>";
                    $tabla .= "</table>"; 
                }           
            }
        }
        $tabla .= '</body></html>';
        echo $tabla;
    }

    public function reporteTesoreria(){
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/rendicion/reporteTesoreria');
    }

    public function getExcelTesoreria(){
        $fechaInicio = $_POST["fechaInicio"];
        $fechaFinal = $_POST["fechaFinal"]; 

    /*     $fechaInicio = "2022-01-01";
        $fechaFinal = "2022-05-05";  */
        


        $filename = $fechaInicio . "_" . $fechaFinal . "_tesoreria_reporte.xls";

        header('Content-Encoding: UTF-8');
        header("Content-type: application/x-msdownload; charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        echo "\xEF\xBB\xBF";
        

        $rendicionTotal = $this->db->table("rendicion")
            ->where("created_at > ", $fechaInicio)
            ->where("created_at < ", $fechaFinal)
            ->where("estado !=","5")->get()->getResultArray();

        $tabla = "";
        $tabla .= "<table>
            <tr>
                <th bgcolor='#00b050'><font color='white'>Empresa</font></th>
                <th bgcolor='#00b050'><font color='white'>Num OC</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Solicitante</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Área</fotn></th>
               
                <th bgcolor='#00b050'><font color='white'>Año registro</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Mes registro</fotn></th>
                <th bgcolor='#002060'><font color='white'>Fecha</fotn></th>
                <th bgcolor='#002060'><font color='white'>Moneda</fotn></th>
                <th bgcolor='#002060'><font color='white'>Tipo de Cambio</fotn></th>

                
                <th bgcolor='#00b050'><font color='white'>Total S/</fotn></th>
            
        
            </tr>
        </table>";
      
        foreach($rendicionTotal as $key => $value){
            //buscando si tiene centros
            $idRendicion = $value["id"];
            
            $items = $this->db->table("rendicion_item ri")
            ->select('ri.id ri_id, ri.nroDoc,ri.idEmpresaProv,ri.detalle,ri.monto,c1.descripcion cuenta1_descripcion, c3.descripcion cuenta3_descripcion, c3.codigo cuenta3_codigo,cc.descripcion centro_descripcion, cc.codigo centro_codigo,e.nombre e_nombre')
            
            ->join("empresa e","e.id = ri.idEmpresaProv")
            ->join("centro cc","cc.id = ri.idCentro","left")
            ->join("cuenta3 c3","c3.id = ri.idCuenta","left")
            ->join("cuenta2 c2","c2.id = c3.idCuenta","left")
            ->join("cuenta1 c1","c1.id = c2.idCuenta","left")
            ->where("ri.idRendicion",$idRendicion)
            ->get()->getResultArray();

           

            $orden = $this->db->table("rendicion o")
                    ->select("o.id,e.nombre empresa_nombre, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,o.created_at orden_fecha, m.simbolo, o.importe")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("orden","orden.id = o.idOrden")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")
                    ->where("o.created_at > ", $fechaInicio)
                    ->where("o.created_at < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id =", $idRendicion)
                    ->get()->getRowArray();


                
    
 
            $tabla .=  "<table class='table'>";
            $tabla .= "<tr>";
            $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
            $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
            $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
            $tabla .= "<td>" . $orden["to_descripcion"] . "</td>";
            $tabla .= "<td>" . substr($orden["orden_fecha"],0,4) . "</td>";
            $tabla .= "<td>" . substr($orden["orden_fecha"],5,2) . "</td>";
            $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
            $tabla .= "<td>" . $orden["simbolo"] . "</td>";
            $tabla .= "<td>0.00</td>";
            $tabla .= "<td>" . $orden["importe"]  . "</td>";

            $tabla .= "</tr>";
            $tabla .= "</table>"; 
                         
            
        }
        $tabla .= '</body></html>';
        echo $tabla;
        
    }

    

    public function eliminar($id)
    {
        $datosUpdate = [
            "id" => $id,
            "estado" => '5'
        ];
        $this->model->save($datosUpdate);
        return redirect()->to(site_url('admin/rendicion'));
    }
}
