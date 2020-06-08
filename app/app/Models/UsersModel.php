<?php namespace App\Models;
use CodeIgniter\Model;


class UsersModel extends Model {
    protected $table = 'users';
    protected $allowedFields = ['mail', 'name', 'firstName', 'birthIsoDate', 'pwd', 'isDeleted'];

    /*
        Information : we only store the user's password's sha512 hash for security reasons.
                        Always compare the db 'pwd' value to the password's sha512 hash.
    */

    public function getUser($mail) {
        return $this->asArray()
            ->where(['mail' => $mail])
            ->first();
    }
}