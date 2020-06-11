<?php namespace App\Controllers;


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
                $msg = 'Vous n\'êtes pas membre de la promotion que vous avez essayé de consulter';
                $data = Users::prepareLoggedInUserData(session('user'));
                $redir = 'home/workspace.html';
            } else { // user is logged in, the promotion exists and he's a member of it
                $promotion = $model->getPromotionEntity($promotionId);
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
            'promotionData' => isset($promotion)? $promotion : null,
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
}