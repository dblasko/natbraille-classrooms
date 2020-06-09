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

    public function createNotification($timestamp, $content, $userMail, $link) {
        $data = [
            'isoData' => $timestamp,
            'content' => $content,
            'isSeen' => 0,
            'userMail' => $userMail,
            'link' => $link,
        ];
        $this->save($data);
    }

    public function getUnreadNotifications($userMail) {
        $query = $this->db->query("SELECT * FROM notifications WHERE userMail = ? AND isSeen = 0", array($userMail));

        if ($query != null) {
            $unreadNotifications = array();
            foreach ($query->getResultArray() as $result) {
                $unreadNotifications[] = new NotificationEntity(
                    $result['id'], $result['isSeen'], $result['content'], $result['link']
                );
            }
        } else $unreadNotifications = null;

        return $unreadNotifications;
    }

    public function getById($id) {
        $data = $this->asArray()
            ->where(['id' => $id])
            ->first();
        if ($data != null) {
            $notification = new NotificationEntity(
                $data['id'], $data['isSeen'], $data['content'], $data['link']
            );
        }
        return isset($notification)? $notification : null;
    }
}
