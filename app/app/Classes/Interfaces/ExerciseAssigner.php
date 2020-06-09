<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;

    interface ExerciseAssigner {
        public function assign(ExerciseEntity $e, ExerciseAssignee $a);
        public function unassign(ExerciseEntity $e, ExerciseAssignee $a);
    }
}