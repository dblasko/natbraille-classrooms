<?php namespace App\Controllers;


use App\Models\ExerciseAssignmentModel;
use App\Models\PromotionModel;

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
}