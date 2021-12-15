<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function __construct()
    {
       
    }
    public function index()
    {
        echo 'hola';
        
    }

    public function logout(){
        $session = session();
        $session->destroy();

        return redirect()->to('/');
    }
}
