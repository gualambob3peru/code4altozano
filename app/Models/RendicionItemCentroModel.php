<?php

namespace App\Models;

use CodeIgniter\Model;

class RendicionItemCentroModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'rendicion_itemcentro';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['idRendicionItem','detalle','idCentro','idCuenta','monto'];

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
            "rendicion_itemcentro.id, rendicion_itemcentro.idRendicionItem,rendicion_itemcentro.detalle,rendicion_itemcentro.idCentro,rendicion_itemcentro.idCuenta,rendicion_itemcentro.monto,c.idKey
            ")
            ->where("rendicion_itemcentro.idRendicionItem",$id)
            ->join("centro c","c.id = rendicion_itemcentro.idCentro","left")
            ->get()->getResultArray();
    }
}
