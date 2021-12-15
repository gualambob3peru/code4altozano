<?php

namespace Config;

use App\Validation\PersonalRules;
use App\Validation\EmpresaRules;
use App\Validation\ClasecostoRules;
use App\Validation\Cuenta1Rules;
use App\Validation\Cuenta2Rules;
use App\Validation\Cuenta3Rules;
use App\Validation\CentroRules;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        PersonalRules::class,
        EmpresaRules::class,
        ClasecostoRules::class,
        Cuenta1Rules::class,
        Cuenta2Rules::class,
        Cuenta3Rules::class,
        CentroRules::class,
        
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------
}
