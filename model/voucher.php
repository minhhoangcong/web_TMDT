<?php
     function get_voucher(){
        $sql="SELECT * FROM voucher ORDER BY id";
        return pdo_query($sql);
     }

     function get_detail_voucher($id){
        $sql="SELECT * FROM voucher WHERE id=?";
        return pdo_query_one($sql, $id);
   }

     function get_idvoucher($id){
        $sql="SELECT * FROM voucher WHERE id=?";
        return pdo_query_one($sql, $id);
     }
     function create_voucher($id, $ma_voucher, $giamgia, $ngaybatdau, $ngayketthuc, $dieukien){
      $sql="INSERT INTO voucher(ma_voucher, giamgia, ngaybatdau, ngayketthuc, dieukien, id) VALUES (?,?,?,?,?,?)";      
      pdo_execute($sql,$ma_voucher, $giamgia, $ngaybatdau, $ngayketthuc, $dieukien, $id);
   }
  
  function update_voucher($id,$ma_voucher, $giamgia, $ngaybatdau, $ngayketthuc, $dieukien){
      $sql = "UPDATE voucher SET ma_voucher=?,giamgia=?,ngaybatdau=?,ngayketthuc=?,dieukien=? WHERE id=?";
      pdo_execute($sql, $ma_voucher, $giamgia, $ngaybatdau, $ngayketthuc, $dieukien, $id);
    }
  
  function del_voucher($id){
      // XÓA AN TOÀN: Xóa dữ liệu liên quan trước (CON → CHA)
      
      // Bước 1: Set NULL cho id_voucher trong donhang (không xóa đơn hàng)
      $sql_donhang = "UPDATE donhang SET id_voucher=NULL WHERE id_voucher=?";
      
      // Bước 2: Xóa dadung_voucher (lịch sử dùng voucher)
      $sql_dadung = "DELETE FROM dadung_voucher WHERE id_voucher=?";
      
      // Bước 3: Xóa voucher
      $sql_voucher = "DELETE FROM voucher WHERE id=?";
      
      if(is_array($id)){
          foreach ($id as $ma) {
              pdo_execute($sql_donhang, $ma);
              pdo_execute($sql_dadung, $ma);
              pdo_execute($sql_voucher, $ma);
          }
      }
      else{
          pdo_execute($sql_donhang, $id);
          pdo_execute($sql_dadung, $id);
          pdo_execute($sql_voucher, $id);
      }
   }
  
     
?>