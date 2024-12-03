<?php

namespace app\controllers;

use app\core\AuthHelper;
use app\models\User;

class UserController extends Controller {
    
    public function usersView() {
        AuthHelper::authRoute();
        $this->returnView('./assets/views/users/usersView.html');
    }

    public function loginView(){
        AuthHelper::nonAuthRoute();
        $this->returnView('./assets/views/users/users-update.html');
    }

    public function registerView(){
        AuthHelper::nonAuthRoute();
        $this->returnView('./assets/views/users/users-add.html');
    }

    public function getUsers() {
        $userModel = new User();
        header("Content-Type: application/json");
        $users = $userModel->getAllUsers();

        $this->returnJSON($users);
        exit();
    }

    public function login() {
        $inputData = [
            'email' => $_POST['email'] ? $_POST['email'] : false,
            'password' => $_POST['password'] ? $_POST['password'] : false,
        ];

        $user = new User();
        $authedUser = $user->login(
            [
                'email' => $inputData['email'],
                'password' => $inputData['password'],
            ]
        );

        AuthHelper::startSession($authedUser);
        
        http_response_code(200);
        $this->returnJSON([
            'route' => '/'
        ]);

    }

    public function logout() {
        AuthHelper::endSession();
        http_response_code(200);
        $this->returnJSON([
            'route' => '/ '
        ]);
    }

    public function validateUser($inputData){
       $errors = [];
       $firstName = $inputData['firstName'];
       $lastName = $inputData['lastName'];
       $email= $inputData['email'];
       $password= $inputData['password'];

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
          'email' => $email,
          'password' => $password,
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

    public function register() {
        
        $inputData = [
            'firstName' => $_POST['firstName'] ? $_POST['firstName'] : false,
            'lastName' => $_POST['lastName'] ? $_POST['lastName'] : false,
            'email' => $_POST['email'] ? $_POST['email'] : false,
            'password' => $_POST['password'] ? $_POST['password'] : false,
        ];

        $userData = $this->validateUser($inputData);

        $user = new User();

        // Attempt to save the user
        
        $user->saveUser(
            [
                'firstName' => $inputData['firstName'],
                'lastName' => $inputData['lastName'],
                'email' => $inputData['email'],
                'password' => $inputData['password'],
            ]
        );

        http_response_code(200);
        $this->returnJSON([
            'route' => '/login'
        ]);
    }

}