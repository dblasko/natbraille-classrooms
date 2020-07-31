<?php

namespace App\Classes {

    use App\Classes\Interfaces\ExerciseAssignee;
    use App\Classes\Interfaces\ExerciseAssigner;
    use App\Classes\Interfaces\ExerciseProvider;
    use App\Classes\Interfaces\ExerciseSolver;
    use App\Models\ExerciseAssignmentModel;
    use App\Models\PromotionModel;

    class PromotionEntity implements ExerciseAssignee, ExerciseProvider {
        /*
         * Is an ExerciseAssignee to the ExerciseAssigners
         * Is an ExerciseProvider to the ExerciseSolvers
         */
        private $id;
        private $name;
        private $link;
        private $creationIsoDate;
        private $isClosedPromotion;
        private $exerciseAssigners; // cf class diagram for more logic
        private $exerciseSolvers; // cf class diagram for more logic
        /*
         * ExerciseAssignations is a list of arrays, each array represents an exercise assignation
         * Each sub-array has exercise, submissions, id, date & assigner indexes
         * submissions is another array of SolutionSubmissionEntities
         */
        private $exerciseAssignations;

        /* ExerciseAssignee implementations */
        public function addExercise(ExerciseEntity $e, ExerciseAssigner $ea) {
            $assignModel = new ExerciseAssignmentModel();
            if ($assignModel->isExerciseAssigned($e, $this)) {
                return false; // this exercise is already assigned to this promotion, display msg in the view
            }

            $this->exerciseAssignations[] = [
                'exercise' => $e,
                'submissions' => [],
                'assigner' => $ea,
                'date' => date("Y-m-d H:i:s"),
            ];
            $assignModel->syncExercises($this);
            return true;
        }

        public function removeExercise(ExerciseEntity $e) {
            $assignModel = new ExerciseAssignmentModel();
            if ($assignModel->isExerciseAssigned($e, $this)) {
                foreach ($this->exerciseAssignations as $assignation) {
                    if ($assignation['exercise']->getId() === $e->getId()) unset($assignation);
                }
                $assignModel->syncExercises($this);
            }
        }

        public function getAssignedExercisesList() {
            $exercises = [];
            foreach ($this->exerciseAssignations as $assignation) {
                $exercises[] = $assignation['exercise'];
            }
            return $exercises;
        }

        /* ExerciseProvider implementations */

        public function addSubmission(SolutionSubmissionEntity $ss) {
            $exercise = $ss->getExercise();
            foreach ($this->exerciseAssignations as $assignation) {
                if ($assignation['exercise']->getId() === $exercise->getId()) {
                    $assignation['submissions'][] = $ss;
                }
            }
            $model = new ExerciseAssignmentModel();
            $model->syncExercises($this);
        }

        public function getSolverSubmissions(ExerciseSolver $s, ExerciseEntity $e) {
            $submissions = []; // SolutionSubmissionEntities
            foreach($this->exerciseAssignations as $assignation) {
                if ($assignation['exercise']->getId() === $e->getId()) {
                    foreach($assignation['submissions'] as $submission) {
                        if ($submission->getSolver()->getMail() === $s->getMail()) $submissions[] = $submission;
                    }
                }
            }
            return $submissions;
        }

        function getExerciseSubmissions(ExerciseEntity $e) {
            $submissions = [];
            foreach ($this->exerciseAssignations as $assignation) {
                if ($assignation['exercise']->getId() === $e->getId()) {
                    $submissions = $assignation['submissions'];
                }
            }
            return $submissions;
        } // for getExerciseSummary

        public function getExerciseSummary(ExerciseEntity $e) {
            // TODO : implement when needed and when decided on exact logic
            // use getExerciseSubmissions as a helper
        }



        public function saveNewToDb() {
            $promoModel = new PromotionModel();
            return $promoModel->createPromotion(
                $this->name,
                $this->isClosedPromotion,
                $this->link
            );
        }

        public function generateInviteLink() {
            $seed = random_int(0, 99);
            $encodedName = base64_encode($this->name);
            $this->link = $encodedName . strval($seed);
        }

        /**
         * PromotionEntity constructor.
         * @param $name
         * @param $link
         * @param $exerciseAssigners
         * @param $exerciseSolvers
         */
        public function __construct($id, $name, $link, $isClosedPromotion, $creationIsoDate, $exerciseAssigners, $exerciseSolvers, $exerciseAssignations)
        {
            $this->id = $id;
            $this->name = $name;
            $this->link = $link;
            $this->isClosedPromotion = $isClosedPromotion;
            $this->$creationIsoDate = $creationIsoDate;
            $this->exerciseAssigners = $exerciseAssigners;
            $this->exerciseSolvers = $exerciseSolvers;
            $this->exerciseAssignations = $exerciseAssignations;
        }

        public function getPublicLink() {
            return site_url('promotions/join/'.$this->link);
        }

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param mixed $id
         */
        public function setId($id): void
        {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getCreationIsoDate()
        {
            return $this->creationIsoDate;
        }

        /**
         * @param mixed $creationIsoDate
         */
        public function setCreationIsoDate($creationIsoDate): void
        {
            $this->creationIsoDate = $creationIsoDate;
        }



        /**
         * @return mixed
         */
        public function getIsClosedPromotion()
        {
            return $this->isClosedPromotion;
        }

        /**
         * @param mixed $isClosedPromotion
         */
        public function setIsClosedPromotion($isClosedPromotion): void
        {
            $this->isClosedPromotion = $isClosedPromotion;
        }


        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name): void
        {
            $this->name = $name;
        }

        /**
         * @return mixed
         */
        public function getLink()
        {
            return $this->link;
        }

        /**
         * @return mixed
         */
        public function getExerciseAssignations()
        {
            return $this->exerciseAssignations;
        }

        /**
         * @param mixed $exerciseAssignations
         */
        public function setExerciseAssignations($exerciseAssignations): void
        {
            $this->exerciseAssignations = $exerciseAssignations;
        }



        /**
         * @param mixed $link
         */
        public function setLink($link): void
        {
            $this->link = $link;
        }

        /**
         * @return mixed
         */
        public function getExerciseAssigners()
        {
            return $this->exerciseAssigners;
        }

        /**
         * @param mixed $exerciseAssigners
         */
        public function setExerciseAssigners($exerciseAssigners): void
        {
            $this->exerciseAssigners = $exerciseAssigners;
        }

        /**
         * @return mixed
         */
        public function getExerciseSolvers()
        {
            return $this->exerciseSolvers;
        }

        /**
         * @param mixed $exerciseSolvers
         */
        public function setExerciseSolvers($exerciseSolvers): void
        {
            $this->exerciseSolvers = $exerciseSolvers;
        }


    }
}