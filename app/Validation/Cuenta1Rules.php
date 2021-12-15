<?php
namespace App\Validation;
use App\Models\Cuenta1Model;

class Cuenta1Rules
{
    public function validateCodigoCuenta1($codigo){
     
        if((new Cuenta1Model())->where('codigo',$codigo)->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}