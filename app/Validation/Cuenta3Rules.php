<?php
namespace App\Validation;
use App\Models\Cuenta3Model;

class Cuenta3Rules
{
    public function validateCodigoCuenta3($codigo){
        if((new Cuenta3Model())->where('codigo',$codigo)->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}