<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
        $twig = twig_instance();
        $twig->display ('home/logged_out_content.html', []);
	}

	//--------------------------------------------------------------------

}
