<?php namespace App\Controllers;

use App\Controllers\Users;

class Home extends BaseController
{
	public function index()
	{
        $twig = twig_instance();
        $page = (session('loggedIn'))? 'home/workspace.html' : 'home/logged_out_content.html';
        if (session('loggedIn')) {
            $data = Users::prepareLoggedInUserData(session('user'));
            echo print_r($data['notifications']);
        }
        $twig->display ($page, [
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
        ]);
	}

	//--------------------------------------------------------------------

}
