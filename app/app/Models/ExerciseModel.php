<?php namespace App\Model;
use App\Classes\ExerciseEntity;
use App\Classes\UserEntity;
use CodeIgniter\Model;

class ExerciseModel extends Model {
    protected $table = 'exercises';
    protected $allowedFields = ['lastUpdateIsoDate', 'name', 'exerciseContent', 'exerciseCorrection', 'commentaryContent',
        'createdByMail', 'idTranscriptionType'];

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