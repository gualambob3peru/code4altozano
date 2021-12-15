<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\PersonalModel;

class Personal extends BaseController
{
    private $db = "";
    private $table = "personal";
    private $nombre = "Personal";
    private $lista = "personal";
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
        $this->model = new PersonalModel();
    }

    public function index()
    {

       
        $lista_datos = $this->db->table($this->table)
            ->select("personal.id,personal.nombres,personal.apellidoPaterno,personal.apellidoMaterno,personal.nroDocumento,tp.descripcion tipo_desc,c.descripcion cargo_desc")
            ->join("cargo c","c.id = personal.idCargo")
            ->join("tipo_documento tp","tp.id = personal.idTipoDocumento")
            ->where("personal.estado","1")
            ->get()->getResult();
        $data = $this->dataView;
        $data["lista_datos"] = $lista_datos;


        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/list', $data);
    }

    public function agregar(){
        $tipoDocumentos = $this->db->table("tipo_documento")
            ->where("estado","1")
            ->get()->getResult();

        $cargos = $this->db->table("cargo")
            ->where("estado","1")
            ->get()->getResult();

        if ($this->request->getPost('submit')) {
            $rules = [
                'nombres' => 'required',
                'apellidoPaterno' => 'required',
                'apellidoMaterno' => 'required',
                'idTipoDocumento' => 'required',
                'nroDocumento' => 'required',
                'idCargo' => 'required',
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
                $datosView["tipoDocumentos"] = $tipoDocumentos;
                $datosView["cargos"] = $cargos;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
            } else {
                $datosInsert= [
                    "nombres" => $this->request->getVar('nombres'),
                    "apellidoPaterno" => $this->request->getVar('apellidoPaterno'),
                    "apellidoMaterno" => $this->request->getVar('apellidoMaterno'),
                    "idTipoDocumento" => $this->request->getVar('idTipoDocumento'),
                    "nroDocumento" => $this->request->getVar('nroDocumento'),
                    "idCargo" => $this->request->getVar('idCargo')
                ];

                $this->model->save($datosInsert);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["tipoDocumentos"] = $tipoDocumentos;
            $datosView["cargos"] = $cargos;

            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
        }
    }

    public function editar($id){
    
        $f_model = $this->model->find($id);
        $tipoDocumentos = $this->db->table("tipo_documento")
            ->where("estado","1")
            ->get()->getResult();

        $cargos = $this->db->table("cargo")
            ->where("estado","1")
            ->get()->getResult();

          
        if ($this->request->getPost('submit')) {
            $rules = [
                'nombres' => 'required',
                'apellidoPaterno' => 'required',
                'apellidoMaterno' => 'required',
                'idTipoDocumento' => 'required',
                'idCargo' => 'required',
            ];

            if($f_model["nroDocumento"] == $this->request->getVar('nroDocumento'))
                $rules["nroDocumento"] = 'required';
            else   
                $rules["nroDocumento"] = 'required';
            
            $errors = [
                'nroDocumento' => [
                    'validateRuc' => 'Este Nro de documento ya está registrado'
                ]
            ];

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView["f_model"] = $f_model;
                $datosView["tipoDocumentos"] = $tipoDocumentos;
                $datosView["cargos"] = $cargos;
                $datosView['validation'] = $this->validator;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/editar',$datosView);
            } else {
                $datosUpdate= [
                    "id" => $id,
                    "nombres" => $this->request->getVar('nombres'),
                    "apellidoPaterno" => $this->request->getVar('apellidoPaterno'),
                    "apellidoMaterno" => $this->request->getVar('apellidoMaterno'),
                    "idTipoDocumento" => $this->request->getVar('idTipoDocumento'),
                    "nroDocumento" => $this->request->getVar('nroDocumento'),
                    "idCargo" => $this->request->getVar('idCargo')
                ];

                $this->model->save($datosUpdate);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["tipoDocumentos"] = $tipoDocumentos;
            $datosView["cargos"] = $cargos;
            $datosView["f_model"] = $f_model;
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/editar',$datosView);
        }
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