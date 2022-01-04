<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CentroModel;
use Config\Database;
use App\Models\Cuenta1Model;

class Cuenta1 extends BaseController
{
    private $db = "";
    private $table = "cuenta1";
    private $nombre = "cuenta1";
    private $lista = "cuenta1";
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
        $this->model = new Cuenta1Model();
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
                'codigo' => 'required|validateCodigoCuenta1',
                'descripcion' => 'required',
            ];

            $errors = [
                'codigo' => [
                    'validateCodigoCuenta1' => 'Este código ya está registrado'
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
                'codigo' => 'required',
                'descripcion' => 'required',
            ];

            $errors = [
                'codigo' => [
                    'validateCodigoCuenta1' => 'Este Código ya está registrado'
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
                    "idCuenta" => $this->request->getVar('idCuenta')

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
                $rules["codigo"] = 'required';

            $errors = [
                'codigo' => [
                    'validateCodigoCuenta1' => 'Este Código ya está registrado'
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
                    "codigo" => $this->request->getVar('codigo')
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


    public function ajaxGetCuenta1(){
        $cuentas = (new Cuenta1Model())->where("idCuenta",$this->request->getVar('idCuenta'))->where("estado","1")->findAll();
        echo json_encode(array("response"=>$cuentas));
    }

    public function editar($id){
    
        $f_model = $this->model->find($id);

        if ($this->request->getPost('submit')) {

            $rules = [
                'descripcion' => 'required',
            ];
            
            $errors = [
                'codigo' => [
                    'validateCodigoCuenta1' => 'Este código ya está registrado'
                ]
            ];

            if($f_model["codigo"] == $this->request->getVar('codigo'))
                $rules["codigo"] = 'required';
            else   
                $rules["codigo"] = 'required|validateCodigoCuenta1';

      

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