<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;

    interface ExerciseProvider {
        public function addSubmission(SolutionSubmissionEntity $ss, ExerciseEntity $e);
        public function getSolverSubmissions(ExerciseSolver $s, ExerciseEntity $e);
        function getExerciseSubmissions(ExerciseEntity $e); // for getExerciseSummary
        public function getExerciseSummary(ExerciseEntity $e);
    }
}