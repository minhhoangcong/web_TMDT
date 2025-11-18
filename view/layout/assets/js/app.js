$(document).ready(function () {
  // Init hero slider with defensive accessibility handling to avoid DevTools warnings
  $(".hero-slider")
    .on("init reInit afterChange", function (e, slick, currentSlide) {
      var cur =
        typeof currentSlide === "number"
          ? currentSlide
          : slick.currentSlide || 0;
      var $slides = $(slick.$slides);
      // Make only the current slide focusable; hide others from a11y tools
      $slides.each(function (i, el) {
        var $el = $(el);
        var isActive = i === cur;
        $el.attr("aria-hidden", !isActive);
        try {
          el.inert = !isActive;
        } catch (_) {}
        var focusables = $el.find(
          "a, button, input, textarea, select, [tabindex]"
        );
        focusables.attr("tabindex", isActive ? 0 : -1);
      });
      // Remove stray focus on hidden slides
      if (
        document.activeElement &&
        $(document.activeElement)
          .closest(".slick-slide")
          .attr("aria-hidden") === "true"
      ) {
        $(document.activeElement).blur();
      }
    })
    .slick({
      arrows: false,
      infinite: true,
      autoplay: true,
      autoplaySpeed: 2000,
      infinite: true,

      dots: true,
      cssEase: "linear",
      accessibility: false, // prevent Slick from adding tabindex that conflicts with aria-hidden
      responsive: [
        {
          breakpoint: 480,
          settings: {
            dots: false,
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ],
      // prevArrow: `<button type='button' class='slick-prev slick-arrow'><i class='fa fa-angle-left' aria-hidden='true'></i></button>`,
      // nextArrow: `<button type='button' class='slick-next slick-arrow'><i class='fa fa-angle-right' aria-hidden='true'></i></button>`,
    });
});

// Wishlist toggle handler (works on product cards and detail page)
$(document).on("click", ".wishlist-toggle", function (e) {
  e.preventDefault();
  var $btn = $(this);
  var pid = parseInt($btn.data("product-id"), 10);
  if (!pid || isNaN(pid)) return;

  if (!window.AUTH_LOGGED_IN) {
    window.location.href = "index.php?pg=login";
    return;
  }

  var isActive = $btn.hasClass("active");
  var url = isActive
    ? window.WISHLIST_ENDPOINTS && window.WISHLIST_ENDPOINTS.remove
    : window.WISHLIST_ENDPOINTS && window.WISHLIST_ENDPOINTS.add;
  if (!url) return;

  $.post(url, { product_id: pid })
    .done(function (res) {
      try {
        if (res && res.success) {
          // Cập nhật trạng thái active của nút
          $btn.toggleClass("active", !isActive);

          // Cập nhật số lượng yêu thích ở header với animation
          if (typeof res.count !== "undefined") {
            $(".wishlist-count").text(res.count).addClass("updated");
            setTimeout(function () {
              $(".wishlist-count").removeClass("updated");
            }, 500);
          } else if (
            window.WISHLIST_ENDPOINTS &&
            window.WISHLIST_ENDPOINTS.count
          ) {
            $.getJSON(window.WISHLIST_ENDPOINTS.count, function (r) {
              if (r && typeof r.count !== "undefined") {
                $(".wishlist-count").text(r.count).addClass("updated");
                setTimeout(function () {
                  $(".wishlist-count").removeClass("updated");
                }, 500);
              }
            });
          }

          // Show toast notification
          if (isActive) {
            Toast.success("Đã xóa khỏi yêu thích");
          } else {
            Toast.success("Đã thêm vào yêu thích");
          }

          // CHỈ xóa card khỏi DOM nếu:
          // 1. Đang XÓA (isActive = true)
          // 2. Đang ở trang wishlist (.wishlist-page tồn tại)
          if (isActive && $(".wishlist-page").length > 0) {
            var $card = $btn.closest(".product-item");
            if ($card.length > 0) {
              $card.fadeOut(150, function () {
                $(this).remove();
                // Kiểm tra nếu không còn sản phẩm nào
                if ($(".wishlist-page .product-item").length === 0) {
                  $(".wishlist-content .container").html(
                    "<p>Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>"
                  );
                }
              });
            }
          }
        } else if (res && res.error === "not_logged_in") {
          window.location.href = "index.php?pg=login";
        }
      } catch (err) {
        console.warn("Wishlist response parse error", err);
      }
    })
    .fail(function () {
      console.warn("Wishlist request failed");
    });
});

// Add to cart from wishlist without leaving the page
$(document).on("click", ".wishlist-addtocart", function (e) {
  e.preventDefault();
  var $btn = $(this);
  var pid = parseInt($btn.data("product-id"), 10);
  if (!pid || isNaN(pid)) return;

  // Show loading state
  var originalText = $btn.text();
  $btn
    .prop("disabled", true)
    .html('<i class="fa fa-spinner fa-spin"></i> Đang thêm...');

  $.post("wishlist_add_to_cart.php", { product_id: pid })
    .done(function (res) {
      if (res && res.success) {
        // Show toast notification
        Toast.success("Đã thêm vào giỏ hàng!");

        // Update header cart badge with animation
        if (typeof res.cartCount !== "undefined") {
          $(".cart-count").text(res.cartCount).addClass("updated");
          setTimeout(function () {
            $(".cart-count").removeClass("updated");
          }, 500);
        } else if (window.CART_COUNT_ENDPOINT) {
          $.getJSON(window.CART_COUNT_ENDPOINT, function (r) {
            if (r && typeof r.cartCount !== "undefined") {
              $(".cart-count").text(r.cartCount).addClass("updated");
              setTimeout(function () {
                $(".cart-count").removeClass("updated");
              }, 500);
            }
          });
        }

        // Update button state
        $btn.addClass("added").html('<i class="fa fa-check"></i> Đã thêm');
        setTimeout(function () {
          $btn.prop("disabled", false).removeClass("added").text(originalText);
        }, 1500);
      } else {
        Toast.error("Không thể thêm vào giỏ hàng");
        $btn.prop("disabled", false).text(originalText);
      }
    })
    .fail(function () {
      Toast.error("Lỗi kết nối server");
      $btn.prop("disabled", false).text(originalText);
    });
});

const tabItems = document.querySelectorAll(".account-link");
const accountRight = document.querySelector(".account-right");
const orderHistoryContent = document.querySelector(".orders-page");
const historyLink = document.getElementById("history");
const myAccount = document.getElementById("myaccount");
const orderDetailContent = document.querySelector(".order-detail-content");

// Chỉ thêm event listener nếu element tồn tại
if (tabItems.length > 0) {
  tabItems.forEach((item) => item.addEventListener("click", handleTabClick));
}
function handleTabClick(event) {
  tabItems.forEach((item) => item.classList.remove("active"));
  event.target.classList.add("active");

  const tabId = event.target.id.toLowerCase();
  showTabContent(tabId);
}

function showTabContent(tabId) {
  switch (tabId) {
    case "history":
      showHistory();
      break;
    case "myaccount":
      showMyAccount();
      break;

    default:
      console.error("Unhandled tab id: ", tabId);
  }
}

function hideAllTabs() {
  if (accountRight) accountRight.style.display = "none";
  if (orderHistoryContent) orderHistoryContent.style.display = "none";
  if (orderDetailContent) orderDetailContent.style.display = "none";
}

if (historyLink) {
  historyLink.addEventListener("click", showHistory);
}
function showHistory() {
  hideAllTabs();
  if (orderHistoryContent) orderHistoryContent.style.display = "block";
}

if (myAccount) {
  myAccount.addEventListener("click", showMyAccount);
}
function showMyAccount() {
  hideAllTabs();
  if (accountRight) accountRight.style.display = "flex";
}
