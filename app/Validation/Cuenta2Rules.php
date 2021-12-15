<?php
namespace App\Validation;
use App\Models\Cuenta2Model;

class Cuenta2Rules
{
    public function validateCodigoCuenta2($codigo){
        if((new Cuenta2Model())->where('codigo',$codigo)->first() == NULL){
            return true;
        }else{
            return false;
        }
    }
}