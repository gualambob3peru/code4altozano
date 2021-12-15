<?php

namespace App\Libraries;

class Template
{
    private $viewTemplate;

    public function __construct()
    {
    }

    public function setTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;
    }

    public function getTemplate()
    {
        return $this->viewTemplate;
    }

    public function render($body,$data=[]){
        
        
        $pagina = view($body, $data);

        $data = [
            "body" => $pagina
        ]; 
  

        echo view($this->viewTemplate,$data);
    }
}
