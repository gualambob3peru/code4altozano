<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BancoModel;
use Config\Database;
use App\Models\EmpresaModel;
use App\Models\MonedaModel;

class Empresa extends BaseController
{
    private $db = "";
    private $table = "empresa";
    private $nombre = "Empresa";
    private $lista = "empresas";
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
        $this->model = new EmpresaModel();
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

        $tipoEmpresas = $this->db->table("tipoEmpresa")
            ->where("estado","1")
            ->get()->getResult();

        if ($this->request->getPost('submit')) {
            $rules = [
                'nombre' => 'required',
                'idTipoEmpresa' => 'required',
                'ruc' => 'required|validateRuc',
                'direccion' => 'required'
            ];

            $errors = [
                'ruc' => [
                    'validateRuc' => 'Este RUC ya está registrado'
                ]
            ];

            if (!$this->validate($rules, $errors)) {
                $datosView = $this->dataView;
                $datosView['validation'] = $this->validator;
                $datosView["tipoEmpresas"] = $tipoEmpresas;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
            } else {
                

                $datosInsert = [
                    "nombre" => $this->request->getVar('nombre'),
                    "idTipoEmpresa" => $this->request->getVar('idTipoEmpresa'),
                    "ruc" => $this->request->getVar('ruc'),
                    "direccion" => $this->request->getVar('direccion')
                ];

                $this->model->save($datosInsert);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["tipoEmpresas"] = $tipoEmpresas;
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/agregar',$datosView);
        }
    }

    public function editar($id){
        $f_model = $this->model->find($id);

        $tipoEmpresas = $this->db->table("tipoEmpresa")
            ->where("estado","1")
            ->get()->getResult();

        if ($this->request->getPost('submit')) {
            $rules = [
                'nombre' => 'required',
                'idTipoEmpresa' => 'required',
                'direccion' => 'required'
            ];

            if($f_model["ruc"] == $this->request->getVar('ruc'))
                $rules["ruc"] = 'required';
            else   
                $rules["ruc"] = 'required|validateRuc';
            
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
                $datosView["tipoEmpresas"] = $tipoEmpresas;

                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/editar',$datosView);
            } else {
                $datosUpdate= [
                    "id" => $id,
                    "nombre" => $this->request->getVar('nombre'),
                    "idTipoEmpresa" => $this->request->getVar('idTipoEmpresa'),
                    "ruc" => $this->request->getVar('ruc'),
                    "direccion" => $this->request->getVar('direccion')
                ];

                $this->model->save($datosUpdate);
                return redirect()->to(site_url('admin/'.$this->table));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["f_model"] = $f_model;
            $datosView["tipoEmpresas"] = $tipoEmpresas;

            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/editar',$datosView);
        }
    }

    public function listaBancos($idEmpresa){
        $datosView = $this->dataView;

        $datosView["empresa"] = (new EmpresaModel())->find($idEmpresa);
        //bancos registrados de empresas
        $bancos = $this->db->table("banco_empresa be")
            ->select("be.nroCuenta,be.id,b.descripcion banco_descripcion,m.descripcion descripcion_moneda,m.simbolo simbolo_moneda")
            ->join("banco b","b.id = be.idBanco")
            ->join("moneda m","m.id = be.idMoneda")
            ->where("be.estado","1")
            ->where("be.idEmpresa",$idEmpresa)
            ->get()->getResult();

        $datosView["bancos"] = $bancos;


        $this->template->setTemplate('templates/template2');
        $this->template->render('Admin/'.$this->table.'/listaBancos',$datosView);
    }

    public function agregarBanco($idEmpresa){
        
        if ($this->request->getPost('submit')) {
            $rules = [
                'idBanco' => 'required',
                'idEmpresa' => 'required',
                'idMoneda' => 'required',
                'nroCuenta' => 'required'
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

                $datosView["empresa"] = (new EmpresaModel())->find($idEmpresa);   
                $datosView["bancos"] = (new BancoModel())->where("estado","1")->findAll();   
                $datosView["monedas"] = (new MonedaModel())->where("estado","1")->findAll();   
                
                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/agregarBanco',$datosView);
            } else {
                $datosInsert= [
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idBanco" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "nroCuenta" => $this->request->getVar('nroCuenta')
                ];

                $this->db->table("banco_empresa")
                    ->insert($datosInsert);

                
                return redirect()->to(site_url('admin/'.$this->table.'/listaBancos/'.$idEmpresa));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["empresa"] = (new EmpresaModel())->find($idEmpresa);   
            $datosView["bancos"] = (new BancoModel())->where("estado","1")->findAll();   
            $datosView["monedas"] = (new MonedaModel())->where("estado","1")->findAll();   
            
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/agregarBanco',$datosView);
        }
    }

    public function editarBanco($idEmpresa,$idBancoEmpresa){
        
        if ($this->request->getPost('submit')) {
            $rules = [
                'idBanco' => 'required',
                'idMoneda' => 'required',
                'idEmpresa' => 'required',
                'nroCuenta' => 'required'
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

                $datosView["empresa"] = (new EmpresaModel())->find($idEmpresa);   
                $datosView["bancos"] = (new BancoModel())->where("estado","1")->findAll();   
                $datosView["banco"] = $this->db->table("banco_empresa")->where("id",$idBancoEmpresa)->get()->getRow();
                $datosView["monedas"] = (new MonedaModel())->where("estado","1")->findAll(); 
                
                $this->template->setTemplate('templates/template2');
                $this->template->render('Admin/'.$this->table.'/editarBanco',$datosView);
            } else {
                $datosInsert= [
                    "idEmpresa" => $this->request->getVar('idEmpresa'),
                    "idBanco" => $this->request->getVar('idBanco'),
                    "idMoneda" => $this->request->getVar('idMoneda'),
                    "nroCuenta" => $this->request->getVar('nroCuenta')
                ];

                $this->db->table("banco_empresa")
                    ->set("idBanco", $this->request->getVar('idBanco'))
                    ->set("idMoneda", $this->request->getVar('idMoneda'))
                    ->set("nroCuenta", $this->request->getVar('nroCuenta'))
                    ->where("id",$idBancoEmpresa)->update();

                
                return redirect()->to(site_url('admin/empresa/listaBancos/'.$idEmpresa));    
            }
        } else {
            $datosView = $this->dataView;
            $datosView["empresa"] = (new EmpresaModel())->find($idEmpresa);   
            $datosView["bancos"] = (new BancoModel())->where("estado","1")->findAll();   
            $datosView["banco"] = $this->db->table("banco_empresa")->where("id",$idBancoEmpresa)->get()->getRow();
            $datosView["monedas"] = (new MonedaModel())->where("estado","1")->findAll(); 
            
            $this->template->setTemplate('templates/template2');
            $this->template->render('Admin/'.$this->table.'/editarBanco',$datosView);
        }
    }

    public function ajaxAgregarEmpresaSin(){
        $nombre = $this->request->getPost('nombre');
        $nombre = trim($nombre);
        if(trim($nombre)!= ""){
            //no debe existir
            $empresa = (new EmpresaModel())->where('nombre',$nombre)->findAll(); 
            
            if(count($empresa)){
                
                echo json_encode(array("state"=>1,"msg" => "Ya existe","id"=>$empresa[0]["id"],"nombre" => $nombre));
            }else{
                $nombre2 = explode(" - ", $nombre);
                if(count($nombre2)==2){
                    $nombre2 = $nombre2[1];
                    $empresa = (new EmpresaModel())->where('nombre',$nombre2)->findAll();  
                    if(count($empresa)){
              
                        echo json_encode(array("state"=>2,"msg" => "Ya existe", "id"=>$empresa[0]["id"],"nombre" => $nombre2));
                    }else{
                        //Tenemos que agregar porque lo más probable es que no existe en la BD
                        $datosInsert = [
                            "nombre" => $nombre,
                            "idTipoEmpresa" => 2,
                            "ruc" => '',
                            "direccion" => ''
                        ];
        
                        $this->model->save($datosInsert);    
                        $miId = $this->model->getInsertID();
                        echo json_encode(array("state"=>3,"msg" => "Agregado","id"=>$miId,"nombre" => $nombre));
                    }
                }else{
                    //Tenemos que agregar porque lo más probable es que no existe en la BD
                    $datosInsert = [
                        "nombre" => $nombre,
                        "idTipoEmpresa" => 2,
                        "ruc" => '',
                        "direccion" => ''
                    ];
    
                    $this->model->save($datosInsert);    
                    $miId = $this->model->getInsertID();
                    echo json_encode(array("state"=>4,"msg" => "Agregado","id"=>$miId,"nombre" => $nombre));
                }
            }
      
        }else{
            echo json_encode(array("state"=>5,"msg" => "Debe ingresar un nombre"));
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

    public function eliminarBanco($id){
        $banco_empresa = $this->db->table("banco_empresa")
            ->where("id",$id)->get()->getRow();

        $idEmpresa = $banco_empresa->idEmpresa;

        $this->db->table("banco_empresa")
        ->set("estado","0")
        ->where("id",$id)->update();

        return redirect()->to(site_url('admin/empresa/listaBancos/'.$idEmpresa)); 
    }
}