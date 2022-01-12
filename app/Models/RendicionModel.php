<?php

namespace App\Models;

use CodeIgniter\Model;

class RendicionModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'rendicion';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['codigo','idPersonal','importe','idTipoSolicitudRen','idTipoOrden','idEmpresa','idPersonalSoli','idPersonalJefe','idOrden','idEmpresaEje','idBanco_empresa','idMoneda','referencia','estado'];

    // Dates
    protected $useTimestamps        = false;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getList(){
        return $this->select(
            "rendicion.id,rendicion.codigo,rendicion.idPersonal, rendicion.importe, rendicion.referencia,rendicion.estado,rendicion.created_at,
            p.nombres p_nombres,p.apellidoPaterno p_apellidoPaterno,p.apellidoMaterno p_apellidoMaterno,
            to.codigo tipoOrden_codigo, to.descripcion tipoOrden_descripcion,
            tsr.descripcion tsr_descripcion,
            e.nombre empresa_nombre,e.ruc empresa_ruc,e.direccion empresa_direccion,
            eje.nombre eje_nombre,eje.ruc eje_ruc,eje.direccion eje_direccion,
            pes.nombres pes_nombres,pes.apellidoPaterno pes_apellidoPaterno,pes.apellidoMaterno pes_apellidoMaterno,
            pej.nombres pej_nombres,pej.apellidoPaterno pej_apellidoPaterno,pej.apellidoMaterno pej_apellidoMaterno,
            o.codigo o_codigo,
            m.descripcion m_descripcion,
            ")
            ->join("personal p","p.id = rendicion.idPersonal")
            ->join("tipoOrden to","to.id = rendicion.idTipoOrden")
            ->join("tipoSolicitudRen tsr","rendicion.idTipoSolicitudRen = tsr.id")
            ->join("empresa e","e.id = rendicion.idEmpresa")
            ->join("personal pes","pes.id = rendicion.idPersonalSoli")
            ->join("personal pej","pej.id = rendicion.idPersonalJefe")
            ->join("empresa eje","eje.id = rendicion.idEmpresaEje")
            ->join("moneda m", 'm.id = rendicion.idMoneda')
            ->join("orden o","o.id = rendicion.idOrden","left")
        
            ->where("rendicion.estado !=","5")
            ->get()->getResult();
    }

    public function get_id($idRendicion){
        return $this->select(
            "rendicion.id,rendicion.codigo,rendicion.idPersonal, rendicion.importe, rendicion.referencia,rendicion.estado,rendicion.created_at,
            p.nombres p_nombres,p.apellidoPaterno p_apellidoPaterno,p.apellidoMaterno p_apellidoMaterno,
            to.codigo tipoOrden_codigo, to.descripcion tipoOrden_descripcion,
            tsr.descripcion tsr_descripcion,
            e.nombre empresa_nombre,e.ruc empresa_ruc,e.direccion empresa_direccion,
            eje.nombre eje_nombre,eje.ruc eje_ruc,eje.direccion eje_direccion,
            pes.nombres pes_nombres,pes.apellidoPaterno pes_apellidoPaterno,pes.apellidoMaterno pes_apellidoMaterno,
            pej.nombres pej_nombres,pej.apellidoPaterno pej_apellidoPaterno,pej.apellidoMaterno pej_apellidoMaterno,
            o.codigo o_codigo,
            m.descripcion m_descripcion,
            b.descripcion b_descripcion,
            be.nroCuenta be_nroCuenta,
            ")
            ->join("personal p","p.id = rendicion.idPersonal")
            ->join("tipoOrden to","to.id = rendicion.idTipoOrden")
            ->join("tipoSolicitudRen tsr","rendicion.idTipoSolicitudRen = tsr.id")
            ->join("empresa e","e.id = rendicion.idEmpresa")
            ->join("personal pes","pes.id = rendicion.idPersonalSoli")
            ->join("personal pej","pej.id = rendicion.idPersonalJefe")
            ->join("empresa eje","eje.id = rendicion.idEmpresaEje")
            ->join("moneda m", 'm.id = rendicion.idMoneda')
            ->join("banco_empresa be", 'be.id = rendicion.idBanco_empresa')
            ->join("banco b", 'b.id = be.idBanco')
            ->join("orden o","o.id = rendicion.idOrden")
        
            ->where("rendicion.estado !=","5")
            ->where("rendicion.id ",$idRendicion)
            ->get()->getRowArray();
    }

}
