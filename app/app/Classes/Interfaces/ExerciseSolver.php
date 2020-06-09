<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;
    use App\Classes\SolutionSubmissionEntity;

    interface ExerciseSolver {
        public function submitSolution(SolutionSubmissionEntity $ss, ExerciseProvider $ep);
        public function getSubmissions(ExerciseEntity $e, ExerciseProvider $ep); // the solver's submissions for given exercise on the given provider -> calls getSolverSubmissions on ExerciseProvider
        public function getMail();
    }
}