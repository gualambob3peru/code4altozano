<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CentroModel;
use Config\Database;
use App\Models\KeyModel;

class Key extends BaseController
{
    private $db = "";
    private $table = "key";
    private $nombre = "Key";
    private $lista = "key";
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
        $this->model = new KeyModel();
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
                'descripcion' => 'required',
            ];

            $errors = [
                'nroDocumento' => [
                    'validateRuc' => 'Este Nro de documento ya está registrado'
                ]
            ];

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView['validation'] = $this->validator;


                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
            } else {
                $datosInsert= [
                    "descripcion" => $this->request->getVar('descripcion'),

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

    public function ajaxGet(){
        if ($_POST) {
            $idKey = $this->request->getVar('idKey');
            $keys = (new KeyModel())->findAll();

            echo json_encode(array("response"=>"1","keys" => $keys));
        }
    }

    public function editar($id){
    
        $f_model = $this->model->find($id);

        if ($this->request->getPost('submit')) {
            $rules = [
                'descripcion' => 'required'
            ];
            
            $errors = [
                'nroDocumento' => [
                    'validateRuc' => 'Este Nro de documento ya está registrado'
                ]
            ];

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
                    "descripcion" => $this->request->getVar('descripcion')

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
            //->select("key.id,key.descripcion,centro.codigo,centro.descripcion centro_descripcion")
            ->join("centro",$this->table.".id = centro.idKey")
            ->where("centro.idKey","1")
            ->where("centro.estado",$idKey)
            ->get()->getResult();

        $f_model = $this->model->find($idKey);

        $datosView = $this->dataView;
        $datosView["centros"] = $centros;
        $datosView["idKey"] = $idKey;
        $datosView["f_model"] = $f_model;

        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/centro',$datosView);
    }

    public function centroAgregar(){
      
        if ($this->request->getPost('submit')) {
            $rules = [
                'codigo' => 'required|validateCodigo',
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
                    "codigo" => $this->request->getVar('codigo'),
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