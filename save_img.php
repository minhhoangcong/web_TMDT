<?php
session_start();
ob_start();

header('Content-Type: application/json; charset=utf-8');

function generateRandomNumber() {
    $randomNumber = '';
    for ($i = 0; $i < 8; $i++) {
        $randomNumber .= rand(0, 9);
    }
    return $randomNumber;
}

function ensureUploadDir($dir)
{
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            return [false, 'Không thể tạo thư mục upload'];
        }
    }
    if (!is_writable($dir)) {
        return [false, 'Thư mục upload không có quyền ghi'];
    }
    return [true, null];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

if (!isset($_POST['imageData'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu ảnh']);
    exit;
}

$imageData = $_POST['imageData'];

// Bảo đảm thư mục upload tồn tại và ghi được
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'upload';
[$ok, $err] = ensureUploadDir($uploadDir);
if (!$ok) {
    error_log('[save_img] ' . $err);
    echo json_encode(['success' => false, 'message' => $err]);
    exit;
}

$name_img = 'AT_DESIGN_' . generateRandomNumber() . '.png';

// Xác định ảnh mặt trước/mặt sau theo session
if (!isset($_SESSION['mat'])) {
    $_SESSION['mat'] = 1;
}
if ($_SESSION['mat'] == 1) {
    $_SESSION['img_front'] = $name_img;
    $_SESSION['mat'] = 2;
} elseif ($_SESSION['mat'] == 2) {
    $_SESSION['img_back'] = $name_img;
    unset($_SESSION['mat']);
}

// Chuyển đổi base64 thành binary và lưu file
$outputFile = $uploadDir . DIRECTORY_SEPARATOR . $name_img;
$imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);
$imageData = base64_decode($imageData, true);
if ($imageData === false) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu ảnh không hợp lệ']);
    exit;
}

// Enforce max size (e.g., 5MB)
if (strlen($imageData) > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Ảnh quá lớn (tối đa 5MB)']);
    exit;
}

// MIME validation
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->buffer($imageData);
if ($mime !== 'image/png' && $mime !== 'image/jpeg') {
    echo json_encode(['success' => false, 'message' => 'Định dạng ảnh không được hỗ trợ']);
    exit;
}
// If JPEG, adjust extension/name
if ($mime === 'image/jpeg') {
    $name_img = 'AT_DESIGN_' . generateRandomNumber() . '.jpg';
    $outputFile = $uploadDir . DIRECTORY_SEPARATOR . $name_img;
}

$bytes = @file_put_contents($outputFile, $imageData);
if ($bytes === false) {
    error_log('[save_img] Lưu ảnh thất bại: ' . $outputFile);
    echo json_encode(['success' => false, 'message' => 'Không thể lưu ảnh lên máy chủ']);
    exit;
}

// Trả về đường dẫn tương đối dùng hiển thị (so với web root)
$relative = 'upload/' . $name_img;
error_log('[save_img] Image saved: ' . $relative);
echo json_encode(['success' => true, 'filename' => $name_img, 'path' => $relative]);
?>
