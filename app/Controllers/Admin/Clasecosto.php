<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CentroModel;
use Config\Database;
use App\Models\ClasecostoModel;
use App\Models\Cuenta1Model;
use App\Models\Cuenta2Model;
use App\Models\Cuenta3Model;

class Clasecosto extends BaseController
{
    private $db = "";
    private $table = "clasecosto";
    private $nombre = "clasecosto";
    private $lista = "clasecosto";
    private $dataView = [];
    private $model = "";

    public function __construct()
    {
        helper(['form', 'url']);
        $this->db =  Database::connect();
        $this->dataView = [
            "lista" => $this->lista,
            "table" => $this->table,
            "nombre" => $this->nombre,
        ];
        $this->model = new ClasecostoModel();
    }

    public function index()
    {
        $lista_datos = $this->db->table($this->table)
            ->where("estado","1")
            ->get()->getResult();
        $data = $this->dataView;
        $data["lista_datos"] = $lista_datos;


        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/list', $data);
    }

    public function agregar(){
        if ($this->request->getPost('submit')) {
            $rules = [
                'codigo' => 'required|validateCodigo',
                'descripcion' => 'required',
            ];

            $errors = [
                'codigo' => [
                    'validateCodigo' => 'Este Código ya está registrado'
                ]
            ];

            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView['validation'] = $this->validator;


                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
            } else {
                $datosInsert= [
                    "descripcion" => $this->request->getVar('descripcion'),
                    "codigo" => $this->request->getVar('codigo'),

                ];

                $this->model->save($datosInsert);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $datosView = $this->dataView;
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
        }
    }

    public function ajaxAgregar(){
        if ($_POST) {
            $rules = [
                'codigo' => 'required|validateCodigo',
                'descripcion' => 'required',
            ];

            $errors = [
                'codigo' => [
                    'validateCodigo' => 'Este Código ya está registrado'
                ]
            ];
            
            if (!$this->validate($rules, $errors)) {
                $validation = \Config\Services::validation();
                $data_error = array();
                $data_error["error_codigo"] = $validation->getError('codigo');
                $data_error["error_descripcion"] = $validation->getError('descripcion');
                
                echo json_encode(array(
                    "response"=>"0",
                    "data_error" => $data_error
                ));
               
            } else {
                $datosInsert= [
                    "descripcion" => $this->request->getVar('descripcion'),
                    "codigo" => $this->request->getVar('codigo'),

                ];

                $this->model->save($datosInsert);
                echo json_encode(array("response"=>"1"));
            }
        } else{
            echo json_encode(array("response"=>"3"));
        }
    }

    public function ajaxEditar(){
        $f_model = $this->model->find($this->request->getVar('id'));

        if ($_POST) {
            $rules = [
                'descripcion' => 'required',
            ];

            if($f_model["codigo"] == $this->request->getVar('codigo'))
                $rules["codigo"] = 'required';
            else   
                $rules["codigo"] = 'required|validateCodigo';

            $errors = [
                'codigo' => [
                    'validateCodigo' => 'Este Código ya está registrado'
                ]
            ];
            
            if (!$this->validate($rules, $errors)) {
                $validation = \Config\Services::validation();
                $data_error = array();
                $data_error["error_codigo"] = $validation->getError('codigo');
                $data_error["error_descripcion"] = $validation->getError('descripcion');
                
                echo json_encode(array(
                    "response"=>"0",
                    "data_error" => $data_error
                ));
               
            } else {
                $datosInsert= [
                    "id" => $this->request->getVar('id'),
                    "descripcion" => $this->request->getVar('descripcion'),
                    "codigo" => $this->request->getVar('codigo'),

                ];

                $this->model->save($datosInsert);
                echo json_encode(array("response"=>"1"));
            }
        } else{
            echo json_encode(array("response"=>"3"));
        }
    }

    public function ajaxEliminar(){
        if ($_POST) {
            $rules = [
                'id' => 'required',
            ];

            $errors = [
                'id' => [
                    'required' => 'Es necesario el id'
                ]
            ];
            
            if (!$this->validate($rules, $errors)) {
                $validation = \Config\Services::validation();
                $data_error = array();
                $data_error["error_id"] = $validation->getError('id');
                
                echo json_encode(array(
                    "response"=>"0",
                    "data_error" => $data_error
                ));
               
            } else {
                $datosInsert= [
                    "id" => $this->request->getVar('id'),
                    "estado" => "0"
                ];

                $this->model->save($datosInsert);
                echo json_encode(array("response"=>"1"));
            }
        } else{
            echo json_encode(array("response"=>"3"));
        }
    }

    public function ajaxGet(){
        if ($_POST) {
            $clasecosto = (new ClasecostoModel())->where("estado","1")->findAll();

            echo json_encode(array("response"=>"1","clasecosto" => $clasecosto));
        }
    }
    
    public function ajaxGetCentro(){
        if ($_POST) {
            $clasecosto = (new ClasecostoModel())->where("estado","1")->findAll();

            $clasecosto_centro = $this->db->table("centro_clase c")
            ->select("c.id,c.idCentro,c.idClaseCosto,c.estado,cc.descripcion clasecosto_descripcion")
            ->join("clasecosto cc","cc.id = c.idClaseCosto")
            ->where("c.estado","1")
            ->where("c.idCentro", $this->request->getVar('idCentro'))
            ->get()->getResult();

            echo json_encode(array("response"=>"1","clasecosto" => $clasecosto,"clasecosto_centro"=>$clasecosto_centro));
        }
    }
    
    public function updateCentro(){
        if ($_POST) {
            $clases = explode(",", $_POST["clases"]);

            $this->db->table("centro_clase")
                    ->set('estado', '0')
                    ->where('idCentro',$_POST["idCentro"])
                    ->update();

            foreach($clases as $key => $value){
                $data = [
                    'idCentro' => $_POST["idCentro"],
                    'idClaseCosto'  => $value
                ];

                $fila = $this->db->table("centro_clase")->where('idCentro',$_POST["idCentro"])->where('idClaseCosto',$value)->get()->getRow();

                if($fila){
                    $this->db->table("centro_clase")
                    ->set('estado', '1')
                    ->where('idCentro',$_POST["idCentro"])->where('idClaseCosto',$value)
                    ->update();
                
                }else{
                    $this->db->table("centro_clase")->insert($data);
                }

                
            }

            echo json_encode(array("response"=>$_POST));
        }
    }
    

    public function editar($id){
    
        $f_model = $this->model->find($id);

        if ($this->request->getPost('submit')) {
            $rules = [
                'descripcion' => 'required',
            ];
            
            $errors = [
                'codigo' => [
                    'validateCodigo' => 'Este código ya está registrado'
                ]
            ];

            if($f_model["codigo"] == $this->request->getVar('codigo'))
                $rules["codigo"] = 'required';
            else   
                $rules["codigo"] = 'required|validateCodigo';

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView["f_model"] = $f_model;
                $datosView['validation'] = $this->validator;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/editar',$datosView);
            } else {
                $datosUpdate= [
                    "id" => $id,
                    "codigo" => $this->request->getVar('codigo'),
                    "descripcion" => $this->request->getVar('descripcion'),

                ];

                $this->model->save($datosUpdate);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {    
            $datosView = $this->dataView;
            $datosView["f_model"] = $f_model;
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/editar',$datosView);
        }
    }

    public function cuentas(){

        $clasecosto_all = (new ClasecostoModel())->where("estado","1")->findAll(); 
        $cuenta1_all = (new Cuenta1Model())->where("estado","1")->findAll(); 
        $cuenta2_all = (new Cuenta2Model())->where("estado","1")->findAll(); 
        $cuenta3_all = (new Cuenta3Model())->where("estado","1")->findAll(); 

        $datosView = $this->dataView;
        $datosView["clasecostos"] = $clasecosto_all;
        $datosView["cuenta1_all"] = $cuenta1_all;
        $datosView["cuenta2_all"] = $cuenta2_all;
        $datosView["cuenta3_all"] = $cuenta3_all;

        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/cuentas',$datosView);
    }
    public function ajaxGetClase(){
        echo json_encode(array("response"=>(new ClasecostoModel())->where("estado","1")->findAll()));
    }

    public function centro($idKey){
        
        $centros = $this->db->table($this->table)
            ->join("centro",$this->table.".id = centro.idKey")
            ->where("centro.estado","1")
            ->get()->getResult();

        $datosView = $this->dataView;
        $datosView["centros"] = $centros;
        $datosView["idKey"] = $idKey;

        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/centro',$datosView);
    }

    public function centroAgregar(){
      
        if ($this->request->getPost('submit')) {
            $rules = [
                'descripcion' => 'required',
                'idKey' => 'required',
            ];

            $errors = [
               
            ];
            $idKey = $this->request->getVar('idKey');

            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView['validation'] = $this->validator;


                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/centro/'.$idKey,$datosView);
            } else {
                $datosInsert= [
                    "descripcion" => $this->request->getVar('descripcion'),
                    "idKey" => $idKey
                ];
                $centroModel = new CentroModel();
                $centroModel->save($datosInsert);
                return redirect()->to(site_url('admin/'.$this->table.'/centro/'.$idKey));    
            }
        } else {
            $datosView = $this->dataView;

            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table,$datosView);
        }
    }

    public function centroEliminar($id,$idKey){
        $datosUpdate= [
            "id" => $id,
            "estado" => '0'
        ];
        $centroModel = new CentroModel();
        $centroModel->save($datosUpdate);
        return redirect()->to(site_url('admin/'.$this->table.'/centro/'.$idKey)); 
    }

    public function eliminar($id){
        $datosUpdate= [
            "id" => $id,
            "estado" => '0'
        ];
        $this->model->save($datosUpdate);
        return redirect()->to(site_url('admin/'.$this->table)); 
    }
}