<?php namespace App\Classes {

    class SolutionSubmissionEntity {
        private $id;
        private $isoDate;
        private $exercise;
        private $userMail;
        private $score;
        private $attemptTxt;

        /**
         * SolutionSubmissionEntity constructor.
         * @param $id
         * @param $isoDate
         * @param $exercise
         * @param $userMail
         * @param $score
         * @param $attemptTxt
         */
        public function __construct($id, $isoDate, $exercise, $userMail, $score, $attemptTxt)
        {
            $this->id = $id;
            $this->isoDate = $isoDate;
            $this->exercise = $exercise;
            $this->userMail = $userMail;
            $this->score = $score;
            $this->attemptTxt = $attemptTxt;
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
        public function getIsoDate()
        {
            return $this->isoDate;
        }

        /**
         * @param mixed $isoDate
         */
        public function setIsoDate($isoDate): void
        {
            $this->isoDate = $isoDate;
        }

        /**
         * @return mixed
         */
        public function getExercise()
        {
            return $this->exercise;
        }

        /**
         * @param mixed $exercise
         */
        public function setExercise($exercise): void
        {
            $this->exercise = $exercise;
        }

        /**
         * @return mixed
         */
        public function getUserMail()
        {
            return $this->userMail;
        }

        /**
         * @param mixed $userMail
         */
        public function setUserMail($userMail): void
        {
            $this->userMail = $userMail;
        }

        /**
         * @return mixed
         */
        public function getScore()
        {
            return $this->score;
        }

        /**
         * @param mixed $score
         */
        public function setScore($score): void
        {
            $this->score = $score;
        }

        /**
         * @return mixed
         */
        public function getAttemptTxt()
        {
            return $this->attemptTxt;
        }

        /**
         * @param mixed $attemptTxt
         */
        public function setAttemptTxt($attemptTxt): void
        {
            $this->attemptTxt = $attemptTxt;
        }


    }
}