<?php
namespace App\Validation;
use App\Models\ClasecostoModel;

class ClasecostoRules
{
    public function validateCodigo($codigo){
        if((new ClasecostoModel())->where('codigo',$codigo)->where('estado','1')->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}