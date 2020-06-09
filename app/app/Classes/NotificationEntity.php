<?php namespace App\Classes {


    class NotificationEntity {
        private $id;
        private $isRead;
        private $text;
        private $link;

        public function __construct($id, $isRead, $text, $link) {
            $this->id = $id;
            $this->isRead = $isRead;
            $this->text = $text;
            $this->link = $link;
        }

        public function toArray() {
            return [
                'id' => $this->id,
                'isSeen' => $this->isRead,
                'content' => $this->text,
                'link' => $this->link,
            ];
        }

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
        }

        /**
         * @return mixed
         */
        public function getIsRead()
        {
            return $this->isRead;
        }

        /**
         * @param mixed $isRead
         */
        public function setIsRead($isRead): void
        {
            $this->isRead = $isRead;
        }

        /**
         * @return mixed
         */
        public function getText()
        {
            return $this->text;
        }

        /**
         * @param mixed $text
         */
        public function setText($text): void
        {
            $this->text = $text;
        }

        /**
         * @return mixed
         */
        public function getLink()
        {
            return $this->link;
        }

        /**
         * @param mixed $link
         */
        public function setLink($link): void
        {
            $this->link = $link;
        }


    }
}