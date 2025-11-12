<?php
/**
 * Reset chat history - Xóa lịch sử chat
 */
session_start();

// Xóa lịch sử chat
if(isset($_SESSION['chat_history'])){
    unset($_SESSION['chat_history']);
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Đã xóa lịch sử chat'
]);
?>
