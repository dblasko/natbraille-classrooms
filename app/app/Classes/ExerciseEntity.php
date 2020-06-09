<?php namespace App\Classes {

    class ExerciseEntity {
        private $id;
        private $name;
        private $exerciseContent;
        private $exerciseCorrection;
        private $commentaryContent;
        private $createdByMail;
        private $idTranscriptionType;

        /**
         * ExerciseEntity constructor.
         * @param $id
         * @param $name
         * @param $exerciseContent
         * @param $exerciseCorrection
         * @param $commentaryContent
         * @param $createdByMail
         * @param $idTranscriptionType
         */
        public function __construct($id, $name, $exerciseContent, $exerciseCorrection, $commentaryContent, $createdByMail, $idTranscriptionType)
        {
            $this->id = $id;
            $this->name = $name;
            $this->exerciseContent = $exerciseContent;
            $this->exerciseCorrection = $exerciseCorrection;
            $this->commentaryContent = $commentaryContent;
            $this->createdByMail = $createdByMail;
            $this->idTranscriptionType = $idTranscriptionType;
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
        public function getExerciseContent()
        {
            return $this->exerciseContent;
        }

        /**
         * @param mixed $exerciseContent
         */
        public function setExerciseContent($exerciseContent): void
        {
            $this->exerciseContent = $exerciseContent;
        }

        /**
         * @return mixed
         */
        public function getExerciseCorrection()
        {
            return $this->exerciseCorrection;
        }

        /**
         * @param mixed $exerciseCorrection
         */
        public function setExerciseCorrection($exerciseCorrection): void
        {
            $this->exerciseCorrection = $exerciseCorrection;
        }

        /**
         * @return mixed
         */
        public function getCommentaryContent()
        {
            return $this->commentaryContent;
        }

        /**
         * @param mixed $commentaryContent
         */
        public function setCommentaryContent($commentaryContent): void
        {
            $this->commentaryContent = $commentaryContent;
        }

        /**
         * @return mixed
         */
        public function getCreatedByMail()
        {
            return $this->createdByMail;
        }

        /**
         * @param mixed $createdByMail
         */
        public function setCreatedByMail($createdByMail): void
        {
            $this->createdByMail = $createdByMail;
        }

        /**
         * @return mixed
         */
        public function getIdTranscriptionType()
        {
            return $this->idTranscriptionType;
        }

        /**
         * @param mixed $idTranscriptionType
         */
        public function setIdTranscriptionType($idTranscriptionType): void
        {
            $this->idTranscriptionType = $idTranscriptionType;
        }



    }
}