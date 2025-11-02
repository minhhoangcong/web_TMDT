<?php
    define('PATH_IMG','upload/');
    define('PATH_IMG_ADMIN','../../upload/');

    // CSRF utilities
    if (!function_exists('csrf_get_token')) {
        function csrf_get_token(){
            if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
            if (!isset($_SESSION['csrf_token'])) {
                try {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } catch (Exception $e) {
                    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
                }
            }
            return $_SESSION['csrf_token'];
        }
    }
    if (!function_exists('csrf_validate')) {
        function csrf_validate($token){
            if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
            if (!isset($_SESSION['csrf_token'])) return false;
            if (!is_string($token) || $token === '') return false;
            return hash_equals($_SESSION['csrf_token'], $token);
        }
    }
?>