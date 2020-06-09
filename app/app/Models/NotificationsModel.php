<?php namespace App\Models;
use CodeIgniter\Model;

use App\Classes\NotificationEntity;


class NotificationsModel extends Model
{
    protected $table = 'notifications';
    protected $allowedFields = ['isoDate', 'content', 'isSeen', 'userMail', 'link'];

    public function saveEntity($notification) {
        $this->save($notification->toArray());
    }

    public function getUnreadNotifications($userMail) {
        $results = $this->asArray()
            ->where(['userMail' => $userMail]);

        if ($results != null) {
            $unreadNotifications = array();
            foreach ($results as $result) {
                $unreadNotifications[] = new NotificationEntity(
                    $result['id'], $result['isSeen'], $result['content'], $result['link']
                );
            }
        } else $unreadNotifications = null;

        return $unreadNotifications;
    }
}
