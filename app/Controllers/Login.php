<?php

namespace App\Controllers;

use App\Libraries\Template;
use App\Models\PersonalModel;


class Login extends BaseController
{
    public $template;
    public function __construct()
    {
        $this->template = new Template();
    }

    public function index()
    {
        helper(['form', 'url']);
        $encrypter = \Config\Services::encrypter();
       
        if ($this->request->getPost('submit')) {
     
            $rules = [
				'email' => 'required|min_length[6]|max_length[50]|valid_email',
				'password' => 'required|min_length[8]|max_length[255]|validateUserPe[email,password]',
			];

			$errors = [
				'password' => [
					'validateUser' => 'El email o contraseña no coincide'
				]
			];

            //if (!$validation->withRequest($this->request)->run()) {
            if (! $this->validate($rules, $errors)) {
                $this->template->setTemplate('templates/template1');
                $this->template->render('Login/login', ['validation' => $this->validator]);
            } else {
                $personalModel = new PersonalModel();
                $personal = $personalModel->where('email', $this->request->getVar('email'))->first();
              
                $session = session();
                $session->set("personal",$personal);
                
                return redirect()->to(site_url('admin'));    
            }
        } else {
            $this->template->setTemplate('templates/template1');
            $this->template->render('Login/login');
        }
    }

    public function otromas(){
        echo "esto es otro mas";
    }
    public function otro()
    {
        $datos = [
            "nombre" => "Franz",
            "apellido" => "Gualambo"
        ];


        $this->template->setTemplate("templates/template1");
        $this->template->render("Login/otro", $datos);
    }
}
