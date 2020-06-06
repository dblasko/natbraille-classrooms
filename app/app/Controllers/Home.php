<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		//return view('welcome_message');
        $twig = twig_instance();
        $twig->display ('layout.html', ['user_first_name' => 'test']);
	}

	//--------------------------------------------------------------------

}
