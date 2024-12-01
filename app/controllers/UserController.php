<?php

namespace app\controllers;
use app\models\User;

class UserController extends Controller {
    public function getUsers() {
        $userModel = new User();
        header("Content-Type: application/json");
        $users = $userModel->getAllUsers();

        $this->returnJSON($users);
        exit();
    }

    public function validateUser($inputData){
       $errors = [];
       $firstName = $inputData['firstName'];
       $lastName = $inputData['lastName'];

       if($firstName){
          $firstName = htmlspecialchars($firstName, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
          if(strlen($firstName) < 2) {
             $errors['firstNameShort'] = 'First name is  too short.';
          }
       } else {
           $errors['requiredFirstName'] = 'First name is required.';
       }

       if ($lastName) {
        $lastName = htmlspecialchars($lastName, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
        if (strlen($lastName) < 2) {
            $errors['lastNameShort'] = 'last name is too short';
        }
       } else {
        $errors['requiredLastName'] = 'last name is required';
       }
       

       if(count($errors)){
          http_response_code(400);
          echo json_encode($errors);
          exit();
       }

       return [
          'firstName' => $firstName,
          'lastName' => $lastName,
       ];
    }

    public function getUserByID($id){
        if(!$id) {
            http_response_code(404);
            echo json_encode(['error'=> 'User ID is required.']);
            exit();
        }

        $userModel = new User();
        header("Content-Type:application/json");
        $user = $userModel->getUserById($id);

        if(!$user){
            http_response_code(404);
            echo json_encode(['error' => 'User not found.']);
            exit();
        }

        echo json_encode($user);
        exit();
    }

    public function saveUser() {
       $inputData = json_decode(file_get_contents('php://input'), true);
       $userData = $this->validateUser($inputData);

       $userModel = new User();
       $result = $userModel->createUser([
           'name' => $userData['firstName'] . ' ' . $userData['lastName'],
           'email' => $_POST['email'] ?? '',
           'password' => $_POST['password'] ?? '',
       ]);

       if($result) {
          http_response_code(200);
          echo json_encode(['success' => true,'message' => 'User created.']);
       }else {
          http_response_code(500);
          echo json_encode(['error' => 'Failed to create.']);
       }

       exit();
    }

    public function updateUser($id){
        if (!$id) {
            http_response_code(404);
            echo json_encode(['error' => 'User ID is required.']);
            exit();
        }

        parse_str(file_get_contents('php://input'), $_PUT);

        $inputData = [
            'firstName' => $_PUT['firstName'] ?? false,
            'lastName' => $_PUT['lastName'] ?? false,
        ];

        $userData = $this->validateUser($inputData);

        $userModel = new User();
        $result = $userModel->updateUser($id, [
            'name' => $userData['firstName'] . ' ' . $userData['lastName'],
            'email' => $_PUT['email'] ?? '',
        ]);

        if($result) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'User updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user.']);
        }

        exit();
    }

    public function usersView() {
        $userModel = new User();
        $user = $userModel->getUserById($userId);
        $this->returnView('../assets/views/users/usersView.html');
    }

    public function usersAddView() {
        include '../public/assets/views/users/users-add.html';
        exit();
    }


    /**
     * Render the update user view page.
     */
    public function usersUpdateView() {
        include '../public/assets/views/users/users-update.html';
        exit();
    }
}