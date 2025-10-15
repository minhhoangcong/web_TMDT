var diachi = document.getElementsByClassName("diachikhac")[0];
function diachikhac() {
  if (diachi && diachi.style.display == "block") {
    diachi.style.display = "none";
  } else if (diachi) {
    diachi.style.display = "block";
  }
}

// Kiểm tra element tồn tại trước khi truy cập
var orderForms = document.getElementsByClassName("order-form order-info");
if (orderForms.length > 1 && orderForms[1].children.length > 6) {
  var namenhan = orderForms[1].children[0];
  var emailnhan = orderForms[1].children[2];
  var sdtnhan = orderForms[1].children[4];
  var diachinhan = orderForms[1].children[6];

  if (
    diachi &&
    (namenhan.value != "" ||
      emailnhan.value != "" ||
      sdtnhan.value != "" ||
      diachinhan.value != "")
  ) {
    diachi.style.display = "block";
  }
}

// Kiểm tra element checkdiachi tồn tại
var checkdiachi_elements = document.getElementsByClassName("checkdiachi");
if (diachi && checkdiachi_elements.length > 0) {
  if (diachi.style.display == "block") {
    checkdiachi_elements[0].checked = "checked";
  } else {
    checkdiachi_elements[0].checked = "";
  }
}

function thanhtoanthanhcong() {
  var modals = document.querySelectorAll(".modal");
  if (modals.length > 0) {
    modals[0].classList.add("active");
  }
}
