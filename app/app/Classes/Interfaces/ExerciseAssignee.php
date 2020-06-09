<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;

    interface ExerciseAssignee {
        public function addExercise(ExerciseEntity $e, ExerciseAssigner $ea);
        public function removeExercise(ExerciseEntity $e);
        public function getAssignedExercisesList();
    }
}