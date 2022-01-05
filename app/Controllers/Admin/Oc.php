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

use Config\Database;

class Oc extends BaseController
{
    private $db = "";
    private $table = "orden";
    private $nombre = "oc";
    private $lista = "ocs";
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
        $this->model = new OcModel();
    }

    public function index()
    {
        $data = $this->dataView;
        $ocs = $this->db->table("orden o")
            ->select('o.estado orden_estado,o.idPersonal,o.id,o.codigo orden_codigo,o.fecha,ts.descripcion tipoSolicitud_desc,e.nombre empresa_nombre,c.descripcion centro_desc,cu3.codigo cuenta3_cod,cu3.descripcion cuenta3_desc,peso.nombres peso_nombres, peso.apellidoPaterno peso_apellidoPaterno,peso.apellidoMaterno peso_apellidoMaterno,eje.nombre eje_nombre,o.importe,m.descripcion moneda_descripcion')
            ->join("personal peso", 'peso.id = o.idPersonalSoli')
            ->join("tipoSolicitud ts", 'o.idTipoSolicitud = ts.id')
            ->join("empresa e", 'e.id = o.idEmpresa')
            ->join("empresa eje", 'eje.id = o.idEmpresaEje')
            ->join("moneda m", 'm.id = o.idMoneda')
            ->join("centro c", 'c.id = o.idCentroCosto', 'left')
            ->join("cuenta3 cu3", 'cu3.id = o.idCuenta', 'left')
            ->where("o.estado !=", "5")
            ->get()->getResult();

        $data["ocs"] = $ocs;
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/oc/list', $data);
    }

    public function aprobar($idOrden)
    {
        if ($_SESSION["personal"]["idCargo"] == "3") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '3'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/oc/index'));
        } else if ($_SESSION["personal"]["idCargo"] == "2") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '4'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/oc/index'));
        } else if ($_SESSION["personal"]["idCargo"] == "1") {
            $datosUpdate = [
                "id" => $idOrden,
                "estado" => '1'
            ];
            $this->model->save($datosUpdate);
            return redirect()->to(site_url('admin/oc/index'));
        }
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
                $this->template->render('Admin/oc/agregar', $data);
            } else {

                $cantOrden = $this->db->table("orden")
                    ->select('count(*) cant')
                    ->where("idTipoOrden", $this->request->getVar('idTipoOrden'))
                    ->where('estado !=','5')
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

                $codigo = $codTipoOrden . $cantOrden . "-".date("Y");

                //hallando monto
                $detalles = $this->request->getVar('detalle');
                $monedas = $this->request->getVar('moneda');
                $monto = 0;

                
                foreach ($detalles as $key => $value) {
                    if($monedas[$key]!=""){

                        $monto += $monedas[$key];
                    }
                }


                $idcuenta3 = $this->request->getVar('idCuenta3');
                $varioscentros = $this->request->getVar('varioscentros');
                $varioscuentas = $this->request->getVar('varioscuentas');
                if($varioscentros){
                    foreach ($varioscentros as $key => $value) {

                        $idcuenta3 = $varioscuentas[$key];
 
                    }
                }
     

                $datosInsert = [
                    "codigo" => $codigo,
                    "idPersonal" => $_SESSION["personal"]["id"],
                    "texto" => $this->request->getVar('texto'),
                    "importe" => $monto,
                    "idTipoSolicitud" => $this->request->getVar('idTipoSolicitud'),
                    "fecha" => date("Y-m-d"),
                    "nombre" => $this->request->getVar('nombre'),
                    "idTipoOrden" => $this->request->getVar('idTipoOrden'),
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idCentroCosto" => $this->request->getVar('idCentroCosto'),
                    "idPersonalSoli" => $this->request->getVar('solicitado'),
                    "idPersonalJefe" => $this->request->getVar('jefe'),
                    "idCuenta" => $idcuenta3,
                    "objeto" => $this->request->getVar('objeto'),
                    "idEmpresaEje" => $this->request->getVar('ejecutado'),
                    "idBanco_empresa" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "referencia" => $this->request->getVar('referencia'),
                ];
                $this->model->save($datosInsert);
                $miId = $this->model->getInsertID();



                //insertando detalles
                foreach ($detalles as $key => $value) {
                    $datosDetalles = array();
                    $datosDetalles["idOrden"] = $miId;
                    $datosDetalles["descripcion"] = $value;
                    $datosDetalles["monto"] = $monedas[$key];
                    (new OrdenDetalleModel())->save($datosDetalles);
                }

                $varioscentros = $this->request->getVar('varioscentros');
                $porcentajecentro = $this->request->getVar('porcentajecentro');
                $varioscuentas = $this->request->getVar('varioscuentas');
                
                if($varioscentros){
                    foreach ($varioscentros as $key => $value) {
                        $orden_centro = array();
                        $orden_centro["idOrden"] = $miId;
                        $orden_centro["idCentro"] = $varioscentros[$key];
                        $orden_centro["idCuenta"] = $varioscuentas[$key];
                        $orden_centro["porcentaje"] = $porcentajecentro[$key];
    
                        $this->db->table("orden_centro")->insert($orden_centro);
                    }
                }

                if ($_FILES["docs"]["name"][0] != "") {



                    foreach ($this->request->getFileMultiple('docs') as $file) {


                        $name = uniqid().'.'.$file->guessExtension();
                        $file->move('uploads/'.$miId,$name);


                        $data = [
                            'name' =>  $file->getClientName(),
                            'type'  => $file->getClientMimeType()
                        ];

                        $this->db->table("orden_imagen")
                            ->insert(array(
                                "idOrden" => $miId,
                                "imagen" => $name
                            ));
                       // $save = $builder->insert($data);

                        $msg = 'Files has been uploaded';
                    }
                }



                return redirect()->to(site_url('admin/oc/index'));
            }
        } else {

            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/oc/agregar', $data);
        }
    }

    public function editar($idOrden)
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
                $this->template->render('Admin/oc/agregar', $data);
            } else {

                $cantOrden = $this->db->table("orden")
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

                $codigo = $codTipoOrden . $cantOrden . "-2021";

                //hallando monto
                $detalles = $this->request->getVar('detalle');
                $monedas = $this->request->getVar('moneda');
                $monto = 0;

                foreach ($detalles as $key => $value) {
                    $monto += $monedas[$key];
                }

                $idcuenta3 = $this->request->getVar('idCuenta3');
                $varioscentros = $this->request->getVar('varioscentros');
                $varioscuentas = $this->request->getVar('varioscuentas');
                if($varioscentros){
                    foreach ($varioscentros as $key => $value) {

                        $idcuenta3 = $varioscuentas[$key];
 
                    }
                }

                $datosInsert = [
                    "id" => $idOrden,
                    "texto" => $this->request->getVar('texto'),
                    "importe" => $monto,
                    "idTipoSolicitud" => $this->request->getVar('idTipoSolicitud'),
                    "fecha" => date("Y-m-d"),
                    "nombre" => $this->request->getVar('nombre'),
                    "idTipoOrden" => $this->request->getVar('idTipoOrden'),
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idCentroCosto" => $this->request->getVar('idCentroCosto'),
                    "idPersonalSoli" => $this->request->getVar('solicitado'),
                    "idPersonalJefe" => $this->request->getVar('jefe'),
                    "idCuenta" => $idcuenta3,
                    "objeto" => $this->request->getVar('objeto'),
                    "idEmpresaEje" => $this->request->getVar('ejecutado'),
                    "idBanco_empresa" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "referencia" => $this->request->getVar('referencia'),
                ];
                $this->model->save($datosInsert);
                $miId = $idOrden;

                $this->db->table("orden_detalle")
                    ->where("idOrden",$idOrden)
                    ->delete();

                //insertando detalles
                foreach ($detalles as $key => $value) {
                    $datosDetalles = array();
                    $datosDetalles["idOrden"] = $miId;
                    $datosDetalles["descripcion"] = $value;
                    $datosDetalles["monto"] = $monedas[$key];
                    (new OrdenDetalleModel())->save($datosDetalles);
                } 

                $varioscentros = $this->request->getVar('varioscentros');
                $varioscuentas = $this->request->getVar('varioscuentas');
                $porcentajecentro = $this->request->getVar('porcentajecentro');
                
                $this->db->table("orden_centro")
                    ->where("idOrden",$idOrden)
                    ->delete();
                if($varioscentros){
                    foreach ($varioscentros as $key => $value) {
                        $orden_centro = array();
                        $orden_centro["idOrden"] = $miId;
                        $orden_centro["idCentro"] = $varioscentros[$key];
                        $orden_centro["idCuenta"] = $varioscuentas[$key];
                        $orden_centro["porcentaje"] = $porcentajecentro[$key];
    
                        $this->db->table("orden_centro")->insert($orden_centro);
                    }
                }
             
                if ($_FILES["docs"]["name"][0] != "") {



                    foreach ($this->request->getFileMultiple('docs') as $file) {


                        $name = uniqid().'.'.$file->guessExtension();
                        $file->move('uploads/'.$miId,$name);


                        $data = [
                            'name' =>  $file->getClientName(),
                            'type'  => $file->getClientMimeType()
                        ];

                        $this->db->table("orden_imagen")
                            ->insert(array(
                                "idOrden" => $miId,
                                "imagen" => $name
                            ));
                       // $save = $builder->insert($data);

                        $msg = 'Files has been uploaded';
                    }
                }



                return redirect()->to(site_url('admin/oc/index'));
            }
        } else {
            $orden = (new OcModel())->find($idOrden);
            $data["o_orden"] =$orden;
            $data["o_tipoOrden"] = (new TipoOrdenModel())->find($orden["idTipoOrden"]);
            $data["o_tipoSolicitud"] = (new TipoSolicitudModel())->find($orden["idTipoSolicitud"]);
            $data["o_empresa"] = (new EmpresaModel())->find($orden["idEmpresa"]);
            $data["o_centroCosto"] = (new CentroModel())->find($orden["idCentroCosto"]);
            $data["o_key"] = (new KeyModel())->find($data["o_centroCosto"]["idKey"]);
            $data["o_centros"] = $this->db->table("orden_centro oc")
            ->join("centro c","c.id = oc.idCentro")
            ->join("key k","k.id = c.idKey")
            ->where("oc.idOrden",$idOrden)->get()->getResultArray();

            $data["o_detalles"] = $this->db->table("orden_detalle od")
            ->where("od.idOrden",$idOrden)->get()->getResultArray();

            $data["o_images"] = $this->db->table("orden_imagen")
                ->where("idOrden",$idOrden)->get()->getResultArray();

           
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/oc/editar', $data);
        }
    }
    public function ajaxEliminarImagen(){
        $idOrdenImagen = $this->request->getVar('idOrdenImagen');
        $orden_imagen = $this->db->table("orden_imagen")->where("id",$idOrdenImagen)->get()->getRowArray();

        if($orden_imagen){

            $this->db->table("orden_imagen")->where("id",$idOrdenImagen)->delete();
            
            if (file_exists("uploads/".$orden_imagen["idOrden"]."/".$orden_imagen["imagen"])) {
                unlink("uploads/".$orden_imagen["idOrden"]."/".$orden_imagen["imagen"]);
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

    public function ver($idOrden)
    {
        $data["orden"] = (new OcModel())->find($idOrden);
        $data["empresa"] = (new EmpresaModel())->find($data["orden"]["idEmpresa"]);
        $data["personalSoli"] = (new PersonalModel())->find($data["orden"]["idPersonalSoli"]);
        $data["personalJefe"] = (new PersonalModel())->find($data["orden"]["idPersonalJefe"]);
        $data["ejecutado"] = (new EmpresaModel())->find($data["orden"]["idEmpresaEje"]);
        $data["cuenta"] = (new Cuenta3Model())->find($data["orden"]["idCuenta"]);
        $data["ordenDetalle"] = (new OrdenDetalleModel())->where("idOrden", $idOrden)->get()->getResult();
        $data["tipoOrden"] = (new TipoOrdenModel())->find($data["orden"]["idTipoOrden"]);
        $data["tipoSolicitud"] = (new TipoSolicitudModel())->find($data["orden"]["idTipoSolicitud"]);

        $data["images"] = $this->db->table("orden_imagen")->where("idOrden",$idOrden)->get()->getResult();
        $data["moneda"] = (new MonedaModel())->find($data["orden"]["idMoneda"]);

        $data["banco"] = $this->db->table("banco_empresa be")
            ->select("be.id,be.nroCuenta,b.descripcion")
            ->join("banco b", "b.id = be.idBanco")
            ->where("be.id", $data["orden"]["idBanco_empresa"])
            ->get()->getRow();


        $key = $this->db->table("centro c")
            ->select("c.descripcion descripcion_centro,c.codigo codigo_centro,k.descripcion key_descripcion")
            ->join("key k", "k.id = c.idKey")
            ->where("c.id", $data["orden"]["idCentroCosto"])
            ->get()->getRow();

        if ($data["cuenta"] == NULL) {
            $orden_centros = $this->db->table("orden_centro oc")
                ->select("c.codigo codigo_centro,c.descripcion descripcion_centro,oc.porcentaje")
                ->join("centro c", "c.id = oc.idCentro")
                ->where("oc.idOrden", $idOrden)
                ->get()->getResult();
            $data["orden_centros"] = $orden_centros;
        } else {
            $cuenta = $this->db->table("cuenta3 c3")
                ->select("c3.codigo c3_codigo,c3.descripcion c3_descripcion,c2.codigo c2_codigo,c2.descripcion c2_descripcion,c1.codigo c1_codigo,c1.descripcion c1_descripcion,cc.codigo cc_codigo,cc.descripcion cc_descripcion")
                ->join("cuenta2 c2", "c3.idCuenta = c2.id")
                ->join("cuenta1 c1", "c2.idCuenta = c1.id")
                ->join("clasecosto cc", "c1.idCuenta = cc.id")
                ->where("c3.id", $data["orden"]["idCuenta"])->get()->getRow();
            $data["cuenta"] = $cuenta;
        }


        $data["key"] = $key;

        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/oc/ver', $data);
    }

    public function reporteOrdenes()
    {
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/oc/reporteOrdenes');
    }

    public function reporteFinanzas()
    {
        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/oc/reporteFinanzas');
    }

    public function getExcelOrdenes()
    {

        $fechaInicio = $_POST["fechaInicio"];
        $fechaFinal = $_POST["fechaFinal"];

        $filename = $fechaInicio . "_" . $fechaFinal . "_reporte.xls";


        $ordenes = $this->db->table("orden")
            ->where("fecha > ", $fechaInicio)
            ->where("fecha < ", $fechaFinal)
            ->where("estado !=", "5")
            ->get()->getResult();

        foreach ($ordenes as $key => $value) {
            $detalles = $this->db->table("orden_detalle")
                ->where("idOrden", $value->id)
                ->get()->getResult();
            $ordenes[$key]->detalle = $detalles;
        }



        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

        $tabla = "<table>
            <tr>
                <th>Codigo</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Descripcion</th>
                <th>Detalles</th>
            </tr>
        </table>";
        foreach ($ordenes as $key => $value) {
            $tabla .=  "<table>";
            $tabla .= "<tr>";
            $tabla .= "<td>" . $value->codigo . "</td>";
            $tabla .= "<td>" . $value->importe . "</td>";
            $tabla .= "<td>" . $value->fecha . "</td>";
            $tabla .= "<td>" . $value->texto . "</td>";

            $detalle = $value->detalle;

            foreach ($detalle as $key2 => $value2) {
                $tabla .= "<td>" . $value2->descripcion . "</td>";
                $tabla .= "<td>" . $value2->monto . "</td>";
            }

            $tabla .= "</tr>";
            $tabla .= "</table>";
        }

        echo $tabla;
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
        

        $ordenesTotal = $this->db->table("orden")
            ->where("fecha > ", $fechaInicio)
            ->where("fecha < ", $fechaFinal)
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
                <th bgcolor='#002060'><font color='white'>Glosa</fotn></th>
                <th bgcolor='#002060'><font color='white'>Objeto del contrato</fotn></th>
                
                <th bgcolor='#00b050'><font color='white'>Total S/</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Total $</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Ceco</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Detalle Ceco</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Nivel 3</fotn></th>
                <th bgcolor='#00b050'><font color='white'>Nivel 1</fotn></th>
            </tr>
        </table>";
      
        foreach($ordenesTotal as $key => $value){
            if($value["idCuenta"] == "0"){ //multiples centros
                $orden = $this->db->table("orden o")
                    ->select("o.id,e.nombre empresa_nombre, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,o.fecha orden_fecha, m.simbolo, o.objeto,o.idCuenta idCuenta3, o.importe")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")
                    ->where("o.fecha > ", $fechaInicio)
                    ->where("o.fecha < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id =", $value["id"])
                    ->get()->getRowArray();

          

                $centros = $this->db->table("orden_centro")
                        ->where("idOrden",$value["id"])
                        ->get()->getResultArray();
                $detalles = $this->db->table("orden_detalle")
                ->where("idOrden",$value["id"])
                ->get()->getResultArray();
     
                foreach ($centros as $keyC => $valueC) {
                    $value_cuenta = $this->db->table("centro")
                        ->where("id",$valueC["idCentro"])->get()->getRowArray();
                   
                       

                    
                    $tabla .=  "<table>";
                    $tabla .= "<tr>";
                    $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
                    $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
                    $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
                    $tabla .= "<td>" . $orden["to_descripcion"] . "</td>";
                    $tabla .= "<td>" . $orden["eje_nombre"] . "</td>";
                    $tabla .= "<td>" . substr($orden["orden_fecha"],0,4) . "</td>";
                    $tabla .= "<td>" . substr($orden["orden_fecha"],5,2) . "</td>";
                    $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
                    $tabla .= "<td>" . $orden["simbolo"] . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>".$value_cuenta["codigo"]."</td>";
                    $tabla .= "<td>" . $detalles[$keyC]["descripcion"]  . "</td>";
                    $tabla .= "<td>" . $detalles[$keyC]["monto"]  . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>" . $value_cuenta["codigo"] . "</td>";
                    $tabla .= "<td>" . $value_cuenta["descripcion"] . "</td>";
                    $tabla .= "<td> - </td>";
                    $tabla .= "<td> - </td>";
        
                    $tabla .= "</tr>";
                    $tabla .= "</table>"; 
                }
            }
            else{
                $ordenes = $this->db->table("orden o")
                    ->select("o.id,e.nombre empresa_nombre, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,o.fecha orden_fecha, m.simbolo, o.objeto,o.idCuenta idCuenta3, o.importe, c1.descripcion cuenta1_descripcion, c3.descripcion cuenta3_descripcion, c3.codigo cuenta3_codigo,cc.descripcion centro_descripcion, cc.codigo centro_codigo")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")
                    ->join("centro cc","cc.id = o.idCentroCosto")
                    ->join("cuenta3 c3","c3.id = o.idCuenta")
                    ->join("cuenta2 c2","c2.id = c3.idCuenta")
                    ->join("cuenta1 c1","c1.id = c2.idCuenta")
                    ->where("o.fecha > ", $fechaInicio)
                    ->where("o.fecha < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id !=", $value["id"])
                    ->get()->getResult();
        
               
                
                foreach ($ordenes as $key => $value) {
                 
                    $tabla .=  "<table>";
                    $tabla .= "<tr>";
                    $tabla .= "<td>" . $value->empresa_nombre . "</td>";
                    $tabla .= "<td>" . $value->orden_codigo . "</td>";
                    $tabla .= "<td>" . $value->soli_nombres . "</td>";
                    $tabla .= "<td>" . $value->to_descripcion . "</td>";
                    $tabla .= "<td>" . $value->eje_nombre . "</td>";
                    $tabla .= "<td>" . substr($value->orden_fecha,0,4) . "</td>";
                    $tabla .= "<td>" . substr($value->orden_fecha,5,2) . "</td>";
                    $tabla .= "<td>" . date("d/m/Y", strtotime($value->orden_fecha) ) . "</td>";
                    $tabla .= "<td>" . $value->simbolo . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>".$value->centro_codigo."-".$value->cuenta3_codigo."</td>";
                    $tabla .= "<td>" . $value->objeto . "</td>";
                    $tabla .= "<td>" . $value->importe . "</td>";
                    $tabla .= "<td>0.00</td>";
                    $tabla .= "<td>" . $value->centro_codigo . "</td>";
                    $tabla .= "<td>" . $value->centro_descripcion . "</td>";
                    $tabla .= "<td>" . $value->cuenta3_descripcion . "</td>";
                    $tabla .= "<td>" . $value->cuenta1_descripcion . "</td>";
        
                  //  $detalle = $value->detalle;
        
                    /*foreach ($detalle as $key2 => $value2) {
                        $tabla .= "<td>" . $value2->descripcion . "</td>";
                        $tabla .= "<td>" . $value2->monto . "</td>";
                    }*/
        
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
        $this->template->render('Admin/oc/reporteTesoreria');
    }

    public function getExcelTesoreria(){
        $fechaInicio = $_POST["fechaInicio"];
        $fechaFinal = $_POST["fechaFinal"];

        $filename = $fechaInicio . "_" . $fechaFinal . "_tesoreria_reporte.xls";

       header('Content-Encoding: UTF-8');
        header("Content-type: application/x-msdownload; charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        echo "\xEF\xBB\xBF";
        
        $totalImportes = 0;
        $cantidadRegistroSoles = 0;
        $cantidadRegistroDolares = 0;
        $totalImporteSoles = 0;
        $totalImporteDolares = 0;

        $ordenesTotal = $this->db->table("orden")
            ->where("fecha > ", $fechaInicio)
            ->where("fecha < ", $fechaFinal)
            ->where("estado !=","5")->get()->getResultArray();
           
        $tabla = "";
        $tabla .= "<table>
            <tr>
                <th style='border:1px solid black'>Empresa</th>
                <th style='border:1px solid black'>Fecha Solicitud</th>
                <th style='border:1px solid black'>Solicitud</th>
                <th style='border:1px solid black'>Número de OC</th>
                <th style='border:1px solid black'>Solicitante</th>
                <th style='border:1px solid black'>Beneficiario</th>
                <th style='border:1px solid black'>RUC Beneficiario</th>
                <th style='border:1px solid black'>Banco Beneficiario</th>
                <th style='border:1px solid black'>Objeto de Contrato</th>
                <th style='border:1px solid black'>Moneda</th>
                <th style='border:1px solid black'>Importe</th>
                <th style='border:1px solid black'>Glosa</th>
                <th style='border:1px solid black'>Ceco</th>
                <th style='border:1px solid black'>Detalle Ceco</th>
                <th style='border:1px solid black'>Cuenta</th>
                <th style='border:1px solid black'>Nivel 3</th>
                <th style='border:1px solid black'>Nivel 1</th>
                
            </tr>
        </table>";
      
        foreach($ordenesTotal as $key => $value){
            if($value["idCuenta"] == "0"){ //multiples centros
                $orden = $this->db->table("orden o")
                    ->select("o.id,e.nombre empresa_nombre,o.fecha orden_fecha,ts.descripcion ts_descripcion, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,eje.ruc eje_ruc, m.simbolo,be.nroCuenta be_nroCuenta,b.descripcion banco_descripcion, o.objeto,o.idCuenta idCuenta3, o.importe")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("tipoSolicitud ts","ts.id = o.idTipoSolicitud")
                    ->join("banco_empresa be","be.id = o.idBanco_empresa")
                    ->join("banco b","b.id = be.idBanco")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")

                    ->where("o.fecha > ", $fechaInicio)
                    ->where("o.fecha < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id =", $value["id"])
                    ->get()->getRowArray();
  
          
                

                $centros = $this->db->table("orden_centro")
                        ->where("idOrden",$value["id"])
                        ->get()->getResultArray();
                
                $detalles = $this->db->table("orden_detalle")
                ->where("idOrden",$value["id"])
                ->get()->getResultArray();

     
                foreach ($centros as $keyC => $valueC) {
                    if($orden["simbolo"] == "PEN"){
                        $cantidadRegistroSoles++;
                        $totalImporteSoles += $detalles[$keyC]["monto"];
                    }else{
                        $cantidadRegistroDolares++;
                        $totalImporteDolares += $detalles[$keyC]["monto"];
                    }

                    $value_cuenta = $this->db->table("centro")
                        ->where("id",$valueC["idCentro"])->get()->getRowArray();
                    
                        $tabla .=  "<table>";
                        $tabla .= "<tr>";
                        $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
                        $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
                        $tabla .= "<td>" . $orden["ts_descripcion"] . "</td>";
                        $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
                        $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
                        $tabla .= "<td>" . $orden["eje_nombre"] . "</td>";
                        $tabla .= "<td>" . $orden["eje_ruc"] . "</td>";
                        $tabla .= "<td>" . $orden["banco_descripcion"]." ".$orden["be_nroCuenta"] . "</td>";
                        $tabla .= "<td>" . $detalles[$keyC]["descripcion"] . "</td>";
                        $tabla .= "<td>" . $orden["simbolo"] . "</td>";
                        $tabla .= "<td>" . $detalles[$keyC]["monto"] . "</td>";
                        $tabla .= "<td>" . $value_cuenta["codigo"]. "</td>";
                        $tabla .= "<td>" . $value_cuenta["codigo"] . "</td>";
                        $tabla .= "<td>" . $value_cuenta["descripcion"] . "</td>";
                        $tabla .= "<td> - </td>";
                        $tabla .= "<td> - </td>";
                        $tabla .= "<td> - </td>";
                        $tabla .= "</tr>";
                        $tabla .= "</table>"; 
                }
            }
            else{
                $orden = $this->db->table("orden o")
                    ->select("o.id,e.nombre empresa_nombre,o.fecha orden_fecha,ts.descripcion ts_descripcion, o.codigo orden_codigo,pso.nombres soli_nombres,pso.apellidoPaterno soliAp, pso.apellidoMaterno soliAm, pej.nombres jefe_nombres,pej.apellidoPaterno jefeAp, pej.apellidoMaterno jefeAm, to.descripcion to_descripcion,eje.nombre eje_nombre,eje.ruc eje_ruc, m.simbolo,be.nroCuenta be_nroCuenta,b.descripcion banco_descripcion, o.objeto,o.idCuenta idCuenta3, o.importe,c1.descripcion cuenta1_descripcion, c3.descripcion cuenta3_descripcion, c3.codigo cuenta3_codigo,cc.descripcion centro_descripcion, cc.codigo centro_codigo")
                    ->join("empresa e","e.id = o.idEmpresa")
                    ->join("personal pso","pso.id = o.idPersonalSoli")
                    ->join("personal pej","pej.id = o.idPersonalJefe")
                    ->join("tipoOrden to","to.id = o.idTipoOrden")
                    ->join("tipoSolicitud ts","ts.id = o.idTipoSolicitud")
                    ->join("banco_empresa be","be.id = o.idBanco_empresa")
                    ->join("banco b","b.id = be.idBanco")
                    ->join("empresa eje","eje.id = o.idEmpresaEje")
                    ->join("moneda m","m.id = o.idMoneda")
                    ->join("centro cc","cc.id = o.idCentroCosto")
                    ->join("cuenta3 c3","c3.id = o.idCuenta")
                    ->join("cuenta2 c2","c2.id = c3.idCuenta")
                    ->join("cuenta1 c1","c1.id = c2.idCuenta")
                    ->where("o.fecha > ", $fechaInicio)
                    ->where("o.fecha < ", $fechaFinal)
                    ->where("o.estado !=", "5")
                    ->where("o.id =", $value["id"])
                    ->get()->getRowArray();
        
               /* foreach ($orden as $key => $value) {
                    $detalles = $this->db->table("orden_detalle")
                        ->where("idOrden", $value->id)
                        ->get()->getResult();
                    $orden[$key]->detalle = $detalles;
                }*/
           
        
                if($orden["simbolo"] == "PEN"){
                    $cantidadRegistroSoles++;
                    $totalImporteSoles += $orden["importe"];
                }else{
                    $cantidadRegistroDolares++;
                    $totalImporteDolares += $orden["importe"];
                }
                 
                $tabla .=  "<table>";
                $tabla .= "<tr>";
                $tabla .= "<td>" . $orden["empresa_nombre"] . "</td>";
                $tabla .= "<td>" . date("d/m/Y", strtotime($orden["orden_fecha"]) ) . "</td>";
                $tabla .= "<td>" . $orden["ts_descripcion"] . "</td>";
                $tabla .= "<td>" . $orden["orden_codigo"] . "</td>";
                $tabla .= "<td>" . $orden["soli_nombres"] . "</td>";
                $tabla .= "<td>" . $orden["eje_nombre"] . "</td>";
                $tabla .= "<td>" . $orden["eje_ruc"] . "</td>";
                $tabla .= "<td>" . $orden["banco_descripcion"]." ".$orden["be_nroCuenta"] . "</td>";
                $tabla .= "<td>" . $orden["objeto"] . "</td>";
                $tabla .= "<td>" . $orden["simbolo"] . "</td>";
                $tabla .= "<td>" . $orden["importe"] . "</td>";
                $tabla .= "<td>" . $orden["centro_codigo"]."-".$orden["cuenta3_codigo"] . "</td>";
                $tabla .= "<td>" . $orden["centro_codigo"] . "</td>";
                $tabla .= "<td>" . $orden["centro_descripcion"] . "</td>";
                $tabla .= "<td>" . $orden["cuenta3_codigo"] . "</td>";
                $tabla .= "<td>" . $orden["cuenta3_descripcion"] . "</td>";
                $tabla .= "<td>" . $orden["cuenta1_descripcion"] . "</td>";

    
                //  $detalle = $value->detalle;
    
                /*foreach ($detalle as $key2 => $value2) {
                    $tabla .= "<td>" . $value2->descripcion . "</td>";
                    $tabla .= "<td>" . $value2->monto . "</td>";
                }*/
    
                $tabla .= "</tr>";
                $tabla .= "</table>"; 
             
                
            }
        }

        $tabla .= '<table>';
        $tabla .= '<tr>';
        $tabla .= '<td colspan="10" style="text-align:center">Numero de registros Soles</td>';
        $tabla .= '<td>'.$cantidadRegistroSoles.'</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td colspan="10" style="text-align:center">Total Soles</td>';
        $tabla .= '<td>'.$totalImporteSoles.'</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td colspan="10" style="text-align:center">Numero de registros Soles</td>';
        $tabla .= '<td>'.$cantidadRegistroDolares.'</td>';
        $tabla .= '</tr>';

        $tabla .= '<tr>';
        $tabla .= '<td colspan="10" style="text-align:center">Numero de registros Dolares</td>';
        $tabla .= '<td>'.$totalImporteDolares.'</td>';
        $tabla .= '</tr>';
        $tabla .= '</table>';
        echo $tabla;
        
    }

    public function rendicion()
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
            'ordenes' => (new OcModel())->where("estado!=", "3")->findAll(),
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
                $this->template->render('Admin/oc/rendicion', $data);
            } else {

         

                $cantOrden = $this->db->table("rendicion")
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

                $codigo = "Rend.".$codTipoOrden . $cantOrden . "-2021";

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
                    "idOrden" => 1,
                    "idEmpresaEje" => $this->request->getVar('ejecutado'),
                    "idBanco_empresa" => $this->request->getVar('idBanco'),
                    "idMoneda" => 1,
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


                        $name = uniqid().'.'.$file->guessExtension();
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
                       // $save = $builder->insert($data);

                        $msg = 'Files has been uploaded';
                    }
                }




            }
        } else {
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/oc/rendicion', $data);
        }
    }

    public function eliminar($id)
    {
        $datosUpdate = [
            "id" => $id,
            "estado" => '5'
        ];
        $this->model->save($datosUpdate);
        return redirect()->to(site_url('admin/oc'));
    }
}
