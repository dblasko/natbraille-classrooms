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
        } else if (session('wantsToJoin') != null) {
            $title = 'Vous avez été invité dans une promotion.';
            $msg = 'Pour la rejoindre, veuillez soit vous inscrire puis vous connecter, soit directement vous connecter et vous serez automatiquement ajouté à la promotion.';
        }
        $twig->display ($page, [
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
        ]);
	}

	//--------------------------------------------------------------------

}
