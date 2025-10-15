// Product Detail
const idDetail = document.getElementById("iddetail");
const policy = document.getElementById("policy");
const comment = document.getElementById("comment");
const detailContent = document.querySelector(".detail-policy");
const detailContent2 = document.querySelector(".detail-content-2");
const detailComment = document.querySelector(".detail-content-comment");
const detailTab = document.querySelectorAll(".detail-tab__link");
const sizeItems = document.querySelectorAll(".detail-size__item");

var detail_image = document.getElementsByClassName("detail-image");
var detail_color = document.getElementsByClassName("detail-circle");
var addtocart_elements = document.getElementsByClassName("addtocart");

// Kiểm tra elements tồn tại trước khi sử dụng
if (
  addtocart_elements.length > 0 &&
  addtocart_elements[0].children.length > 5
) {
  var imgcart = addtocart_elements[0].children[1];
  var colorcart = addtocart_elements[0].children[3];
  var sizecart = addtocart_elements[0].children[4];
  var soluongcart = addtocart_elements[0].children[5];
}

if (detail_color.length > 0) {
  detail_color[0].style.border = "0.5px solid gray";
  if (colorcart && detail_color[0].children.length > 0) {
    colorcart.value = detail_color[0].children[0].innerHTML;
  }
}

if (detail_image.length > 0 && imgcart && detail_image[0].children.length > 1) {
  imgcart.value = detail_image[0].children[1].innerHTML;
}

// Kiểm tra soluongtonkho tồn tại
if (typeof soluongtonkho !== "undefined" && soluongtonkho.length > 0) {
  var id_color = soluongtonkho[0]["id_color"];
  var id_size = soluongtonkho[0]["id_size"];
  var soluongcon = soluongtonkho[0]["soluong"];

  // Cập nhật hiển thị số lượng
  var slcon_element = document.getElementById("slcon");
  if (slcon_element) {
    slcon_element.innerHTML = soluongcon;
  }
} else {
  var id_color = null;
  var id_size = null;
  var soluongcon = 0;
}

// Kiểm tra detail-input element tồn tại
var detailInputElements = document.getElementsByClassName("detail-input");
var soluongmua = 1; // default value
if (
  detailInputElements.length > 0 &&
  detailInputElements[0].children.length > 1
) {
  soluongmua = detailInputElements[0].children[1].value;
}

var checkoutdung = document.getElementById("checkoutdung");
var checkoutsai = document.getElementById("checkoutsai");
var cartdung = document.getElementById("cartdung");
var cartsai = document.getElementById("cartsai");

function change_color(a) {
  if (!detail_color || !detail_image || !colorcart || !imgcart) return;

  var ind = 0;
  for (let i = 0; i < detail_color.length; i++) {
    detail_color[i].style.border = "none";
    if (detail_image[i]) detail_image[i].style.display = "none";
    if (a == detail_color[i]) {
      ind = i;
    }
  }
  a.style.border = "0.5px solid gray";
  if (a.children.length > 0) colorcart.value = a.children[0].innerHTML;
  if (detail_image[ind]) detail_image[ind].style.display = "flex";
  if (detail_image[ind] && detail_image[ind].children.length > 1) {
    imgcart.value = detail_image[ind].children[1].innerHTML;
  }

  id_color = a.getAttribute("id_color");
  if (typeof soluongtonkho !== "undefined" && soluongtonkho.length > 0) {
    for (let i = 0; i < soluongtonkho.length; i++) {
      if (
        soluongtonkho[i]["id_color"] == id_color &&
        soluongtonkho[i]["id_size"] == id_size
      ) {
        soluongcon = soluongtonkho[i]["soluong"];
      }
    }
  }

  var slcon_element = document.getElementById("slcon");
  if (slcon_element) slcon_element.innerHTML = soluongcon;

  var alert = document.getElementsByClassName("detail-inventory")[0];
  var detailInputElements = document.getElementsByClassName("detail-input");
  if (
    detailInputElements.length > 0 &&
    detailInputElements[0].children.length > 1
  ) {
    soluongmua = detailInputElements[0].children[1].value;
  }
  if (soluongmua <= soluongcon) {
    cartdung.style.display = "block";
    checkoutdung.style.display = "block";
    cartsai.style.display = "none";
    checkoutsai.style.display = "none";
    document.getElementsByClassName(
      "detail-btn"
    )[0].children[0].children[0].value = soluong.value;
    alert.innerHTML = "Còn hàng";
    alert.style.color = "#46694f";
  } else {
    alert.innerHTML = "Hết hàng";
    alert.style.color = "red";
    cartdung.style.display = "none";
    checkoutdung.style.display = "none";
    cartsai.style.display = "block";
    checkoutsai.style.display = "block";
  }
}
function tatthongbaocart() {
  document.querySelectorAll(".modal")[0].classList.remove("active");
}
function hethang() {
  document.querySelectorAll(".modal")[0].classList.add("active");
}
var sub_img = document.getElementsByClassName("detail-image__item");
var main_img = document.getElementsByClassName("detail-img");
function change_img(a) {
  var ind = 0;
  for (let i = 0; i < detail_image.length; i++) {
    if (detail_image[i].style.display != "none") {
      ind = i;
    }
  }
  main_img[ind].src = a.src;
}

var detailInputElements = document.getElementsByClassName("detail-input");
var soluong =
  detailInputElements.length > 0 && detailInputElements[0].children.length > 1
    ? detailInputElements[0].children[1]
    : null;

if (soluong && typeof soluongcart !== "undefined") {
  soluongcart.value = soluong.value;
}

function update_soluong() {
  if (!soluong) return; // Kiểm tra soluong tồn tại

  var inventoryElements = document.getElementsByClassName("detail-inventory");
  if (inventoryElements.length === 0) return;
  var alert = inventoryElements[0];

  soluong.value = parseInt(soluong.value);

  // Kiểm tra nếu không phải là số nguyên dương
  if (isNaN(soluong.value) || soluong.value <= 0) {
    soluong.value = 1; // Gán bằng 1 nếu không phải là số nguyên dương
  }

  if (typeof soluongcart !== "undefined") {
    soluongcart.value = soluong.value;
  }
  soluongmua = soluong.value;
  if (soluongmua <= soluongcon) {
    cartdung.style.display = "block";
    checkoutdung.style.display = "block";
    cartsai.style.display = "none";
    checkoutsai.style.display = "none";
    document.getElementsByClassName(
      "detail-btn"
    )[0].children[0].children[0].value = soluong.value;
    alert.innerHTML = "Còn hàng";
    alert.style.color = "#46694f";
  } else {
    alert.innerHTML = "Hết hàng";
    alert.style.color = "red";
    cartdung.style.display = "none";
    checkoutdung.style.display = "none";
    cartsai.style.display = "block";
    checkoutsai.style.display = "block";
  }
}
function minus() {
  if (soluong.value > 1) {
    soluong.value--;
  }
  soluongcart.value = soluong.value;
  var alert = document.getElementsByClassName("detail-inventory")[0];
  soluongmua =
    document.getElementsByClassName("detail-input")[0].children[1].value;
  if (soluongmua <= soluongcon) {
    cartdung.style.display = "block";
    checkoutdung.style.display = "block";
    cartsai.style.display = "none";
    checkoutsai.style.display = "none";
    document.getElementsByClassName(
      "detail-btn"
    )[0].children[0].children[0].value = soluong.value;
    alert.innerHTML = "Còn hàng";
    alert.style.color = "#46694f";
  } else {
    alert.innerHTML = "Hết hàng";
    alert.style.color = "red";
    cartdung.style.display = "none";
    checkoutdung.style.display = "none";
    cartsai.style.display = "block";
    checkoutsai.style.display = "block";
  }
}
function plus() {
  soluong.value++;
  soluongcart.value = soluong.value;
  var alert = document.getElementsByClassName("detail-inventory")[0];
  soluongmua =
    document.getElementsByClassName("detail-input")[0].children[1].value;
  if (soluongmua <= soluongcon) {
    cartdung.style.display = "block";
    checkoutdung.style.display = "block";
    cartsai.style.display = "none";
    checkoutsai.style.display = "none";
    document.getElementsByClassName(
      "detail-btn"
    )[0].children[0].children[0].value = soluong.value;
    alert.innerHTML = "Còn hàng";
    alert.style.color = "#46694f";
  } else {
    alert.innerHTML = "Hết hàng";
    alert.style.color = "red";
    cartdung.style.display = "none";
    checkoutdung.style.display = "none";
    cartsai.style.display = "block";
    checkoutsai.style.display = "block";
  }
}

var pick_size = document.getElementsByClassName("pick-size")[0];
sizecart.value = pick_size.innerHTML;
function change_size(a) {
  for (let i = 0; i < sizeItems.length; i++) {
    sizeItems[i].classList.remove("active");
  }
  a.classList.add("active");
  pick_size.innerHTML = a.innerHTML;
  sizecart.value = pick_size.innerHTML;

  id_size = a.getAttribute("id_size");
  for (let i = 0; i < soluongtonkho.length; i++) {
    if (
      soluongtonkho[i]["id_color"] == id_color &&
      soluongtonkho[i]["id_size"] == id_size
    ) {
      soluongcon = soluongtonkho[i]["soluong"];
    }
  }
  document.getElementById("slcon").innerHTML = soluongcon;
  var alert = document.getElementsByClassName("detail-inventory")[0];
  soluongmua =
    document.getElementsByClassName("detail-input")[0].children[1].value;
  if (soluongmua <= soluongcon) {
    cartdung.style.display = "block";
    checkoutdung.style.display = "block";
    cartsai.style.display = "none";
    checkoutsai.style.display = "none";
    document.getElementsByClassName(
      "detail-btn"
    )[0].children[0].children[0].value = soluong.value;
    alert.innerHTML = "Còn hàng";
    alert.style.color = "#46694f";
  } else {
    alert.innerHTML = "Hết hàng";
    alert.style.color = "red";
    cartdung.style.display = "none";
    checkoutdung.style.display = "none";
    cartsai.style.display = "block";
    checkoutsai.style.display = "block";
  }
}

[...detailTab].forEach((item) =>
  item.addEventListener("click", handleTabClick)
);
function handleTabClick(event) {
  // console.log(event.target);
  [...detailTab].forEach((item) => item.classList.remove("active"));
  event.target.classList.add("active");
}
policy.addEventListener("click", showPolicy);
function showPolicy() {
  detailContent.style.display = "none";
  detailComment.style.display = "none";
  detailContent2.style.display = "block";
}

comment.addEventListener("click", showComment);
function showComment() {
  detailContent.style.display = "none";
  detailContent2.style.display = "none";
  detailComment.style.display = "block";
}

idDetail.addEventListener("click", showIdDetail);
function showIdDetail() {
  detailComment.style.display = "none";
  detailContent2.style.display = "none";
  detailContent.style.display = "block";
}

// Lấy tất cả các đối tượng radio input
const ratingInputs = document.querySelectorAll('input[name="rating"]');

// Đặt sự kiện click cho từng đối tượng input
ratingInputs.forEach((input) => {
  input.addEventListener("click", function () {
    const selectedRating = document.getElementById("selectedRating");
    selectedRating.value = this.value;
  });
});
function anmatkhau() {
  document.getElementsByClassName("hien")[0].classList.toggle("fa-eye-slash");
  if (
    document
      .getElementsByClassName("login-password")[0]
      .getElementsByTagName("input")[0].type == "password"
  )
    document
      .getElementsByClassName("login-password")[0]
      .getElementsByTagName("input")[0].type = "text";
  else
    document
      .getElementsByClassName("login-password")[0]
      .getElementsByTagName("input")[0].type = "password";
}
