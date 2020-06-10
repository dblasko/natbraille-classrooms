<?php namespace App\Controllers;

use App\Classes\UserEntity as UserEntity;
use App\Models\NotificationsModel;
use App\Models\PromotionModel;
use App\Models\UsersModel;

use DateTime;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Users extends BaseController {

    public function index() {
        $twig = twig_instance();
        $twig->display('home/logged_out_content.html', ['session' => session()]);
    }

    protected function sendConfirmationMail($mailAddr, $name) {
        $mail = new PHPMailer(true);

        $mail->AddAddress($mailAddr, $name);
        $mail->SetFrom('no-reply@natbraille.fr', 'Natbraille Association');
        $mail->Subject = 'Création de votre compte Natbraille Classrooms';
        $mail->Body = 'Nous vous confirmons la création de votre compte Natbraille Classrooms.\nCelui-ci est lié au compte mail où ce courrier est reçu. Vous pouvez dès maintenant vous connecter avec votre adresse mail et votre mot de passe.\nBienvenue sur Natbraille Classrooms !';

        try{
            $mail->Send();
        } catch(Exception $e){
            echo 'Mail sending failed.';
        }

    }

    protected function addWelcomeNotification($mailAddr) {
        $model = new NotificationsModel();
        $model->createNotification(date("Y-m-d H:i:s"), 'Bienvenue sur Natbraille Classrooms !', $mailAddr, '/');
    }

    protected function convertDate($date) {
        /*
         * Returns false if the date can't be parsed, else a date that can be saved in MySQL
         */
        $convert =  DateTime::createFromFormat('d-m-Y', $this->request->getVar('signinDate'));
        return ($convert !== false)? $convert->format('Y-m-d'): false;
    }

    public function signIn() {
        $userToInsert = new UserEntity( // automatically hashes the pwd
            htmlspecialchars($this->request->getVar('signinMail')),
            htmlspecialchars($this->request->getVar('signinName')),
            htmlspecialchars($this->request->getVar('signinFirstName')),
            htmlspecialchars($this->request->getVar('signinDate')),
            hash('sha512', htmlspecialchars($this->request->getVar('signinPwd'))) // password hash is saved to the DB
        );
        $pwdConfirmValue = htmlspecialchars($this->request->getVar('signinPwdConfirm'));
        $parsedDate = $this->convertDate($userToInsert->getBirthIsoDate()); // parse & transform the date to the correct format for the DB

        if (empty($userToInsert->getMail()) || empty($userToInsert->getName()) || empty($userToInsert->getFirstName())
            || empty($userToInsert->getBirthIsoDate()) || empty($userToInsert->getPwd())) {
            $invalid_form_input = true;
            $msg = 'Merci de renseigner tous les champs du formulaire d\'inscription.';
        } else if ($parsedDate === false) {
            $invalid_form_input = true;
            $msg = 'La date entrée est invalide. Merci d\'entrer une date au format jj-mm-aaaa.';
        } else if ($userToInsert->getPwd() != hash('sha512', $pwdConfirmValue)) { // the user pwd has been hashed, note the confirm value
            $invalid_form_input = true;
            $msg = 'Le mot de passe de confirmation ne correspond pas à celui renseigné !';
        } else if (!filter_var($userToInsert->getMail(), FILTER_VALIDATE_EMAIL)) {
            $invalid_form_input = true;
            $msg = 'L\'adresse mail renseignée n\'est pas valide.';
        } else {
            $model = new UsersModel();

            if ($model->getUser($userToInsert->getMail()) != null) { // mail déjà enregistré en BD
                $invalid_form_input = true;
                $msg = 'Un compte correspondant à l\'adresse mail renseignée existe déjà.';
            } else { // inscription
                $userToInsert->setBirthIsoDate($parsedDate); // the parsed date is correct, we can save it now
                $model->saveEntity($userToInsert);
                //$this->sendConfirmationMail($userToInsert->getMail(), $userToInsert->getFirstName().' '.$userToInsert->getName());
                $this->addWelcomeNotification($userToInsert->getMail());
                $invalid_form_input = false;
                $title = 'Inscription réussie.';
                $msg = 'Un e-mail de confirmation devrait vous parvenir sous peu. Vous pouvez dès maintenant vous connecter avec les identifiants renseignés lors de l\'inscription.';
            }
        }

        $twig = twig_instance();
        $twig->display('home/logged_out_content.html', [
            'sender_form' => 'd\'inscription',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : '',
            'session' => session(),
        ]);
    }


    public static function prepareLoggedInUserData($user) {
        $data['notifications'] = $user->getUnreadNotifications();
        $data['exercises'] = $user->getExercises();
        $data['promotions'] = $user->getPromotions();
        $model = new PromotionModel();
        $data['openPromotions'] = $model->getOpenPromotions();
        return $data;
    }

    public function logIn() {
        $mail = $this->request->getVar('loginMail');
        $pwd = $this->request->getVar('loginPwd');

        if (empty($mail) || empty($pwd)) {
            $invalid_form_input = true;
            $msg = "Veuillez renseigner votre mail et votre mot de passe afin de vous connecter.";
        } else {
            $model = new UsersModel();
            $userDbData = $model->getUser($mail);

            if ($userDbData == null) {
                $invalid_form_input = true;
                $msg = "Il n'existe pas de compte associé à l'adresse mail renseignée.";
            } else if ($userDbData->getPwd() !== hash('sha512', $pwd)) {
                $invalid_form_input = true;
                $msg = "Le mot de passe renseigné est invalide.";
            } else { // connecté
                $invalid_form_input = false;
                session()->set('loggedIn', true);
                session()->set('user', $userDbData);
                $data = $this->prepareLoggedInUserData($userDbData);
            }
        }

        $redirect = 'home/'. (($invalid_form_input)? 'logged_out_content.html' : 'workspace.html');
        $twig = twig_instance();
        $twig->display($redirect, [
            'sender_form' => 'de connexion',
            'invalid_form_input' => $invalid_form_input,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
        ]);
    }

    public function logOut() {
        session()->set('loggedIn', false);
        $twig = twig_instance();
        $twig->display('home/logged_out_content.html', [
            'title' => 'Vous êtes déconnecté.',
            'msg' => 'Vous pouvez quitter le site ou vous reconnecter ci-dessous.',
            'session' => session(),
            'invalid_form_input' => false,
        ]);
    }

    public function read($notifId) {
        $user = session('user');
        $model = new NotificationsModel();
        $notification = $model->getById($notifId);
        if ($user != null && $notification != null) {
            $user->read($notification); 
        }
        header("Location: /");
        exit();
    }
}
