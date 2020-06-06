<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Twig extends \Daycry\Twig\Config\Twig
{
    public $functions_safe = [ 'form_hidden', 'json_decode' ];
    
    public $functions_asis = [ 'current_url' ];

    public $paths = [];
}
