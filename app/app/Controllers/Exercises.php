<?php namespace App\Controllers;

use App\Models\ExerciseModel;

class Exercises extends BaseController {

    public function remove($exerciseId=null) {
        if (!session('loggedIn')) {
            $invalid_form_input = true;
            $msg = 'Vous ne pouvez supprimer un exercice sans être authentifié !';
            $redirect = 'home/logged_out_content.html';
        } else {
            $redirect = 'home/workspace.html';
            $model = new ExerciseModel();
            $exercise = $model->getExercise($exerciseId);

            if ($exercise == null || $exerciseId == null) {
                $invalid_form_input = true;
                $msg = 'L\'exercice à supprimer n\'existe pas !';
            } else if (session('user')->getMail() != $exercise->getCreatedByMail()) {
                $invalid_form_input = true;
                $msg = 'Vous ne pouvez supprimer un exercice que vous n\'avez pas créé !';
            } else {
                $invalid_form_input = false;
                $model->delete($exerciseId);
                $title = 'L\'exercice '.$exercise->getName().' a été supprimé.';
            }

            $data = Users::prepareLoggedInUserData(session('user'));
        }

        $twig = twig_instance();
        $twig->display($redirect, [
            'sender_form' => 'de suppression d\'exercice',
            'invalid_form_input' => $invalid_form_input,
            'title' => isset($title)? $title : null,
            'msg' => isset($msg)? $msg : null,
            'session' => session(),
            'workspaceData' => isset($data)? $data : null,
        ]);
    }
}