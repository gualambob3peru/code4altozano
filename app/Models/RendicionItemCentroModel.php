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
            "rendicion_itemcentro.id, rendicion_itemcentro.idRendicionItem,rendicion_itemcentro.detalle,rendicion_itemcentro.idCentro,rendicion_itemcentro.idCuenta,rendicion_itemcentro.monto,
            c.idKey,c.codigo c_codigo,c.descripcion c_descripcion,
            c3.codigo c3_codigo,c3.descripcion c3_descripcion,
            k.descripcion k_descripcion,
            cc.id cc_id,cc.codigo cc_codigo,cc.descripcion cc_descripcion
            ")
            ->where("rendicion_itemcentro.idRendicionItem",$id)
            ->join("centro c","c.id = rendicion_itemcentro.idCentro")
            ->join("cuenta3 c3","c3.id = rendicion_itemcentro.idCuenta")
             ->join("cuenta2 c2","c2.id = c3.idCuenta","left")
            ->join("cuenta1 c1","c1.id = c2.idCuenta","left")
            ->join("clasecosto cc","cc.id = c1.idCuenta","left")
            ->join("key k","k.id = c.idKey")
            ->get()->getResultArray();
    }
}
