<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\BancoModel;

class Banco extends BaseController
{
    private $db = "";
    private $table = "banco";
    private $nombre = "banco";
    private $lista = "bancos";
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
        $this->model = new BancoModel();
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
                'descripcion' => 'required'
            ];

            $errors = [
                'ruc' => [
                    'validateRuc' => 'Este RUC ya está registrado'
                ]
            ];

            //if (!$validation->withRequest($this->request)->run()) {
            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView['validation'] = $this->validator;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
            } else {
                

                $datosInsert = [
                    "descripcion" => $this->request->getVar('descripcion')
                ];

                $this->model->save($datosInsert);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/agregar',$this->dataView);
        }
    }

    public function ajaxGet_cuentas(){
        $idEmpresa = $this->request->getPost('idEmpresa');

        $bancos = $this->db->table("banco_empresa be")
            ->select("be.id,be.nroCuenta,b.descripcion,m.simbolo")
            ->join("banco b","b.id = be.idBanco")
            ->join("moneda m","m.id = be.idMoneda")
            ->where("be.idEmpresa",$idEmpresa)->get()->getResult();

        echo json_encode(array("response"=>$bancos));
    }

    public function editar($id){
        $f_model = $this->model->find($id);

        if ($this->request->getPost('submit')) {
            $rules = [
                'descripcion' => 'required'
            ];

            $errors = [
                'ruc' => [
                    'validateRuc' => 'Este RUC ya está registrado'
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

    public function eliminar($id){
        $datosUpdate= [
            "id" => $id,
            "estado" => '0'
        ];
        $this->model->save($datosUpdate);
        return redirect()->to(site_url('admin/'.$this->table)); 
    }
}