<?php namespace App\Models;
use App\Classes\ExerciseEntity;
use App\Classes\UserEntity;
use CodeIgniter\Model;

class ExerciseModel extends Model {
    protected $table = 'exercises';
    protected $allowedFields = ['lastUpdateIsoDate', 'name', 'exerciseContent', 'exerciseCorrection', 'commentaryContent',
        'createdByMail', 'idTranscriptionType'];

    public function getExercise($exerciseId) {
        $exerciseData = $this->asArray()
            ->where(['id' => $exerciseId])
            ->first();
        if ($exerciseData != null) {
            $exerciseData = new ExerciseEntity(
                $exerciseData['id'], $exerciseData['name'], $exerciseData['exerciseContent'], $exerciseData['exerciseCorrection'],
                $exerciseData['commentaryContent'], $exerciseData['createdByMail'], $exerciseData['idTranscriptionType']
            );
        }
        return $exerciseData;
    }

    public function getUserExercises(UserEntity $user) {
        $exercises = [];
        $query = $this->db->query("SELECT * FROM exercises WHERE createdByMail = ? ORDER BY lastUpdateIsoDate DESC", array($user->getMail()));

        if ($query != null) {
            foreach ($query->getResultArray() as $result) {
                $exercises[] = new ExerciseEntity(
                    $result['id'], $result['name'], $result['exerciseContent'], $result['exerciseCorrection'],
                    $result['commentaryContent'], $result['createdByMail'], $result['idTranscriptionType']
                );
            }
        }
        return $exercises;
    }

}