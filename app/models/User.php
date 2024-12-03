<?php

namespace app\models;

class User extends Model {

    protected $table = 'users';

    public function updateUserSessionExp($inputData){
        $query = "update users set sessionExpiration = :sessionExpiration where id = :id";
        return $this->query($query, $inputData);
    }

    public function getAllUsers() {
        return $this->findAll();
    }

    /**
     * Find a user by ID.
     */
    public function getUserByID($id) {
        $query = "select id, firstName, lastName, email, sessionExpiration
                  from users 
                  where id = :id;";
        $user = $this->query($query, ['id' => $id]);    // Run SQL
        if (!$user) {                                          // If no member found
            return false;                                        // Return false
        }
        return $user[0];
    }

    /**
     * Create a new user in the database.
     */
    public function saveUser($inputData) {
        $inputData['password'] = password_hash($inputData['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password);";

        return $this->query($query, $inputData);
    }


    public function login($inputData) {
        $query = "SELECT id, firstName, lastName, email, password 
                  FROM users 
                 WHERE email = :email;";                         // SQL to get member data
        $member = $this->query($query, ['email' => $inputData['email']]);    // Run SQL
        if (!$member) {                                          // If no member found
            return false;                                        // Return false
        }

        $password = is_object($member[0]) ? $member[0]->password : $member[0]['password'];

        $authenticated = password_verify($inputData['password'], $member[0]['password']); // Passwords match?
        return ($authenticated ? $member[0] : false);
    }

}