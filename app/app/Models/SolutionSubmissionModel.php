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

    public function getAssignationSubmissions($assignationId, $exerciseEntity=null) {
        /*
         * Gets all SolutionSubmission corresponding to an exercise's assignation to a promotion
         * Since the exerciseEntity is usually available when handling the assignation (and when calling this function),
         *  we pass it as a parameter in order to not have to go through another query to create it for each SolutionSubmissionEntity
         */
        $submissions = [];
        $query = $this->db->query("SELECT * FROM attempt WHERE idAffectation = ?", array($assignationId));

        if ($query != null) {
            foreach($query->getResultArray() as $submissionData) {
                $submission = new SolutionSubmissionEntity(
                    $submissionData['id'], $submissionData['isoDate'], $exerciseEntity, $submissionData['userMail'],
                    $submissionData['score'], $submissionData['attemptTxt']
                );
                $submissions[] = $submission;
            }
        }
        return $submissions;
    }


}