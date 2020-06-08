<?php namespace App\Controllers;

use App\Classes\UserEntity as UserEntity;

class Users extends BaseController {

    public function index() {
        $twig = twig_instance();
        $twig->display('home/logged_out_content.html', []);
    }

    public function signIn() {
        $userToInsert = new UserEntity( // automatically hashes the pwd
            $this->request->getVar('signinMail'),
            $this->request->getVar('signinName'),
            $this->request->getVar('signinFirstName'),
            $this->request->getVar('signinDate'),
            $this->request->getVar('signinPwd')
        );
        $pwdConfirm = $this->request->getVar('signinPwdConfirm');

        if (empty($userToInsert->getMail()) || empty($userToInsert->getName()) || empty($userToInsert->getFirstName())
            || empty($userToInsert->getBirthIsoDate()) || empty($userToInsert->getPwd())) {
            $invalid_form_input = true;
            $msg = 'Merci de renseigner tous les champs du formulaire d\'inscription.';
        } else if ($userToInsert->getPwd() != hash('sha512', $pwdConfirm)) {
            $invalid_form_input = true;
            $msg = 'Le mot de passe de confirmation ne correspond pas à celui renseigné !';
        } else {
            $model = new \App\Models\UsersModel();

            if ($model->getUser($userToInsert->getMail()) != null) { // mail déjà en BD
                $invalid_form_input = true;
                $msg = 'Un compte correspondant à l\'adresse mail renseignée existe déjà.';
            } else {
                $model->save($userToInsert->toArray());
                $invalid_form_input = false;
                $title = 'Inscription réussie.';
                $msg = 'Vous pouvez maintenant vous connecter avec les identifiants renseignés lors de l\'inscription.';
            }
        }

        $twig = twig_instance();
        $twig->display('home/logged_out_content.html', [
            'sender_form' => 'd\'inscription',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : '',
        ]);
    }
}
