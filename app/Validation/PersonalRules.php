<?php
namespace App\Validation;
use App\Models\PersonalModel;

class PersonalRules
{
  public function validateUserPe(string $str, string $fields, array $data){
    $model = new PersonalModel();
    $user = $model->where('email', $data['email'])
                  ->first();

    if(!$user)
      return false;

    return password_verify($data['password'], $user['password']);
  }

  public function valideEmailPersonal($email){
    if((new PersonalModel())->where('email',$email)->first() == NULL){
        return true;
    }else{
        return false;
    }
}
}