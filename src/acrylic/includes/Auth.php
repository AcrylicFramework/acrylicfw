<?php

namespace App\Includes;

use App\Includes\Session;

class Auth {
    private static $sessionKey = 'auth';
    private static $users = [
        ['id' => 1, 'username' => 'user1', 'password' => 'password1'],
        ['id' => 2, 'username' => 'user2', 'password' => 'password2'],
    ];

    public static function attempt($username, $password) {
        foreach (self::$users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                Session::start();
                Session::set(self::$sessionKey, $user['id']);
                return true;
            }
        }
        return false;
    }

    public static function user() {
        Session::start();
        $userId = Session::get(self::$sessionKey);

        if ($userId) {
            foreach (self::$users as $user) {
                if ($user['id'] === $userId) {
                    return $user;
                }
            }
        }
        return null;
    }

    public static function check() {
        return self::user() !== null;
    }

    public static function logout() {
        Session::start();
        Session::delete(self::$sessionKey);
    }
}