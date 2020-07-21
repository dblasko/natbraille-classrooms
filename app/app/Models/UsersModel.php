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

    public function getUserByNameInPromotion($firstName, $lastName, $promotionId) {
        /*
            Given name + firstName + promotionID, return the user of the promotion with that name (or null)
        */
        $promoModel = new PromotionModel();

        //$userData = $this->asArray()
        //    ->where(['firstName' => $firstName, 'name' => $lastName]);
        $userData = $this->db->query("SELECT * FROM users WHERE name = ? AND firstName = ?", array($lastName, $firstName))->getResultArray();
        if ($userData != null) {
            foreach ($userData as $user) {
                // check si mail dans la promo, si oui retourner son entité
                if ($promoModel->isUserMemberOfPromo($user['mail'], $promotionId)) {
                    return new UserEntity(
                        $user['mail'],
                        $user['name'],
                        $user['firstName'],
                        $user['birthIsoDate'],
                        $user['pwd'],
                        $user['isDeleted']
                    );
                }
            }
        }
        return null;
    }

    public function saveEntity($user) {
        $this->save($user->toArray());
    }
}