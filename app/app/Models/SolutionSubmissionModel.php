<?php namespace App\Models;

use CodeIgniter\Model;
use App\Classes\SolutionSubmissionEntity;

class SolutionSubmissionModel extends Model {
    protected $table = 'attempt';
    protected $allowedFields = ['isoDate', 'idAffectation', 'idPromotion'];

    public function saveEntities($affectationArrays) {
        /*
         * Syncs all submissions on a promotion to the DB
         */
        // TODO : check if works correctly
        foreach ($affectationArrays as $affectationArray) {
            $affectationId = $affectationArray['id'];
            $solutionSubmissions = $affectationArray['submissions'];
            foreach ($solutionSubmissions as $solutionSubmission) {
                $data = [
                    'isoDate' => $solutionSubmission->getIsoDate(),
                    'idAffectation' => $affectationId,
                    'userMail' => $solutionSubmission->getUserMail(),
                    'score' => $solutionSubmission->getScore(),
                    'attemptTxt' => $solutionSubmission->getAttemptText(),
                ];
                // If is null, new entity that has to be saved to the DB, else update or do nothing
                if ($solutionSubmission->getId() != null) $data['id'] = $solutionSubmission->getId();
                $this->save($data);
            }
        }
    }


}