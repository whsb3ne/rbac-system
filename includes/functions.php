<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function esc($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function require_login(){
    if (!isset($_SESSION['user_id'])){
        header('Location: index.php');
        exit();
    }
}

function require_admin(){
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin'){
        header('Location: index.php');
        exit();
    }
}
?>