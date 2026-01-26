<?php

namespace services;

use DateTime;
use db\models\User;
use db\repository\SessionRepository;
use db\repository\UserRepository;
use utility\FormErrors;

class AuthService {
    private SessionRepository $sessionRepo;
    private UserRepository $userRepo;
    private User $currentUser;

    public function __construct(SessionRepository $sessionRepo, UserRepository $userRepo) {
        $this->sessionRepo = $sessionRepo;
        $this->userRepo = $userRepo;
    }

    public function guard() {
        $sessionToken = $_SESSION['auth_token'] ?? '';
        $existingSession = $this->sessionRepo->findByToken($sessionToken);
        $isTokenExpired = new DateTime($existingSession->expires_at) < new DateTime();
        if(!$existingSession || $isTokenExpired) {
            unset($_SESSION['auth_token']);
            header('Location: /login.php');
            exit;
        }
        $_SESSION['user_id'] = $existingSession->user_id;
    }

    public function validateLoginInfo(string $username, string $password): FormErrors {
        $loginErrors = new FormErrors();
        
        // Super basic form validation
        if(strlen($username) === 0) {
            $loginErrors->username = 'Username is required';
            $loginErrors->hasErrors = true;
        }
        if(strlen($password) === 0) {
            $loginErrors->password = 'Password is required';
            $loginErrors->hasErrors = true;
        }

        if($loginErrors->hasErrors) {
            return $loginErrors;
        }

        // DB validation
        $existingUser = $this->userRepo->findByUsername($username);
        if($existingUser === null) {
            $loginErrors->username = 'User does not exist';
            $loginErrors->hasErrors = true;
        }
        else if(!password_verify($password, $existingUser->password)) {
            $loginErrors->username = 'Incorrect password';
            $loginErrors->hasErrors = true;
        } else {
            $this->currentUser = $existingUser;
        }

        return $loginErrors;
    }

    public function validateRegisterInfo(string $username, string $password, string $passwordConfirmation): FormErrors {
        $registerErrors = new FormErrors();
        
        // Super basic form validation
        if(strlen($username) === 0) {
            $registerErrors->username = 'Username is required';
            $registerErrors->hasErrors = true;
        }
        if($passwordConfirmation !== $password) {
            $registerErrors->password = 'Passwords do not match';
            $registerErrors->passwordConfirmation = 'Passwords do not match';
            $registerErrors->hasErrors = true;
        }
        if(strlen($password) === 0) {
            $registerErrors->password = 'Password is required';
            $registerErrors->hasErrors = true;
        }
        if(strlen($passwordConfirmation) === 0) {
            $registerErrors->passwordConfirmation = 'Please confirm your password';
            $registerErrors->hasErrors = true;
        }

        if($registerErrors->hasErrors) {
            return $registerErrors;
        }
        
        // DB validation
        $existingUser = $this->userRepo->findByUsername($username);
        if($existingUser !== null) {
            $registerErrors->username = 'User already exists';
            $registerErrors->hasErrors = true;
        }

        return $registerErrors;
    }
    
    public function loginUser() {
        $newSession = $this->sessionRepo->createSessionForUser($this->currentUser);
        $_SESSION['auth_token'] = $newSession->token;
    }
    
    public function registerUser(string $username, string $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = new User($username, $hashedPassword);
        
        $user = $this->userRepo->create($user);
        $this->currentUser = $user;
        
        $newSession = $this->sessionRepo->createSessionForUser($user);
        $_SESSION['auth_token'] = $newSession->token;
    }

    public function logoutUser() {
        if(!empty($_SESSION['auth_token'])) {
            $this->sessionRepo->deleteSessionForUser($_SESSION['auth_token']);
        }
    }
}