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

const tabItems = document.querySelectorAll(".account-link");
const accountRight = document.querySelector(".account-right");
const orderHistoryContent = document.querySelector(".order-history");
const orderHistory = document.getElementById("history-order");
const historyLink = document.getElementById("history");
const myAccount = document.getElementById("myaccount");
const accountHistory = document.querySelector(".account-history");

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
    case "history-order":
      showHistoryOrder();
      break;

    default:
      console.error("Unhandled tab id: ", tabId);
  }
}

function hideAllTabs() {
  if (accountRight) accountRight.style.display = "none";
  if (accountHistory) accountHistory.style.display = "none";
  if (orderHistoryContent) orderHistoryContent.style.display = "none";
}
if (historyLink) {
  historyLink.addEventListener("click", showHistory);
}
function showHistory() {
  hideAllTabs();
  if (accountHistory) accountHistory.style.display = "inline-flex";
}

if (myAccount) {
  myAccount.addEventListener("click", showMyAccount);
}
function showMyAccount() {
  hideAllTabs();
  if (accountRight) accountRight.style.display = "inline-flex";
}

if (orderHistory) {
  orderHistory.addEventListener("click", showHistoryOrder);
}
function showHistoryOrder() {
  hideAllTabs();
  if (orderHistoryContent) orderHistoryContent.style.display = "inline-flex";
}
