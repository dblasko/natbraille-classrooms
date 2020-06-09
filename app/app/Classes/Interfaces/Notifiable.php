<?php

namespace App\Classes\Interfaces {

    use App\Classes\NotificationEntity;

    interface Notifiable
    {
        public function getUnreadNotifications();

        public function read(NotificationEntity $n);

        public function unread(NotificationEntity $n);
    }

}