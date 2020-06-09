<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
        $twig = twig_instance();
        $page = (session('loggedIn'))? 'home/workspace.html' : 'home/logged_out_content.html';
        $twig->display ($page, ['session' => session()]);
	}

	//--------------------------------------------------------------------

}
