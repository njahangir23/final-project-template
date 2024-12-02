<?php

namespace app\models;

class User extends Model {

    protected $table = 'users';

    public function getAllUsers() {
        return $this->findAll();
    }

    /**
     * Find a user by ID.
     */
    public function getUserById($id) {
        $query = "select * from users where id = :id";
        return $this->query($query, ['id' => $id]);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        return $this->query($query, ['email' => $email]);
    }

    /**
     * Create a new user in the database.
     */
    public function createUser($inputData) {
        $inputData['password'] = password_hash($inputData['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password);";
        return $this->query($query, $inputData);
    }

    /**
     * Update user information by ID.
     */
    public function updateUser($id, $data) {
        $query = "UPDATE $this->table SET name = :name, email = :email WHERE id = :id";
        return $this->query($query, [
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    public function login($inputData) {
        $query = "SELECT id, firstName, lastName, email, password 
                  FROM users 
                 WHERE email = :email;";                         // SQL to get member data
        $member = $this->query($query, ['email' => $inputData['email']]);    // Run SQL
        if (!$member) {                                          // If no member found
            return false;                                        // Return false
        }

        $authenticated = password_verify($inputData['password'], $member[0]['password']); // Passwords match?
        return ($authenticated ? $member[0] : false);
    }

}