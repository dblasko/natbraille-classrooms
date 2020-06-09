<?php namespace App\Models;
use App\Classes\ExerciseEntity;
use App\Classes\PromotionEntity;
use CodeIgniter\Model;

class ExerciseAssignmentModel extends Model {
    protected $table = 'notifications';
    protected $allowedFields = ['affectationIsoDate', 'idExo', 'idPromotion'];

    public function isExerciseAssigned(ExerciseEntity $e, PromotionEntity $p) {
        // TODO : confirm works well (data really null when no assignment)
        $data = $this->asArray()
            ->where(['idExo' => $e->getId(), 'idPromotion' => $p->getId()])
            ->first();
        return ($data != null);
    }
}