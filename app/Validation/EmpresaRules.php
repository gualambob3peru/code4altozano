<?php
namespace App\Validation;
use App\Models\EmpresaModel;

class EmpresaRules
{
    public function validateRuc($ruc){
        if((new EmpresaModel())->where('ruc',$ruc)->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}