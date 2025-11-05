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

// Lấy form elements by name thay vì by index để tránh lỗi
var imgcart = null;
var colorcart = null;
var sizecart = null;
var soluongcart = null;

if (addtocart_elements.length > 0) {
  var form = addtocart_elements[0];
  imgcart = form.querySelector('input[name="img"]');
  colorcart = form.querySelector('input[name="color"]');
  sizecart = form.querySelector('input[name="size"]');
  soluongcart = form.querySelector('input[name="soluong"]');
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

// Lấy input soluong_checkout by name
var soluongCheckoutInput = checkoutdung
  ? checkoutdung.querySelector('input[name="soluong_checkout"]')
  : null;

// Helper function để update checkout input
function updateCheckoutSoluong(value) {
  if (soluongCheckoutInput) {
    soluongCheckoutInput.value = value;
  }
}

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
    updateCheckoutSoluong(soluong.value);
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

  // Update gallery navigation state - tìm index của ảnh được click
  if (typeof allImages !== "undefined" && allImages.length > 0) {
    // Tìm ảnh trong mảng allImages dựa trên element được click
    for (let i = 0; i < allImages.length; i++) {
      if (allImages[i] === a || allImages[i].src === a.src) {
        currentImageIndex = i;
        updateActiveThumb();
        break;
      }
    }
  }
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
    updateCheckoutSoluong(soluong.value);
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
    updateCheckoutSoluong(soluong.value);
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
    updateCheckoutSoluong(soluong.value);
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

// Guarded size picker logic so this file can be loaded on pages without size UI
var pick_size_el = document.getElementsByClassName("pick-size");
var pick_size =
  pick_size_el && pick_size_el.length > 0 ? pick_size_el[0] : null;
if (pick_size && typeof sizecart !== "undefined" && sizecart) {
  sizecart.value = pick_size.innerHTML;
}

function change_size(a) {
  if (!pick_size) return; // not on detail page
  for (let i = 0; i < sizeItems.length; i++) {
    sizeItems[i].classList.remove("active");
  }
  a.classList.add("active");
  pick_size.innerHTML = a.innerHTML;
  if (typeof sizecart !== "undefined" && sizecart) {
    sizecart.value = pick_size.innerHTML;
  }

  id_size = a.getAttribute("id_size");
  if (typeof soluongtonkho !== "undefined") {
    for (let i = 0; i < soluongtonkho.length; i++) {
      if (
        soluongtonkho[i]["id_color"] == id_color &&
        soluongtonkho[i]["id_size"] == id_size
      ) {
        soluongcon = soluongtonkho[i]["soluong"];
      }
    }
  }
  var slconEl = document.getElementById("slcon");
  if (slconEl) slconEl.innerHTML = soluongcon;
  var alert = document.getElementsByClassName("detail-inventory")[0];
  var detailInput = document.getElementsByClassName("detail-input");
  if (detailInput.length > 0 && detailInput[0].children[1]) {
    soluongmua = detailInput[0].children[1].value;
  }
  if (soluongmua <= soluongcon) {
    if (cartdung) cartdung.style.display = "block";
    if (checkoutdung) checkoutdung.style.display = "block";
    if (cartsai) cartsai.style.display = "none";
    if (checkoutsai) checkoutsai.style.display = "none";
    updateCheckoutSoluong(soluong.value);
    if (alert) {
      alert.innerHTML = "Còn hàng";
      alert.style.color = "#46694f";
    }
  } else {
    if (alert) {
      alert.innerHTML = "Hết hàng";
      alert.style.color = "red";
    }
    if (cartdung) cartdung.style.display = "none";
    if (checkoutdung) checkoutdung.style.display = "none";
    if (cartsai) cartsai.style.display = "block";
    if (checkoutsai) checkoutsai.style.display = "block";
  }
}

// Only wire up tab/policy/comment handlers if elements exist (detail page)
if (detailTab && detailTab.length) {
  [...detailTab].forEach((item) =>
    item.addEventListener("click", handleTabClick)
  );
}
function handleTabClick(event) {
  if (!detailTab || !detailTab.length) return;
  [...detailTab].forEach((item) => item.classList.remove("active"));
  event.target.classList.add("active");
}
if (policy && detailContent && detailContent2 && detailComment) {
  policy.addEventListener("click", showPolicy);
}
function showPolicy() {
  if (!detailContent || !detailContent2 || !detailComment) return;
  detailContent.style.display = "none";
  detailComment.style.display = "none";
  detailContent2.style.display = "block";
}

if (comment && detailContent && detailContent2 && detailComment) {
  comment.addEventListener("click", showComment);
}
function showComment() {
  if (!detailContent || !detailContent2 || !detailComment) return;
  detailContent.style.display = "none";
  detailContent2.style.display = "none";
  detailComment.style.display = "block";
}

if (idDetail && detailContent && detailContent2 && detailComment) {
  idDetail.addEventListener("click", showIdDetail);
}
function showIdDetail() {
  if (!detailContent || !detailContent2 || !detailComment) return;
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

// ========== Image Gallery Navigation ==========
let currentImageIndex = 0;
let allImages = [];
let mainImageElement = null;

// Initialize image gallery on page load
document.addEventListener("DOMContentLoaded", function () {
  initImageGallery();
});

function initImageGallery() {
  // Check if detail_image exists
  if (
    typeof detail_image === "undefined" ||
    !detail_image ||
    detail_image.length === 0
  ) {
    return;
  }

  // Get all thumbnail images from currently visible detail-image
  let activeDetailImage = Array.from(detail_image).find(
    (img) => img.style.display !== "none"
  );

  if (!activeDetailImage && detail_image.length > 0) {
    // If no active found, use first one
    detail_image[0].style.display = "flex";
    activeDetailImage = detail_image[0];
  }

  if (!activeDetailImage) {
    return; // Still no active image, exit
  }

  const thumbnails = activeDetailImage.querySelectorAll(".detail-image__item");
  allImages = Array.from(thumbnails);

  // Get main image element
  mainImageElement = activeDetailImage.querySelector(".detail-img");

  // Set first image as active
  if (allImages.length > 0) {
    allImages[0].classList.add("active");
    currentImageIndex = 0;
  }

  // Don't add new event listeners - use existing onclick="change_img(this)"
}

function updateActiveThumb() {
  allImages.forEach((img) => img.classList.remove("active"));
  if (allImages[currentImageIndex]) {
    allImages[currentImageIndex].classList.add("active");
  }
}

function nextImage() {
  if (allImages.length === 0) {
    initImageGallery();
  }

  if (allImages.length === 0) return; // No images to navigate

  currentImageIndex = (currentImageIndex + 1) % allImages.length;

  // Cập nhật ảnh lớn trực tiếp
  var ind = 0;
  for (let i = 0; i < detail_image.length; i++) {
    if (detail_image[i].style.display != "none") {
      ind = i;
      break;
    }
  }

  if (main_img[ind] && allImages[currentImageIndex]) {
    main_img[ind].src = allImages[currentImageIndex].src;
    updateActiveThumb();
  }
}

function prevImage() {
  if (allImages.length === 0) {
    initImageGallery();
  }

  if (allImages.length === 0) return; // No images to navigate

  currentImageIndex =
    (currentImageIndex - 1 + allImages.length) % allImages.length;

  // Cập nhật ảnh lớn trực tiếp
  var ind = 0;
  for (let i = 0; i < detail_image.length; i++) {
    if (detail_image[i].style.display != "none") {
      ind = i;
      break;
    }
  }

  if (main_img[ind] && allImages[currentImageIndex]) {
    main_img[ind].src = allImages[currentImageIndex].src;
    updateActiveThumb();
  }
}

// Reinitialize gallery when color changes (wrap existing function)
(function () {
  const originalChangeColor = window.change_color;
  window.change_color = function (a) {
    if (originalChangeColor) {
      originalChangeColor.call(this, a);
    }
    setTimeout(() => {
      initImageGallery();
    }, 100);
  };
})();
