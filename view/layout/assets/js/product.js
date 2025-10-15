const listVal = document.querySelector(".list-val");
const listValMenu = document.querySelector(".list-val-menu");
const toggle1 = document.querySelector(".updown-toggle");

// Kiểm tra element tồn tại trước khi thêm event listener
if (toggle1) {
  toggle1.addEventListener("click", function (event) {
    event.target.classList.toggle("fa-angle-up");
    event.target.classList.toggle("fa-angle-down");
    if (listValMenu) listValMenu.classList.toggle("active");
  });
}

function tailai(a) {
  var link = a.getAttribute("link");
  if (a.checked) {
    window.location.href = link;
  } else {
    var id = link.slice(-1);
    link = link.slice(0, link.length - 1);
    link = link + "-" + id;
    window.location.href = link;
  }
}

var listprice = document.getElementById("listprice");
var listgioitinh = document.getElementById("listgioitinh");

// Kiểm tra elements tồn tại trước khi sử dụng
var checkprice = listprice ? listprice.getAttribute("checkprice") : "";
var checkgioitinh = listgioitinh
  ? listgioitinh.getAttribute("checkgioitinh")
  : "";

if (checkprice) {
  for (let i = 0; i < checkprice.length; i++) {
    if (
      listprice.children[checkprice[i] - 1] &&
      listprice.children[checkprice[i] - 1].children[0]
    ) {
      listprice.children[checkprice[i] - 1].children[0].checked = "checked";
    }
  }
}

if (checkgioitinh) {
  for (let i = 0; i < checkgioitinh.length; i++) {
    if (
      listgioitinh.children[checkgioitinh[i] - 1] &&
      listgioitinh.children[checkgioitinh[i] - 1].children[0]
    ) {
      listgioitinh.children[checkgioitinh[i] - 1].children[0].checked =
        "checked";
    }
  }
}

var subpage = document.getElementsByClassName("subpage");
var iconsubpage = document.getElementsByClassName("product-pagination-link");
function changesubpage(a) {
  for (let i = 0; i < iconsubpage.length; i++) {
    iconsubpage[i].classList.remove("active");
  }
  for (let i = 0; i < subpage.length; i++) {
    subpage[i].style.display = "none";
  }
  a.classList.add("active");
  subpage[a.innerHTML - 1].style.display = "grid";
}
