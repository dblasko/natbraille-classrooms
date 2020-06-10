<?php

namespace App\Classes {

    use App\Classes\Interfaces\ExerciseAssignee;
    use App\Classes\Interfaces\ExerciseProvider;
    use App\Classes\Interfaces\Notifiable;
    use App\Classes\Interfaces\ExerciseAssigner;
    use App\Classes\Interfaces\ExerciseSolver;
    use App\Models\ExerciseModel;
    use App\Models\NotificationsModel;
    use App\Models\PromotionModel;

    class UserEntity implements Notifiable, ExerciseAssigner, ExerciseSolver
    {
        private $mail;
        private $name;
        private $firstName;
        private $birthIsoDate;
        private $pwd;
        private $isDeleted;

        public function __construct($mail, $name, $firstName, $birthIsoDate, $pwd, $isDeleted = false)
        {
            $this->mail = $mail;
            $this->name = $name;
            $this->firstName = $firstName;
            $this->birthIsoDate = $birthIsoDate;
            $this->pwd = $pwd;
            $this->isDeleted = $isDeleted;
        }

        public function toArray() {
            return [
                'mail' => $this->mail,
                'name' => $this->name,
                'firstName' => $this->firstName,
                'birthIsoDate' => $this->birthIsoDate,
                'pwd' => $this->pwd,
                'isDeleted' => $this->isDeleted,
            ];
        }

        /* Notifiable implementation */

        public function getUnreadNotifications()
        {
            $model = new NotificationsModel;
            return $model->getUnreadNotifications($this->mail);
        }

        public function read(NotificationEntity $n)
        {
            $model = new NotificationsModel();
            $n->setIsRead(true);
            $model->saveEntity($n);
        }

        public function unread(NotificationEntity $n)
        {
            $model = new NotificationsModel();
            $n->setIsRead(false);
            $model->saveEntity($n);
        }

        /* ExerciseAssigner implementation */

        public function assign(ExerciseEntity $e, ExerciseAssignee $a) {
            // Returns false if coudln't be assigned because that exercise is already assigned to the promotion
            return $a->addExercise($e, $this);
        }

        public function unassign(ExerciseEntity $e, ExerciseAssignee $a) {
            $a->removeExercise($e);
        }

        /* ExerciseSolver implementation */

        public function submitSolution(SolutionSubmissionEntity $ss, ExerciseProvider $ep) {
            $ep->addSubmission($ss);
        }

        public function getSubmissions(ExerciseEntity $e, ExerciseProvider $ep) {
            // get the solver's (here this user) submissions for given exercise at the provider
            return $ep->getSolverSubmissions($this, $e);
        }


        public function getExercises() {
            $model = new ExerciseModel();
            return $model->getUserExercises($this);
        }

        public function getPromotions() {
            $model = new PromotionModel();
            return $model->getPromotionsMemberOf($this);
        }


        /**
         * @return mixed
         */
        public function getMail()
        {
            return $this->mail;
        }

        /**
         * @param mixed $mail
         */
        public function setMail($mail): void
        {
            $this->mail = $mail;
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
        public function getFirstName()
        {
            return $this->firstName;
        }

        /**
         * @param mixed $firstName
         */
        public function setFirstName($firstName): void
        {
            $this->firstName = $firstName;
        }

        /**
         * @return mixed
         */
        public function getBirthIsoDate()
        {
            return $this->birthIsoDate;
        }

        /**
         * @param mixed $birthIsoDate
         */
        public function setBirthIsoDate($birthIsoDate): void
        {
            $this->birthIsoDate = $birthIsoDate;
        }

        /**
         * @return mixed
         */
        public function getPwd()
        {
            return $this->pwd;
        }

        /**
         * @param mixed $pwd
         */
        public function setPwd($pwd): void
        {
            $this->pwd = $pwd;
        }

        /**
         * @return bool
         */
        public function getIsDeleted(): bool
        {
            return $this->isDeleted;
        }

        /**
         * @param bool $isDeleted
         */
        public function setIsDeleted(bool $isDeleted): void
        {
            $this->isDeleted = $isDeleted;
        }
    }
}