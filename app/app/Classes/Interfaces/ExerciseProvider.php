<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;
    use App\Classes\SolutionSubmissionEntity;

    interface ExerciseProvider {
        public function addSubmission(SolutionSubmissionEntity $ss);
        public function getSolverSubmissions(ExerciseSolver $s, ExerciseEntity $e);
        function getExerciseSubmissions(ExerciseEntity $e);
        public function getExerciseSummary(ExerciseEntity $e);
    }
}

