<?php namespace App\Controllers;


use App\Classes\PromotionEntity;
use App\Models\ExerciseAssignmentModel;
use App\Models\PromotionModel;
use App\Models\UsersModel;

class Promotions extends BaseController {

    public function index() {
        // TODO : temporary
        header("Location: /");
        exit();
    }

    public function viewPromotionSpace($promotionId = null) { // is routed to /promotions/{id}, to consult a promotion space
        $invalid_form_input = false;
        $model = new PromotionModel();

        if (!session('loggedIn')) {
            $invalid_form_input = true;
            $msg = 'Vous devez être connecté pour consulter une promotion';
            $redir = 'home/logged_out_content.html';
        } else if ($promotionId === null || !$model->isValidPromotionId($promotionId)) {
            $invalid_form_input = true;
            $msg = 'La promotion que vous essayez de consulter n\'existe pas.';
            $data = Users::prepareLoggedInUserData(session('user'));
            $redir = 'home/workspace.html';
        } else {
            if (!$model->isUserMemberOfPromo(session('user')->getMail(), $promotionId)) {
                $invalid_form_input = true;
                $msg = 'Vous n\'êtes pas membre de la promotion que vous avez essayé de consulter.';
                $data = Users::prepareLoggedInUserData(session('user'));
                $redir = 'home/workspace.html';
            } else { // user is logged in, the promotion exists and he's a member of it
                $promotionData['promotion'] = $model->getPromotionEntity($promotionId);
                $promotionData['isCurrentUserTeacher'] = in_array(session('user'), $promotionData['promotion']->getExerciseAssigners());
                $redir = '/promotion/promotion_space.html';
            }
        }

        $twig = twig_instance();
        $twig->display($redir, [
            'sender_form' => 'de consultation d\'une promotion',
            'invalid_form_input' => $invalid_form_input,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
            'promotionData' => isset($promotionData)? $promotionData : null,
        ]);
    }

    public function join($promotionLink) {
        /*
         * If the user is not logged in, the session stores that he wants to join and when he logs in, he will be redirected here to join
         * Else, he directly tries to join
         */
        $promoModel = new PromotionModel();
        $invalid_form_input = false;

        if (session('loggedIn')) {
            session()->remove('wantsToJoin');
            if (!$promoModel->isValidLink($promotionLink)) {
                $invalid_form_input = true;
                $msg = 'Le lien d\'ajout à une promotion est invalide. Celui-ci a peut être été changé par un enseignant.';
            } else {
                $success = $promoModel->addUserToPromotion(session('user'), $promotionLink);
                if (!$success) {
                    $invalid_form_input = true;
                    $msg = 'Votre compte est déjà membre de la promotion correspondant au lien utilisé !';
                } else {
                    $title = 'Votre compte a bien été ajouté à la promotion.';
                    $msg = 'Celle-ci devrait apparaître dans votre espace de travail dorénavant.';
                }
            }

            $data = Users::prepareLoggedInUserData(session('user'));
            $twig = twig_instance();
            $twig->display('home/workspace.html', [
                'sender_form' => 'd\'ajout à une promotion',
                'invalid_form_input' => $invalid_form_input,
                'title' => isset($title)? $title : null,
                'msg' => isset($msg)? $msg : null,
                'session' => session(),
                'workspaceData' => isset($data)? $data : null,
            ]);
        } else {
            session()->set('wantsToJoin', $promotionLink);
            header("Location: /");
            exit();
        }
    }

    public function leave($promotionId=null) {
        $invalid_form_input = false;
        $model = new PromotionModel();

        if (!session('loggedIn')) {
            $invalid_form_input = true;
            $msg = 'Vous devez être connecté pour quitter une promotion';
            $redir = 'home/logged_out_content.html';
        } else if ($promotionId === null || !$model->isValidPromotionId($promotionId)) {
            $invalid_form_input = true;
            $msg = 'La promotion que vous essayez de quitter n\'existe pas.';
            $data = Users::prepareLoggedInUserData(session('user'));
            $redir = 'home/workspace.html';
        } else {
            if (!$model->isUserMemberOfPromo(session('user')->getMail(), $promotionId)) {
                $invalid_form_input = true;
                $msg = 'Vous n\'êtes pas membre de la promotion que vous avez essayé de quitter.';
                $data = Users::prepareLoggedInUserData(session('user'));
                $redir = 'home/workspace.html';
            } else { // user is logged in, the promotion exists and he's a member of it
                $promotion = $model->getPromotionEntity($promotionId);

                if($model->removeUserFromPromotion(session('user'), $promotionId)) {
                    $title = 'Promotion '.$promotion->getName().' quittée.';
                    $msg = 'Celle-ci ne vous sera plus accessible et n\'apparaîtra plus dans la liste des promotions dont vous êtes membre.';
                } else { // leaving failed
                    $invalid_form_input = true;
                    $msg = 'Votre suppression de la promotion '.$promotion->getName().' a échouée. Veuillez réessayer plus tard.';
                }
                $data = Users::prepareLoggedInUserData(session('user'));
                $redir = 'home/workspace.html';
            }
        }

        $twig = twig_instance();
        $twig->display($redir, [
            'sender_form' => 'de consultation d\'une promotion',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
            'promotionData' => isset($promotionData)? $promotionData : null,
        ]);
    }

    public function unassign($affectationId=null) {
        // check co, id valide à une affectation (get la promo), membre, rôle enseignant sur la promo
        // Puis appel modèle unassign et préparer msg et recharcher espace de la promo avec info bien passé
        // TODO : check si marche bien (cas erreur aussi, déco, pas prof de la promo, refresh...)

        $invalid_form_input = false;
        $model = new PromotionModel();
        $exoModel = new ExerciseAssignmentModel();

        if (!session('loggedIn')) {
            $invalid_form_input = true;
            $msg = 'Vous devez être connecté pour désaffecter un exercice.';
            $redir = 'home/logged_out_content.html';
        } else if ($affectationId === null || !$exoModel->isValidAffectationId($affectationId)) {
            $invalid_form_input = true;
            $msg = 'L\'exercice que vous essayez de désaffecter n\'existe pas.';
            $data = Users::prepareLoggedInUserData(session('user'));
            $redir = 'home/workspace.html';
        } else {
            $promoId = $exoModel->getPromotionIdThatExerciseIsAffectedTo($affectationId);
            if (!$model->isUserTeacherOfPromo(session('user')->getMail(), $promoId)) {
                $invalid_form_input = true;
                $msg = 'Vous n\'êtes pas un enseignant de la promotion à laquelle vous avez essayé de désaffecter un exercice.';
                $data = Users::prepareLoggedInUserData(session('user'));
                $redir = 'home/workspace.html';
            } else { // user is logged in, the promotion exists and he's a teacher of it
                $exoModel->unassign($affectationId);
                $title = 'Exercice désaffecté de la promotion.';
                $msg = 'Il n\'apparaîtra plus dans la liste des exercices de la promotion.';

                $promotionData['promotion'] = $model->getPromotionEntity($promoId);
                $promotionData['isCurrentUserTeacher'] = in_array(session('user'), $promotionData['promotion']->getExerciseAssigners());
                $redir = 'promotion/promotion_space.html';
            }
        }

        $twig = twig_instance();
        $twig->display($redir, [
            'sender_form' => 'de désaffectation d\'un exercice à promotion',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
            'promotionData' => isset($promotionData)? $promotionData : null,
        ]);
    }

    private function httpError($msg = '') {
        header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error', true, 500);
        echo $msg;
        exit;
    }

    public function changeRole() {
        $promoModel = new PromotionModel();
        $userModel = new UsersModel();

        $firstName = isset($_POST['firstName'])? $_POST['firstName'] : NULL;
        $lastName = isset($_POST['lastName'])? $_POST['lastName'] : NULL;
        $wantedRole = isset($_POST['wantedRole'])? $_POST['wantedRole'] : NULL;
        $promotionId = isset($_POST['promotionId'])? $_POST['promotionId'] : NULL;

        if ($firstName == NULL || $lastName == NULL || $wantedRole == NULL || $promotionId == NULL) {
            $userHavingRoleChanged = NULL;
        } else {
            $userHavingRoleChanged = $userModel->getUserByNameInPromotion($firstName, $lastName, $promotionId);
        }

        if (!session('loggedIn') ||     // pas co
            !$promoModel->isUserTeacherOfPromo(session('user')->getMail(), $promotionId) ||      // pas droit prof
            $wantedRole !== ROLE_TEACHER && $wantedRole !== ROLE_STUDENT ||   // rôle invalide
            $userHavingRoleChanged == NULL // pas d'user dans la promo correspondant avec ce first et last name
        ) {
            // retourner erreur
            $this->httpError('Le rôle n\'a pas pu être changé.');
        } else { // security checks ok
            // changer en BD et retourner 200
            if ($promoModel->changeUserRole($userHavingRoleChanged, $promotionId, $wantedRole)) echo 'Rôle mis à jour.';
            else $this->httpError('Le rôle n\'a pas pu être changé.');
            exit();
        }
    }


    public function kick($promotionId = NULL, $lastName = NULL, $firstName = NULL) {
        $promoModel = new PromotionModel();
        $userModel = new UsersModel();
        $invalid_form_input = false;

        if ($firstName == NULL || $lastName == NULL || $promotionId == NULL) {
            $userGettingKicked = NULL;
        } else {
            $userGettingKicked = $userModel->getUserByNameInPromotion($firstName, $lastName, $promotionId);
        }

        if (!session('loggedIn')) {
            $invalid_form_input = true;
            $msg = 'Vous devez être connecté pour renvoyer un(e) membre d\' une promotion.';
            $redir = 'home/logged_out_content.html';
        } else if ($userGettingKicked == NULL) { // membre pas dans la promo ou promo n'existe pas
            $invalid_form_input = true;
            $msg = 'Vous essayez de renvoyer un membre qui n\'existe pas, ou la promotion n\'existe pas.';
            $userData = Users::prepareLoggedInUserData(session('user'));
            $redir = 'home/workspace.html';
        } else if (!$promoModel->isUserTeacherOfPromo(session('user')->getMail(), $promotionId)) {
            $invalid_form_input = true;
            $msg = 'Vous n\'êtes pas enseignant(e) de la promotion dont vous essayez de renvoyer un(e) membre.';

            $promotionData['promotion'] = $promoModel->getPromotionEntity($promotionId);
            $promotionData['isCurrentUserTeacher'] = in_array(session('user'), $promotionData['promotion']->getExerciseAssigners());
            $redir = 'promotion/promotion_space.html';
        } else {
            // test renvoi avec modèle, et en fonction succès ou pas render succès ou pas avec data
            // => set title + msg pour succès & invalid sera faux, sinon true + que msg + sender_form
            if ($promoModel->removeUserFromPromotion($userGettingKicked, $promotionId)) {
                $title = '';
                $msg = '';
            } else { // failed
                $invalid_form_input = true;
                $msg = 'Le membre n\'a pas pu être renvoyé de la promotion. Veuillez réessayer plus tard.';
            }
            $promotionData['promotion'] = $promoModel->getPromotionEntity($promotionId);
            $promotionData['isCurrentUserTeacher'] = in_array(session('user'), $promotionData['promotion']->getExerciseAssigners());
            $redir = 'promotion/promotion_space.html';
        }

        // promo data ou workspace data utilisée selon ce qu'on render
        $twig = twig_instance();
        $twig->display($redir, [
            'sender_form' => 'de renvoi d\'un membre d\'une promotion',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($userData)? $userData : null,
            'promotionData' => isset($promotionData)? $promotionData : null,
        ]);
    }

    public function create() {
        $invalid_form_input = false;

        if (!isset($_POST['createPromoSubmit'])) { // wants to see the form
            $redir = 'promotion/promotion_creation.html';
        } else { // submitting form
            $redir = 'home/workspace.html';
            if (!isset($_POST['promoName']) || !isset($_POST['isClosedPromotion'])) {
                $invalid_form_input = true;
                $msg = 'Veuillez renseigner l\'ensemble des champs du formulaire (sauf le fichier CSV).';
            } else if (!session('loggedIn')) {
                $invalid_form_input = true;
                $msg = 'Vous devez être connecté(e) pour créer une promotion.';
            } else {
                $name = htmlspecialchars($_POST['promoName']);
                $isClosedPromotion = ($_POST['isClosedPromotion'] == 1);
                $promo = new PromotionEntity(-1, $name, null, $isClosedPromotion, null, null, null, null);
                $promo->generateInviteLink();
                $promoId = $promo->saveNewToDb();
                if (!$promoId) {
                    $invalid_form_input = true;
                    $msg = 'La promotion n\'a pas pu être crée en base de données. Veuillez réessayer plus tard.';
                } else { // Make creator member & teacher of that promotion !
                    $promoModel = new PromotionModel();
                    $promoModel->addUserToPromotion(session('user'), $promo->getLink());
                    $promoModel->changeUserRole(session('user'), $promoId, ROLE_TEACHER);
                    // TODO : handling the CSV should be done here...

                    $title = 'Promotion '.$promo->getName().' créée avec succès.';
                    $msg = 'Celle-ci apparaîtra dans votre espace de travail, avec la visibilité spécifiée. Vous avez le rôle d\'enseignant au sein de la promotion.';
                }
            }
            $userData = Users::prepareLoggedInUserData(session('user'));
        }

        $twig = twig_instance();
        $twig->display($redir, [
            'sender_form' => 'de création d\'une promotion',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($userData)? $userData : null,
        ]);
    }


    public function update() {
        $promoModel = new PromotionModel();

        $promotionName = isset($_POST['promotionName'])? $_POST['promotionName'] : NULL;
        $modifiedIsClosedPromotion = isset($_POST['modifiedIsClosedPromotion'])? $_POST['modifiedIsClosedPromotion'] : NULL;
        $promotionId = isset($_POST['promotionId'])? $_POST['promotionId'] : NULL;

        if ($promotionName == NULL || $modifiedIsClosedPromotion == NULL || $promotionId == NULL) {
            $promotionEntity = NULL;
        } else {
            $promotionEntity = $promoModel->getPromotionEntity($promotionId); // null if ID doesn't exist
        }

        if ($promotionEntity == NULL || // id doesn't exist or missing form data
            !session('loggedIn') ||     // not logged in
            !$promoModel->isUserTeacherOfPromo(session('user')->getMail(), $promotionId) // not a teacher of the promotion
        ) {
            $this->httpError('La promotion n\'a pu être mise à jour.');
        } else { // security checks ok
            // update db & return http200
            if ($promoModel->updatePromo($promotionId, $promotionName, $modifiedIsClosedPromotion)) echo 'Promotion mise à jour.';
            else $this->httpError('La promotion n\a pu être mise à jour.');
            exit();
        }
    }
}