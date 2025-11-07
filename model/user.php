<?php




  function getlogin($user,$pass){
    // CHỈ CHO PHÉP USER ĐANG HOẠT ĐỘNG (kichhoat=1) đăng nhập
    $sql="SELECT * FROM users WHERE user=? AND pass=? AND kichhoat=1";
    return pdo_query_one($sql, $user, $pass);
   }

   function getrole($user,$pass){
    $row = getlogin($user, $pass);
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
  $sql = "INSERT INTO users (user,pass, name,email,sdt,gioitinh,ngaysinh,diachi,role,img,kichhoat)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)";      
  pdo_execute($sql, $user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat);
}

function update_user($id,$user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat){
  $sql = "UPDATE users SET user=?,pass=?,name=?,email=?,sdt=?,gioitinh=?, ngaysinh=?, diachi=?, role=?, img=?, kichhoat=? WHERE id=?";
  pdo_execute($sql, $user,$pass, $name,$email,$sdt,$gioitinh,$ngaysinh,$diachi,$role,$img,$kichhoat,$id);
}
function changepassword($email, $password){
  $sql = "UPDATE users SET pass=? WHERE email=?";
  pdo_execute($sql, $password, $email);
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