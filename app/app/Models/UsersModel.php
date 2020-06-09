<?php namespace App\Models;
use CodeIgniter\Model;

use App\Classes\UserEntity;


class UsersModel extends Model {
    protected $table = 'users';
    protected $allowedFields = ['mail', 'name', 'firstName', 'birthIsoDate', 'pwd', 'isDeleted'];

    /*
        Information : we only store the user's password's sha512 hash for security reasons.
                        Always compare the db 'pwd' value to the password's sha512 hash.
    */

    public function getUser($mail) {
        $userData = $this->asArray()
            ->where(['mail' => $mail])
            ->first();
        if ($userData != null) {
            $userData = new UserEntity(
                $userData['mail'],
                $userData['name'],
                $userData['firstName'],
                $userData['birthIsoDate'],
                $userData['pwd'],
                $userData['isDeleted']
            );
        }
        return $userData;
    }

    public function saveEntity($user) {
        $this->save($user->toArray());
    }
}