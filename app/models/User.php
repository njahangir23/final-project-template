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
    public function createUser($data) {
        $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        return $this->query($query, [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
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

    /**
     * Delete a user by ID.
     */
    public function deleteUser($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        return $this->query($query, ['id' => $id]);
    }

    /**
     * Authenticate a user by email and password.
     */
    public function authenticateUser($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user[0]->password)) {
            return $user[0];
        }
        return false;
    }

}