<?php

namespace App\Models;

use CodeIgniter\Model;

class RendicionItemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'rendicion_item';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['idRendicion','nroDoc','idEmpresaProv','detalle','idCentro','idCuenta','monto'];

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

    public function get_all($id){
        return $this->select(
            "rendicion_item.id, rendicion_item.idRendicion,rendicion_item.nroDoc,rendicion_item.idEmpresaProv,rendicion_item.detalle,rendicion_item.idCentro,rendicion_item.idCuenta,rendicion_item.monto,rendicion_item.created_at,c.idKey
            ")
            ->where("rendicion_item.idRendicion",$id)
            ->join("centro c","c.id = rendicion_item.idCentro","left")
            ->get()->getResultArray();
    }
}
