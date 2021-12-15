<?php
namespace App\Validation;
use App\Models\ClasecostoModel;

class ClasecostoRules
{
    public function validateCodigo($codigo){
        if((new ClasecostoModel())->where('codigo',$codigo)->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}