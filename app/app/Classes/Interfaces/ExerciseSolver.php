<?php namespace App\Classes\Interfaces {

    use App\Classes\ExerciseEntity;

    interface ExerciseSolver {
        public function submitSolution(SolutionSubmissionEntity $ss, ExerciseProvider $ep);
        public function getSubmissions(Exercise $e, ExerciseProvider $ep); // the solver's submissions for given exercise on the given provider -> calls getSolverSubmissions on ExerciseProvider
    }
}