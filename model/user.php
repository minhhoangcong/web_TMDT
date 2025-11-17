<?php




  function getlogin($user,$pass){
    // CHỈ CHO PHÉP USER ĐANG HOẠT ĐỘNG (kichhoat=1) đăng nhập
    // Hỗ trợ cả plaintext và MD5
    $sql="SELECT * FROM users WHERE user=? AND kichhoat=1";
    $row = pdo_query_one($sql, $user);
    
    if (!$row || !is_array($row)) return false;
    
    $stored = $row['pass'];
    
    // Kiểm tra plaintext
    if ($pass === $stored) return $row;
    
    // Kiểm tra MD5
    if (md5($pass) === $stored) return $row;
    
    // Không khớp
    return false;
   }

   // ============ HÀM MỚI: ĐĂNG NHẬP BẢO MẬT ============
   // Hỗ trợ cả password cũ (plain/MD5/SHA1/custom) và password mới (Bcrypt)
   // Tự động upgrade password cũ → mới khi user đăng nhập thành công
   function getlogin_secure($user, $pass){
      // Bước 1: Lấy thông tin user từ DB (chỉ cần username)
      $sql = "SELECT * FROM users WHERE user=? AND kichhoat=1";
      $row = pdo_query_one($sql, $user);
      
      if (!$row || !is_array($row)) return false; // User không tồn tại
      
      // Bước 2: Kiểm tra password
      $stored_pass = $row['pass'];
      
      // Kiểm tra xem đây là password đã hash (Bcrypt) hay chưa
      $info = password_get_info($stored_pass);
      
      if ($info['algo'] !== 0) {
          // Đây là password đã hash bằng Bcrypt → Dùng password_verify
          if (password_verify($pass, $stored_pass)) {
              return $row; // ✅ Đăng nhập thành công
          }
      } else {
          // Đây là password cũ - Thử TẤT CẢ các phương pháp có thể
          $password_match = false;
          
          // 1. So sánh trực tiếp (Plain text)
          if ($pass === $stored_pass) {
              $password_match = true;
          }
          
          // 2. So sánh MD5
          if (!$password_match && md5($pass) === $stored_pass) {
              $password_match = true;
          }
          
          // 3. So sánh SHA1 (40 ký tự)
          if (!$password_match && sha1($pass) === $stored_pass) {
              $password_match = true;
          }
          
          // 4. So sánh SHA1 + MD5 kết hợp
          if (!$password_match && sha1(md5($pass)) === $stored_pass) {
              $password_match = true;
          }
          
          // 5. So sánh MD5 + SHA1 kết hợp
          if (!$password_match && md5(sha1($pass)) === $stored_pass) {
              $password_match = true;
          }
          
          // 6. Thử crypt() với salt từ stored password
          if (!$password_match && strlen($stored_pass) >= 13) {
              $salt = substr($stored_pass, 0, 2);
              if (crypt($pass, $salt) === $stored_pass) {
                  $password_match = true;
              }
          }
          
          if ($password_match) {
              // ✅ Đăng nhập thành công với password cũ
              // → Tự động UPGRADE sang Bcrypt hash
              $new_hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
              $update_sql = "UPDATE users SET pass=? WHERE id=?";
              pdo_execute($update_sql, $new_hash, $row['id']);
              
              return $row;
          }
      }
      
      return false; // ❌ Sai mật khẩu
   }

   function getrole($user,$pass){
    $row = getlogin($user, $pass);
    if (is_array($row)) return isset($row['role']) ? intval($row['role']) : -1;
    return -1;
   }

   // ============ HÀM MỚI: LẤY ROLE AN TOÀN ============
   function getrole_secure($user, $pass){
    $row = getlogin_secure($user, $pass);
    if (is_array($row)) return isset($row['role']) ? intval($row['role']) : -1;
    return -1;
   }

   function getidusercu($user,$pass){
    $row = getlogin($user, $pass);
    if(is_array($row)){
      return isset($row['id']) ? intval($row['id']) : -1;
    }else
      return -1;
    }

   // ============ HÀM MỚI: LẤY ID USER AN TOÀN ============
   function getidusercu_secure($user, $pass){
    $row = getlogin_secure($user, $pass);
    if(is_array($row)){
      return isset($row['id']) ? intval($row['id']) : -1;
    }else
      return -1;
    }






//-----------------Đơn hàng------------------
function creatpass() {
   $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{};:,.<>/?`~';
   $password = '';
   for ($i = 0; $i < 8; $i++) {
     $password .= $characters[mt_rand(0, strlen($characters) - 1)];
   }
   return $password;
 }
 function creatusername($name) {
   $username='user_';
   $characters = '0123456789';
   for ($i = 0; $i < 6; $i++) {
     $username .= $characters[mt_rand(0, strlen($characters) - 1)];
   }
   return $username;
 }
function creatuser($user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat){
  // Tự động hash MD5 nếu password chưa được hash (độ dài != 32)
  if (strlen($pass) !== 32) {
    $pass = md5($pass);
  }
  $sql = "INSERT INTO users (user,pass, name,email,sdt,gioitinh,ngaysinh,diachi,role,img,kichhoat)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)";
  pdo_execute($sql, $user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat);
}

// ============ HÀM MỚI: TẠO USER BẢO MẬT ============
// Luôn hash password bằng Bcrypt trước khi lưu vào DB
function creatuser_secure($user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat){
  // Hash password với Bcrypt (cost=12 → mạnh hơn mặc định)
  $hashed_pass = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
  
  $sql = "INSERT INTO users (user,pass, name,email,sdt,gioitinh,ngaysinh,diachi,role,img,kichhoat)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)";
  pdo_execute($sql, $user, $hashed_pass, $name, $email, $sdt, $gioitinh, $ngaysinh, $diachi, $role, $img, $kichhoat);
}function update_user($id,$user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat){
  // Nếu có password mới và chưa hash (độ dài != 32) → Hash MD5
  if (!empty($pass) && strlen($pass) !== 32) {
    $pass = md5($pass);
  }
  $sql = "UPDATE users SET user=?,pass=?,name=?,email=?,sdt=?,gioitinh=?, ngaysinh=?, diachi=?, role=?, img=?, kichhoat=? WHERE id=?";
  pdo_execute($sql, $user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat,$id);
}

// ============ HÀM MỚI: CẬP NHẬT USER BẢO MẬT ============
// Hash password nếu có thay đổi, giữ nguyên nếu không đổi
function update_user_secure($id,$user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat){
  // Lấy password hiện tại từ DB
  $current_user = getuser($id);
  
  // Nếu password mới khác password cũ → Hash lại
  if ($pass !== $current_user['pass']) {
      // Kiểm tra xem password mới đã là hash chưa
      if (password_get_info($pass)['algo'] === 0) {
          // Chưa hash → Hash nó
          $pass = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
      }
      // Nếu đã là hash → Giữ nguyên (trường hợp admin cập nhật thông tin khác)
  }
  
  $sql = "UPDATE users SET user=?,pass=?,name=?,email=?,sdt=?,gioitinh=?, ngaysinh=?, diachi=?, role=?, img=?, kichhoat=? WHERE id=?";
  pdo_execute($sql, $user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat,$id);
}
function changepassword($email, $password){
  // Tự động hash MD5 nếu password chưa hash (độ dài != 32)
  if (strlen($password) !== 32) {
    $password = md5($password);
  }
  $sql = "UPDATE users SET pass=? WHERE email=?";
  pdo_execute($sql, $password, $email);
}

// ============ HÀM MỚI: ĐỔI MẬT KHẨU BẢO MẬT ============
// Hash password mới trước khi cập nhật
function changepassword_secure($email, $new_password){
  $hashed = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
  $sql = "UPDATE users SET pass=? WHERE email=?";
  pdo_execute($sql, $hashed, $email);
}
function deluser($id){
   // ẨN USER thay vì XÓA (Soft Delete - Chuyên nghiệp hơn)
   // Lý do:
   // - Bảo toàn lịch sử đơn hàng (quan trọng cho báo cáo doanh thu)
   // - Tuân thủ pháp luật (GDPR, luật thuế VN yêu cầu lưu 7-10 năm)
   // - Có thể khôi phục nếu xóa nhầm
   // - Bình luận/đánh giá sản phẩm không bị mất
   
   $sql = "UPDATE users SET kichhoat=0 WHERE id=?";
   if(is_array($id)){
       foreach ($id as $ma) {
           pdo_execute($sql, $ma);
       }
   }
   else{
       pdo_execute($sql, $id);
   }
}

// Hàm XÓA THẬT SỰ (chỉ dùng khi cần thiết)
function deluser_permanent($id){
   // Xóa theo thứ tự: Con trước -> Cha sau (để tránh lỗi Foreign Key)
   
   if(is_array($id)){
       foreach ($id as $ma) {
           deluser_single($ma);
       }
   }
   else{
       deluser_single($id);
   }
}

function deluser_single($id){
   try {
       $conn = pdo_get_connection();
       $conn->beginTransaction();
       
       // Bước 1: Xóa dữ liệu liên quan (CON)
       
       // 1.1. Xóa giỏ hàng của user
       $sql_cart = "DELETE FROM cart WHERE id_user=?";
       $stmt = $conn->prepare($sql_cart);
       $stmt->execute([$id]);
       
       // 1.2. Xóa bình luận của user
       $sql_comment = "DELETE FROM comment WHERE id_user=?";
       $stmt = $conn->prepare($sql_comment);
       $stmt->execute([$id]);
       
       // 1.3. Xóa voucher đã dùng của user
       $sql_voucher = "DELETE FROM dadung_voucher WHERE id_user=?";
       $stmt = $conn->prepare($sql_voucher);
       $stmt->execute([$id]);
       
       // 1.4. Xóa thiết kế áo của user
       $sql_design = "DELETE FROM design WHERE id_user=?";
       $stmt = $conn->prepare($sql_design);
       $stmt->execute([$id]);
       
       // 1.5. Xóa hình ảnh thiết kế của user
       $sql_img_design = "DELETE FROM img_product_design WHERE id_user=?";
       $stmt = $conn->prepare($sql_img_design);
       $stmt->execute([$id]);
       
       // 1.6. Xóa wishlist của user
       $sql_wishlist = "DELETE FROM wishlist WHERE user_id=?";
       $stmt = $conn->prepare($sql_wishlist);
       $stmt->execute([$id]);
       
       // 1.7. Xóa đơn hàng của user
       $sql_donhang = "DELETE FROM donhang WHERE iduser=?";
       $stmt = $conn->prepare($sql_donhang);
       $stmt->execute([$id]);
       
       // Bước 2: Xóa user (CHA)
       $sql_user = "DELETE FROM users WHERE id=?";
       $stmt = $conn->prepare($sql_user);
       $stmt->execute([$id]);
       
       $conn->commit();
       
   } catch (Exception $e) {
       $conn->rollBack();
       throw $e;
   } finally {
       unset($conn);
   }
}

function getuser($id){
   $sql="SELECT * FROM users WHERE id=?";
   return pdo_query_one($sql, $id);
}
function getusertoemail($email){
  $sql="SELECT * FROM users WHERE email=?";
  return pdo_query_one($sql, $email);
}
function getusertable($limit=100000, $show_deleted=true){
   // Tham số $show_deleted:
   // - true: Hiển thị TẤT CẢ user (kể cả đã ẩn) - MẶC ĐỊNH
   // - false: Chỉ hiển thị user đang hoạt động (kichhoat=1)
   
   if($show_deleted){
       $sql="SELECT * FROM users ORDER BY id DESC limit ".$limit;
   }else{
       $sql="SELECT * FROM users WHERE kichhoat=1 ORDER BY id DESC limit ".$limit;
   }
   return pdo_query($sql);
}
function isValidPhoneNumber($phoneNumber) {
  // Loại bỏ các ký tự không phải số từ chuỗi
  $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

  // Kiểm tra xem chuỗi còn lại có 10 hoặc 11 chữ số không
  if (strlen($phoneNumber) === 10 || strlen($phoneNumber) === 11) {
      // Kiểm tra xem nếu là 11 chữ số, thì chữ số đầu tiên phải là 0
      if (strlen($phoneNumber) === 11 && $phoneNumber[0] !== '0') {
          return false;
      }

      // Số điện thoại hợp lệ
      return true;
  }

  // Số điện thoại không hợp lệ
  return false;
}

function searchuser($kw=''){
  $sql="SELECT * FROM users WHERE  name LIKE ? or email LIKE ? or user LIKE ?";
  return pdo_query($sql,'%'.$kw.'%', '%'.$kw.'%', '%'.$kw.'%');
 }

// Hàm khôi phục user đã bị ẩn
function restore_user($id){
   $sql = "UPDATE users SET kichhoat=1 WHERE id=?";
   if(is_array($id)){
       foreach ($id as $ma) {
           pdo_execute($sql, $ma);
       }
   }
   else{
       pdo_execute($sql, $id);
   }
}

// Lấy danh sách user đã bị ẩn (để khôi phục)
function get_deleted_users(){
   $sql = "SELECT * FROM users WHERE kichhoat=0 ORDER BY id DESC";
   return pdo_query($sql);
}
?>