<?php namespace App\Controllers;


use App\Models\PromotionModel;

class Promotions extends BaseController {

    public function join($promotionLink) {
        /*
         * If the user is not logged in, the session stores that he wants to join and when he logs in, he will be redirected here to join
         * Else, he directly tries to join
         */
        // TODO : tester tous les cas : join lien, join bouton, join deco ->co, join deoc ->inscrip->co, refresh après join
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