<?php

namespace App\Models;

use CodeIgniter\Model;

class OcModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'orden';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['codigo','idPersonal','importe','idTipoSolicitud','fecha','nombre','idTipoOrden','idEmpresa','idCentroCosto','idPersonalSoli','idPersonalJefe','idCuenta','objeto','idEmpresaEje','idBanco_empresa','idMoneda','referencia','estado'];

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

    public function findComplete($id){
        return $this->select(
            "o.id,o.texto,o.importe,o.objeto,o.referencia,o.fecha, o.created_at,o.estado,
            tp.descripcion tipoSolicitud_descripcion,
            to.codigo tipoOrden_codigo, to.descripcion tipoOrden_descripcion,
            e.nombre empresa_nombre,e.ruc empresa_ruc,e.direccion empresa_direccion,
            cc.codigo centro_codigo,cc.descripcion centro_descripcion,cc.monto centro_monto,
            pes.nombres pes_nombres,pes.apellidoPaterno pes_apellidoPaterno,pes.apellidoMaterno pes_apellidoMaterno
            ")
            ->join("tipoSolicitud tp","o.idTipoSolicitud = tp.id")
            ->join("tipoOrden to","to.id = o.idTipoOrden")
            ->join("empresa e","e.id = o.idEmpresa")
            ->join("centrocosto cc","cc.id = o.idCentroCosto")
            ->join("personal pes","pes.id = o.idPersonalSoli")
            ->join("personal pej","pej.id = o.idPersonalJefe")
        
            ->where("o.estado","1")
            ->where("o.id",$id)
            ->get()->getResult();
    }
}
