<?php
/**
 * UTHshop AI Chatbot - Powered by Google Gemini
 * Trợ lý ảo tư vấn sản phẩm áo thun sinh viên
 * Phiên bản nâng cao: Kết nối DB + Lưu lịch sử
 * 
 * File này HOÀN TOÀN ĐỘC LẬP - không ảnh hưởng đến code khác
 */

// Khởi động session an toàn
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chặn mọi output trước JSON
ob_start();

header('Content-Type: application/json; charset=utf-8');

// === KHỐI DATABASE ĐỘC LẬP ===
// Kết nối database riêng cho chatbot
try {
    if(!defined('PDO_CONNECTED')){
        require_once __DIR__ . "/model/connectdb.php";
    }
    
    // Include functions cần thiết
    if(!function_exists('getproduct_bestsell')){
        require_once __DIR__ . "/model/product.php";
    }
    if(!function_exists('getcatalog')){
        require_once __DIR__ . "/model/catalog.php";
    }
} catch (Exception $e) {
    // Nếu lỗi database, vẫn cho phép chatbot hoạt động
    error_log("Chatbot DB Error: " . $e->getMessage());
}

// Cấu hình
$GEMINI_API_KEY = "AIzaSyDZvtq_N1SIIhGUMtYhT8ItCsO509iOMuk"; // Thay bằng API key thật

// Kiểm tra request
if(!isset($_POST['message']) || empty($_POST['message'])){
    echo json_encode([
        'success' => false,
        'error' => 'Vui lòng nhập tin nhắn'
    ]);
    exit;
}

$user_message = trim($_POST['message']);

// === 1. KẾT NỐI DATABASE - Lấy thông tin sản phẩm thật ===
try {
    // Lấy top 5 sản phẩm bán chạy
    $products_bestsell = getproduct_bestsell(5);
    $product_info = "📦 SẢN PHẨM BÁN CHẠY:\n";
    foreach($products_bestsell as $p){
        $product_info .= "- {$p['name']}: " . number_format($p['price']) . "đ (ID: {$p['id']})\n";
    }
    
    // Lấy top 3 sản phẩm hot
    $products_hot = getproduct_hot(3);
    $product_info .= "\n🔥 SẢN PHẨM HOT:\n";
    foreach($products_hot as $p){
        $product_info .= "- {$p['name']}: " . number_format($p['price']) . "đ (ID: {$p['id']})\n";
    }
    
    // Lấy danh mục
    $catalogs = getcatalog();
    $catalog_info = "\n📂 DANH MỤC:\n";
    foreach($catalogs as $c){
        $catalog_info .= "- {$c['name']}\n";
    }
} catch (Exception $e) {
    $product_info = "Đang cập nhật sản phẩm...";
    $catalog_info = "";
}

// === 2. LƯU LỊCH SỬ CHAT - AI nhớ ngữ cảnh ===
if(!isset($_SESSION['chat_history'])){
    $_SESSION['chat_history'] = [];
}

// Giới hạn lịch sử 10 tin nhắn gần nhất (tránh quá dài)
if(count($_SESSION['chat_history']) > 10){
    $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -10);
}

// Tạo context từ lịch sử
$history_context = "";
if(count($_SESSION['chat_history']) > 0){
    $history_context = "\n\n--- LỊCH SỬ CHAT TRƯỚC ĐÓ ---\n";
    foreach($_SESSION['chat_history'] as $msg){
        $history_context .= "Khách: {$msg['user']}\nBot: {$msg['bot']}\n\n";
    }
    $history_context .= "--- KẾT THÚC LỊCH SỬ ---\n\n";
}

// === 3. PHÂN TÍCH CÂU HỎI - Xử lý đặc biệt ===
$special_response = null;

// Kiểm tra câu hỏi về giá
if(preg_match('/(giá|bao nhiêu|giá cả)/i', $user_message)){
    $special_response = "💰 **Bảng giá sản phẩm:**\n\n" . $product_info . "\n\nGiá đã bao gồm VAT. Miễn phí ship đơn từ 300k! 🚚";
}

// Kiểm tra xem danh mục
if(preg_match('/(danh mục|loại áo|có gì|sản phẩm nào)/i', $user_message)){
    $special_response = $catalog_info . "\n\nBạn muốn xem loại áo nào? Tôi sẽ tư vấn chi tiết! 😊";
}

// System prompt - Định hình tính cách AI
$system_context = "Bạn là trợ lý ảo thông minh của UTHshop - cửa hàng áo thun sinh viên Đại học Giao thông Vận tải TP.HCM.

THÔNG TIN SHOP:
- Tên: UTHshop (uthshop.online)
- Địa chỉ: 02 Võ Oanh, Thành Lộc, Mỹ Tây, TP.HCM
- Hotline: 0909 999 999
- Email: uthshop.group5@gmail.com

" . $product_info . $catalog_info . "

TÍNH NĂNG:
- Thiết kế áo theo yêu cầu (Design custom)
- Thanh toán: COD, chuyển khoản, ZaloPay, VNPAY
- Giao hàng: Toàn quốc, nhanh 2-3 ngày
- Miễn phí ship đơn từ 300k

NHIỆM VỤ:
1. Tư vấn sản phẩm thân thiện, nhiệt tình dựa trên THÔNG TIN THẬT từ database
2. Trả lời ngắn gọn (2-4 câu), dễ hiểu
3. Dùng emoji phù hợp 😊 nhưng không lạm dụng
4. Nhớ ngữ cảnh từ lịch sử chat trước
5. Nếu khách hỏi sản phẩm cụ thể, gợi ý ID để xem chi tiết
6. Khuyến khích khách xem sản phẩm và đặt hàng

PHONG CÁCH:
- Thân thiện, gần gũi như bạn bè
- Nhiệt tình, chuyên nghiệp
- Không dài dòng, đi thẳng vào vấn đề
- Luôn hỏi lại thông tin nếu chưa rõ (size, số lượng, màu...)

" . $history_context . "

Câu hỏi MỚI của khách: {$user_message}

Trả lời (2-4 câu ngắn gọn, dựa trên sản phẩm THẬT):";

// Nếu có câu trả lời đặc biệt, dùng luôn
if($special_response){
    echo json_encode([
        'success' => true,
        'reply' => $special_response,
        'timestamp' => time(),
        'source' => 'database'
    ]);
    
    // Lưu vào lịch sử
    $_SESSION['chat_history'][] = [
        'user' => $user_message,
        'bot' => $special_response,
        'time' => time()
    ];
    
    exit;
}

// Gọi Gemini API
function callGeminiAPI($prompt, $api_key) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $api_key;
    
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 200,
            "topP" => 0.8,
            "topK" => 10
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($http_code !== 200){
        return [
            'success' => false,
            'error' => 'Lỗi kết nối API (Code: ' . $http_code . ')'
        ];
    }
    
    return [
        'success' => true,
        'data' => json_decode($response, true)
    ];
}

// Xử lý response
$result = callGeminiAPI($system_context, $GEMINI_API_KEY);

if(!$result['success']){
    echo json_encode([
        'success' => false,
        'reply' => 'Xin lỗi, tôi đang gặp chút vấn đề kết nối AI. Bạn vui lòng thử lại sau nhé! 😊',
        'error_detail' => $result['error']
    ]);
    exit;
}

// Kiểm tra response có đúng format không
if(!isset($result['data']['candidates'][0]['content']['parts'][0]['text'])){
    echo json_encode([
        'success' => false,
        'reply' => 'AI đang quá tải, bạn thử lại sau vài giây nhé! 🙏',
        'debug' => $result['data']
    ]);
    exit;
}

// Lấy câu trả lời từ AI
$ai_response = $result['data']['candidates'][0]['content']['parts'][0]['text'];

// === 4. LƯU VÀO LỊCH SỬ ===
$_SESSION['chat_history'][] = [
    'user' => $user_message,
    'bot' => trim($ai_response),
    'time' => time()
];

// Trả về JSON
echo json_encode([
    'success' => true,
    'reply' => trim($ai_response),
    'timestamp' => time(),
    'source' => 'ai',
    'history_count' => count($_SESSION['chat_history'])
]);
?>
