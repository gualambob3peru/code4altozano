<?php
namespace App\Validation;
use App\Models\CentroModel;

class CentroRules
{
    public function validateCodigo($codigo){
        if((new CentroModel())->where('codigo',$codigo)->where('estado','1')->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
    public function validateCodigoCentro($codigo){
        if((new CentroModel())->where('codigo',$codigo)->where('estado','1')->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}