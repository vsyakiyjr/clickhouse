import OrderRequest from "./components/OrderRequest";

require("./bootstrap");
require("jquery");
require("tether");
require("jquery-ui");
require("owl.carousel");
require("slick-carousel");
require("./mask");

require("bootstrap");

import datepickerFactory from "jquery-datepicker";
import VueSimpleSuggest from "vue-simple-suggest/lib";
import { event } from "jquery";

datepickerFactory($);

require("jquery-sticky");

$.datepicker.regional["ru"] = {
  closeText: "Закрыть",
  prevText: "",
  nextText: "",
  currentText: "Сегодня",
  monthNames: [
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь",
  ],
  monthNamesSelected: [
    "января",
    "февраля",
    "марта",
    "апреля",
    "мая",
    "июня",
    "июля",
    "августа",
    "сентября",
    "октября",
    "ноября",
    "декабря",
  ],
  monthNamesShort: [
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь",
  ],
  dayNames: [
    "воскресенье",
    "понедельник",
    "вторник",
    "среда",
    "четверг",
    "пятница",
    "суббота",
  ],
  dayNamesShort: ["вск", "пнд", "втр", "срд", "чтв", "птн", "сбт"],
  dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
  weekHeader: "Не",
  dateFormat: "dd.mm.yy",
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: false,
  changeMonth: false,
  changeYear: true,
  yearSuffix: "",
  showButtonPanel: true,
};

$.datepicker.setDefaults($.datepicker.regional["ru"]);

export function updateCartCountHeader(count) {
  $(".cart-count").text(count);

  // if (count > 0) {
  // 	$('.search-widget .menu-button').addClass('has-items-in-cart');
  // } else {
  // 	$('.search-widget .menu-button').removeClass('has-items-in-cart');
  // }
}

// TODO put Cart.vue to cart.blade.php and use it's native functionality, then remove this function
export function getCartContent() {
  let cartId = localStorage.getItem("cart_id");

  $.get("/cart?cart_id=" + cartId).then((r) => {
    let contentEncoded = JSON.stringify(r);

    $("#cart-content").text(contentEncoded);
  });
}

export function array_move(arr, old_index, new_index) {
  if (new_index >= arr.length) {
    var k = new_index - arr.length + 1;
    while (k--) {
      arr.push(undefined);
    }
  }
  arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);

  return arr; // for testing
}

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  $.ajaxSetup({
    headers: { "X-CSRF-TOKEN": token.content },
  });
}

window.Vue = require("vue");
Vue.component("cart", require("./components/Cart.vue").default);
Vue.component("cart-page", require("./components/CartPage.vue").default);
Vue.component("search-widget", require("./components/SearchWidget").default);
Vue.component("vue-simple-suggest", VueSimpleSuggest);
Vue.component("order-request", OrderRequest);
Vue.component("callback", require("./components/Callback").default);
Vue.component(
  "currency-switch",
  require("./components/CurrencySwitch").default
);
Vue.component("loader", require("./components/Loader").default);
Vue.component("auth", require("./components/Auth.vue").default);
Vue.component(
  "reset-password",
  require("./components/ResetPassword.vue").default
);
Vue.component("socials-auth", require("./components/SocialsAuth").default);

const app = new Vue({
  el: "#app",
});

const app2 = new Vue({
  el: "#app2",
});

const app3 = new Vue({
  el: "#app3",
});

var owl = $(".product_img_carousel");
var owlNav = $(".owl-nav-custom");
var possibleAttributeCombinations;
var currentAttributeCombinations;

try {
  let possibleAttributeCombinationsElement = $(
    ".possible-attributes-combination"
  );
  let currentAttributeCombinationElement = $(".attributes-combination");

  possibleAttributeCombinations = JSON.parse(
    possibleAttributeCombinationsElement.text()
  );
  currentAttributeCombinations = JSON.parse(
    currentAttributeCombinationElement.text()
  );

  possibleAttributeCombinationsElement.remove();
  currentAttributeCombinationElement.remove();
} catch (e) { }

function customPager() {
  $.each($(owl).find(".owl-item"), function (i) {
    var pageItem = $(
      '<a class="item-link" data-index="' + i + '" href="#"></a>'
    )
      .css({
        background:
          "url(" +
          $(this)
            .find("img")
            .attr("src") +
          ") center center no-repeat",
        "-webkit-background-size": "cover",
        "-moz-background-size": "cover",
        "-o-background-size": "cover",
        "background-size": "cover",
      })
      .click(function (e) {
        e.preventDefault();
        owl.trigger("to.owl.carousel", i, "test");
        return false;
      });
    $(".owl-nav-custom").append(pageItem);
  });
  owlNav
    .owlCarousel({
      items: 6,
      loop: false,
      nav: true,
      mouseDrag: false,
      touchDrag: false,
      dots: false,
      onInitialized: () => {
        $('.item-link[data-index="0"]').addClass("active");
      },
    })
    .on("to.owl.carousel", function (e, index) {
      $(".item-link").removeClass("active");
      $('.item-link[data-index="' + index + '"]').addClass("active");
    });
}

$(document).ready(function () {
  // NEW REDESIGN
  $("#mobileShowAuthModal").on("tap", function () {
    $(".mobile-menu").removeClass("active");
    $(".burger").removeClass("active");
    $("body").removeClass("body-fixed");
    $(".auth-modal").show();
    $(".blocker").addClass("active");
  });
  $("#showAuthModal").on("tap", function () {
    $(".auth-modal").show();
    $(".blocker").addClass("active");
  });
  $(".auth-close").on("click", function () {
    $(".auth-modal").hide();
    $(".blocker").removeClass("active");
  });

  $(".header .cart-icon").on("mouseenter", function () {
    if ($(window).width() >= "1026") {
      $(".cart-widget").show();
    } else {
      $(".cart-widget").hide();
    }
  });
  $(".add-to-cart").on("click", function () {
    if ($(window).width() >= "1026") {
      $(".cart-widget").show();
    } else {
      $(".cart-widget").hide();
    }
  });

  $(document).mouseup(function (e) {
    let div = $(".cart-widget");
    if (!div.is(e.target) && div.has(e.target).length === 0) {
      div.hide();
    }
  });

  // Разделение цены на разрядность
  function numberWithSpaces(x) {
    if (typeof x === "string") {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    } else {
      return x;
    }
  }
  setTimeout(() => {
    $(".product-card .price-amount.RUB").each(function (index) {
      let price = $(this).attr("data-rub-price");
      $(this)
        .find("#priceRub")
        .html(numberWithSpaces(price));
    });
  }, 150);

  $('input[type="tel"]').mask("+375 99 999 99 99");
  $("#modalPhone").mask("+375 99 999 99 99");
  // Вызов модалки каталог товаров
  $("#catalogBtn").on("click", function () {
    $(".header-catalog__list").toggle();
    $(".blocker").toggleClass("active");
    $("body").toggleClass("body-fixed");
  });

  // mobile menu
  $(".burger").on("click", function () {
    $(".mobile-menu").toggleClass("active");
    $(this).toggleClass("active");
    $("body").toggleClass("body-fixed");
  });

  // скрывать каталог при нажатии вне него
  $(".blocker").on("tap", function () {
    $(".header-catalog__list").hide();
    $("#modalDeliviry").hide();
    $("#modalImportant").hide();
    $("#modalDelivInfo").hide();
    $(".auth-modal").hide();
    $(".blocker").removeClass("active");
    $("body").removeClass("body-fixed");
  });
  // main carousel
  $("#mainSlider").on("init", function (event, slick) {
    let img = $(".slick-current")
      .find(".slider__item")
      .attr("data-image");
    $(".my-slider").attr("style", "background-image:url(" + img + ")");
  });
  $("#mainSlider").slick({
    fade: true,
    arrows: false,
    autoplay: true,
    autoplaySpeed: 5000,
  });
  $("#mainSlider").on("beforeChange", function (
    event,
    slick,
    currentSlide,
    nextSlide
  ) {
    $(".my-slider").removeClass("opacity-anim");
    let img = $("div[data-slick-index=" + nextSlide + "]")
      .find(".slider__item")
      .attr("data-image");
    $(".my-slider").attr("style", "background-image:url(" + img + ")");
    // console.log(nextSlide);
  });

  $("#openImportant").on("click", function () {
    $("#modalImportant").show();
    $(".blocker").addClass("active");
    $("body").addClass("body-fixed");
  });
  $("#openDeliviry").on("click", function () {
    $("#modalDeliviry").show();
    $(".blocker").addClass("active");
    $("body").addClass("body-fixed");
  });
  $(".jqmodal-close").on("click", function () {
    $(".modal").hide();
    $(".blocker").removeClass("active");
    $("body").removeClass("body-fixed");
  });

  function editTop() {
    let widget = $("#cartWidget");
    let catalogList = $(".header-catalog__list");
    if (window.pageYOffset > 50) {
      widget.addClass("top");
      catalogList.addClass("top");
    } else {
      widget.removeClass("top");
      catalogList.removeClass("top");
    }
  }
  //sticky header
  const header = document.getElementById("app2");
  if (header) {
    window.onscroll = function () {
      stickyHeader();
      editTop();
    };
  }
  function stickyHeader() {
    if (window.location.pathname != "/order") {
      if (window.pageYOffset > 51) {
        header.classList.add("sticky");
      } else {
        header.classList.remove("sticky");
      }
    }
  }
  // info-page faq toggle
  $(".info-page__faq").on("click", function () {
    $(this)
      .find("h2")
      .toggleClass("active");
    $(this)
      .find(".info-page__faq-hidden")
      .slideToggle('200');
  });

  // product page
  $(".product-page__about-mobile-btn").on("click", function () {
    if ($(".product-page__about-inner").css("display") == "flex") {
      $(".product-page__about-inner").removeClass("show");
      $("#mobileMoreInfo").text("Больше информации");
      $(".product-page__mobile-arrow").removeClass("toggle");
      $(".product-page__about-mobile-btn").css("margin-bottom", "36px");
    } else {
      $(".product-page__about-inner").addClass("show");
      $("#mobileMoreInfo").text("Меньше информации");
      $(".product-page__mobile-arrow").addClass("toggle");
      $(".product-page__about-mobile-btn").css("margin-bottom", "14px");
    }
  });
  if ($(window).width() > 820) {
    $(".product-page__slider img").each(function (i, el) {
      if (i >= 4) {
        $(el).css("display", "none");
        $(".product-page__more").show();
        $(".product-page__slider").css("margin-bottom", "-15px");
      }
      if (i < 4) {
        $(".product-page__more").hide();
        $(".product-page__slider").css("margin-bottom", "32px");
      }
    });

    $(".product-page__more").on("click", function () {
      $(".product-page__slider img").each(function (i, el) {
        if ($(el).css("display") == "none") {
          $(el).css("display", "block");
          $(".product-page__more").text("Меньше изображений");
          $(".product-page__slider").css("margin-bottom", "32px");
        } else {
          if (i >= 4) {
            $(el).css("display", "none");
          }
          $(".product-page__more").text("Больше изображений");
          $(".product-page__slider").css("margin-bottom", "-15px");
        }
      });
    });
  }
  if ($(window).width() <= 820) {
    $(".product-page__more").hide();
  }

  $(document).on("ready", function () {
    let price = $(".product-info__price .price-amount.RUB").attr(
      "data-rub-price"
    );
    $(".product-info__price")
      .find("#priceRub")
      .html(numberWithSpaces(price));
  });

  function kitcut(text, limit) {
    text = text.trim();
    if (text.length <= limit) return text;

    text = text.slice(0, limit);

    return text.trim() + "...";
  }

  let height = $(".product-page__about-desc").height();
  if (height >= 100) {
    const text = $(".product-page__about-desc span").text();
    let newText = kitcut(text, 260);
    $(".product-page__about-desc span").text(newText);
    $("#showMoreText").on("click", function () {
      $(".product-page__about-desc").text(text);
    });
  } else {
    $("#showMoreText").hide();
  }

  $(".qty-minus").on("click", function () {
    let val = Number($(".qty-selector input").val());
    val = val - 1 || 1;
    $(".qty-selector input").val(val);
  });
  $(".qty-plus").on("click", function () {
    let val = Number($(".qty-selector input").val());
    $(".qty-selector input").val(val + 1);
  });

  $("#delivModal").on("click", function () {
    $("#modalDelivInfo").show();
    $(".blocker").addClass("active");
  });
  $("#modalDelivInfo .jqmodal-close").on("click", function () {
    $("#modalDelivInfo").hide();
    $(".blocker").removeClass("active");
  });

  $(document).mouseup(function (e) {
    let div = $(".product-page__select");
    if (!div.is(e.target) && div.has(e.target).length === 0) {
      div.find("span").removeClass("open");
      div.find("ul").hide();
    }
  });

  $(".product-page__select").on("click", function () {
    if (
      $(this)
        .find("span")
        .hasClass("open")
    ) {
      $(this)
        .find("span")
        .removeClass("open");
      $(this)
        .find("ul")
        .hide();
    } else {
      $(".product-page__select")
        .find("span")
        .removeClass("open");
      $(".product-page__select")
        .find("ul")
        .hide();
      $(this)
        .find("span")
        .addClass("open");
      $(this)
        .find("ul")
        .show();
    }
  });

  // product page END

  // NEW REDESIGN END

  if (window.location.pathname == "/order") {
    getCartContent();
  }

  if ($(".showcase-carousel").length > 0 && $(window).width() > 768) {
    let maxWidth = $(".showcase-carousel").width();

    $(".showcase-carousel").attr("style", `max-width: ${maxWidth}px`);

    $(".showcase-carousel").owlCarousel({
      items: 4,
      loop: false,
      nav: true,
      mouseDrag: false,
      touchDrag: false,
      dots: false,
      navText: [
        "<span class='nav-icon'>⟨</span>",
        "<span class='nav-icon'>⟩</span>",
      ],
      responsive: {
        1440: {
          items: 4,
        },
        0: {
          items: 2,
        },
        768: {
          items: 2,
        },
      },
    });
  } else {
    $(".showcase-carousel")
      .removeClass("owl-carousel")
      .addClass("items")
      .addClass("ikea-family-mini");
    // $(".ikea-family-show-full").on('tap', function() {
    //    $('.ikea-family-mini').removeClass('ikea-family-mini');
    //    $(this).hide();
    // });
  }

  if ($(".history_orders-slider").length > 0) {
    $(".history_orders-slider").owlCarousel({
      items: 1,
      nav: true,
      mouseDrag: false,
      touchDrag: false,
      dots: false,
      navText: [
        "<span class='nav-icon'>⟨</span>",
        "<span class='nav-icon'>⟩</span>",
      ],
    });
  }

  if ($(".product_img_carousel").length > 0) {
    owl
      .owlCarousel({
        dots: true,
        items: 1,
        nav: true,
        // autoHeight: true,
        navText: [
          "<span class='nav-icon'>⟨</span>",
          "<span class='nav-icon'>⟩</span>",
        ],
        onInitialized: customPager,
      })
      .on("changed.owl.carousel", function (e, t, d) {
        owlNav.trigger("to.owl.carousel", e.item.index);
      });
  }

  $(document).on("tap", ".more_info_btn", function (e) {
    e.preventDefault();
    if ($(".more_info_btn").hasClass("collapsed")) {
      $(".more_info_btn").text("(показать больше информации)");
    } else {
      $(".more_info_btn").text("(скрыть)");
    }
    return false;
  });

  /* $(".owl-carousel").owlCarousel({
     items: 4,
     loop: true,
     nav: true,
     dots: false
   });
   */

  var loading = false;

  if ($(".next-page").length > 0) {
    $(window).on("scroll", function () {
      if (
        $(window).scrollTop() + window.innerHeight * 1.5 >
        $(".next-page").offset().top &&
        !loading
      ) {
        $(".next-page").trigger("tap");
        loading = true;
      }
    });
  }

  $(document).on("tap", ".next-page", (e) => {
    e.preventDefault();

    $('#hidden-pagination .pagination a[rel="next"]').trigger("tap");

    return false;
  });

  $(document).on("tap", "#hidden-pagination .pagination a", (e) => {
    e.preventDefault();

    var href = e.target.href;

    $("<div>").load(href, function () {
      $(".catalog_products").append($(this).find(".product_layout"));
      $("#hidden-pagination").html($(this).find(".pagination"));
      loading = false;
      $(".products_catalog").owlCarousel({
        items: 1,
        dots: false,
        nav: true,
        navText: [
          "<img src='/images/icons/slider_arro_new.png'>",
          "<img src='/images/icons/slider_arro_new.png'>",
        ],
      });
      if (!$('#hidden-pagination .pagination a[rel="next"]').length) {
        $(".next-page").hide();
        loading = true;
      }
    });
    //
    return false;
  });

  $(document).on("change", ".product-select", function () {
    location = "/catalog/product/" + $(this).val();
  });

  $(document).on("change", ".attribute-select", function () {
    let selectedAttributesCombinations = {};

    $(".attribute-select").each(function () {
      selectedAttributesCombinations[$(this).attr("name")] = $(this).val();
    });

    let attrs = Object.keys(selectedAttributesCombinations),
      currentAttr,
      matchedAttrs;
    var flag = 0;
    for (let code in possibleAttributeCombinations) {
      matchedAttrs = 0;

      for (let attrIndex = 0; attrIndex < attrs.length; attrIndex++) {
        currentAttr = attrs[attrIndex];

        if (
          possibleAttributeCombinations[code][currentAttr] ==
          selectedAttributesCombinations[currentAttr]
        ) {
          matchedAttrs++;
        }
      }

      if (matchedAttrs == attrs.length) {
        return (window.location = "/catalog/product/" + code);
      }
      flag = code;
    }

    alert("Такой комбинации нет в наличии");
    return (window.location = "/catalog/product/" + flag);
  });

  $(document).on("tap", "#logout", function (e) {
    e.preventDefault();

    $.post($(this).attr("href"), () => {
      location = "/";
    });

    return false;
  });

  $(document).on("tap", ".search-overlay", function () {
    $("._search_suggestions").hide();
    $("._search_suggestions").empty();
  });

  var timer;
  $(document).on("keyup", "._search_input", function () {
    clearInterval(timer);
    timer = setTimeout(() => {
      var search = $(this).val();

      if (search.length >= 2) {
        $.ajax({
          type: "GET",
          url: "/search",
          data: { search: search },
          success: function (data) {
            $("._search_suggestions").show();
            $("._search_suggestions").html(data);
          },
        });
      }
    }, 500);

    if ($(this).val().length > 0) {
      $(".searchIcon").hide();
      $(".cancelSearchIcon").show();
    }
  });

  $("._success_modal").on("hidden.bs.modal", function () {
    window.location.href = "/";
  });
  $("._contacts_modal_trigger").on("tap", function () {
    $("._contacts_modal").modal("show");
  });
  $("._showcase_prev").on("tap", function () {
    $(".owl-prev").trigger("tap");
  });
  $("._showcase_next").on("tap", function () {
    $(".owl-next").trigger("tap");
  });

  /* !!!!!!!!!!!! REDESIGN !!!!!!!!!!!!!!!*/

  // карусель под хедером
  let pluses = $(".main-pluses");
  if (pluses.length) {
    try {
      pluses.carousel({
        interval: 3000,
        dots: false,
        loop: true,
        nav: true,
      });
    } catch (e) { }
  }
  setTimeout(function () {
    $(".carousel-indicators .carousel-item:nth-child(2)").trigger("tap");
    $(".carousel-indicators li:nth-child(2)").trigger("tap");
    $(".main-pluses").carousel("cycle");
  }, 3000);

  function showProperCarousel() {
    let windowWidth = $(window).width();
    let windowHeight = $(window).height();
    let type = windowWidth > windowHeight ? "horizontal" : "vertical";
    $(".main-carousel-container").hide();

    $(".main-carousel-container." + type).show();
    let typeUcfirst = type.charAt(0).toUpperCase() + type.slice(1);

    if ($(`#mainSlides${typeUcfirst}Carousel > .slide`).length > 1) {
      $(`#mainSlides${typeUcfirst}Carousel > .slide:gt(0)`).hide();
      setInterval(function () {
        $(`#mainSlides${typeUcfirst}Carousel > .slide:first`)
          .fadeOut(1200)
          .next()
          .fadeIn(1200)
          .end()
          .appendTo(`#mainSlides${typeUcfirst}Carousel`);
      }, 5000);
    }
  }

  showProperCarousel();

  // скрывать каталог при нажатии вне него
  $("body").on("tap", function () {
    $(".header .sidebarMenu").removeClass("active");
    $(".header .catalog-button").removeClass("active");
    $(".menu-overlay").removeClass("active");
    $("body").removeClass("overflow-hidden");
  });

  // добавление в корзину
  $(document).on(
    "tap",
    ".product-card .add-to-cart,.product-desc .add-to-cart, .item-add-to-cart",
    function (event) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      if (!$(this).hasClass("_remove_from_cart")) {

        let product_id = $(this)
          .parents(".product")
          .data("product-id");

        let qty = $(".qty-selector input").val() || 1;

        let eventPayload = {
          product_id: product_id,
          qty: qty,
        };

        let e = new CustomEvent("cart:add", {
          detail: eventPayload,
        });

        document.dispatchEvent(e);
      } else {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        $(this).removeClass("added");
        $(this).removeClass("_remove_from_cart");

        let productId = $(this)
          .parents(".product")
          .data("product-id");

        if (!productId) {
          return;
        }

        let e = new CustomEvent("cart:remove", {
          detail: productId,
        });

        document.dispatchEvent(e);

        return false;
      }

      return false;
    }
  );
  $(document).on("tap", ".product-info .add-to-cart", function (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    if (!$(this).hasClass("added")) {

      let product_id = $(".product-page").data("product-id");

      const targetElement = event.target;
      const loader = document.createElement('div');
      $(loader).addClass('loader');
      $(targetElement).addClass('loading');
      $(targetElement).append(loader);

      setTimeout(() => {
        $(loader).remove();
        $(targetElement).removeClass('loading');
        $(targetElement).addClass('_remove_from_cart');
        $(targetElement).addClass('added');
      }, 1250);

      let qty = $(".qty-selector input").val() || 1;

      let eventPayload = {
        product_id: product_id,
        qty: qty,
      };

      let e = new CustomEvent("cart:add", {
        detail: eventPayload,
      });

      document.dispatchEvent(e);
    } else {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      $(this).removeClass("added");
      $(this).removeClass("_remove_from_cart");

      let productId = $(".product-page").data("product-id");

      if (!productId) {
        return;
      }

      let e = new CustomEvent("cart:remove", {
        detail: productId,
      });

      document.dispatchEvent(e);

      return false;
    }

    return false;
  });

  // удаление товара из корзины
  $(document).on(
    "tap",
    ".add-to-cart._add_to_cart.added._remove_from_cart",
    function (event) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();

      $(this).removeClass("added");
      $(this).removeClass("_remove_from_cart");

      let productId = $(this)
        .parents(".product")
        .data("product-id");

      if (!productId) {
        return;
      }

      let e = new CustomEvent("cart:remove", {
        detail: productId,
      });

      document.dispatchEvent(e);

      return false;
    }
  );

  // изменение количества на странице товара
  $(".product-desc .qty-selector .fa").on("tap", function () {
    let input = $(this).siblings("input");
    let val = input.val();

    if ($(this).hasClass("fa-minus")) {
      if (val == 1) {
        return;
      }

      input.val(val - 1);
    }

    if ($(this).hasClass("fa-plus")) {
      input.val(val - 1 + 2); // cast to int
    }
  });

  // открытие мобильного меню
  $(".mobile-menu .catalog-button").on("tap", function (e) {
    e.preventDefault();
    e.stopPropagation();

    if ($(this).hasClass("open")) {
      $(".catalog-mobile").removeClass("open");
      $(this).removeClass("open");
      $(".subcategories").removeClass("open");
      $("#app")[0].removeEventListener("touchmove", freezeVp);
    } else {
      $(".catalog-mobile").addClass("open");
      $(this).addClass("open");

      $("#app")[0].addEventListener("touchmove", freezeVp, { passive: false });
    }
  });

  $(".search-widget .menu-button").on("tap", function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();

    if ($(this).hasClass("open")) {
      $(this).removeClass("open");
      $(".mobile-menu").addClass("not-visible");
      $("body").removeClass("mobile-search-and-menu-visible");
      $(".mobile-menu .catalog-button").trigger("tap");
    } else {
      $(this).addClass("open");
      $("body").addClass("mobile-search-and-menu-visible");
      $(".mobile-menu").removeClass("not-visible");
      $(".mobile-menu .catalog-button").trigger("tap");
      //
      // let searchWidgetBottom = parseInt($('.search-widget').attr('style').replace('top:', '').replace('px', '')) + 64; //search widget height
      // let mobileMenuHeight = $('.mobile-menu').height();
      //
      // $('.catalog-mobile.open').attr('style', 'top:' + (searchWidgetBottom + mobileMenuHeight) + 'px');
      // $('.mobile-menu').attr('style', 'top: ' + searchWidgetBottom + 'px');
    }
  });

  $(document).on("tap", ".catalog-button", function () {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(".menu-overlay").removeClass("active");
      $(this)
        .siblings(".sidebarMenu")
        .removeClass("active");
      $("body").removeClass("overflow-hidden");
    } else {
      $(this).addClass("active");
      $(".menu-overlay").addClass("active");
      $(this)
        .siblings(".sidebarMenu")
        .addClass("active");

      $("body").addClass("overflow-hidden");
    }
  });

  // открытие субкатегории
  $(".catalog-mobile .sidebarItem__text").on("tap", function (e) {
    e.preventDefault();
    e.stopPropagation();

    $(this)
      .siblings(".subcategories")
      .addClass("open");
  });

  // закрытие субкатегории
  $(".catalog-mobile .subcategories .back-to-catalog").on("tap", function (e) {
    e.preventDefault();
    e.stopPropagation();

    $(".subcategories").removeClass("open");
  });

  //
  $(".sidebarMenu .items,.subcategories").on("scroll", function (e) {
    e.stopPropagation();
    e.preventDefault();
  });

  if (
    ["/login", "/register", "/reset"].indexOf(window.location.pathname) > -1
  ) {
    $("body").addClass("overflow-hidden");
  }

  $(document).on("tap", ".load-more:not([disabled])", function () {
    let link = $(this).data("link");

    if (!link) {
      return;
    }

    let containerSelector = "#" + $(this).data("container");

    axios.get(link).then((response) => {
      let nextPageHtml = $(response.data)
        .find(containerSelector)
        .html();
      let nextPageLink = $(response.data)
        .find(".load-more")
        .data("link");

      if (nextPageLink && nextPageLink != link) {
        $(this).data("link", nextPageLink);
      } else {
        $(this).attr("disabled", "disabled");
      }

      $(containerSelector).append(nextPageHtml);

      setTimeout(() => {
        let currency = localStorage.getItem("currency");
        $(window).trigger("currency:change_no_emit", currency);
      }, 50);
    });
  });

  $(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
      $(".load-more:not([disabled])").trigger("tap");
    }
  });

  $(document).on("click tap", "._order-request", function () {
    $("#openOrderRequestModal").trigger("click");
  });

  $(document).on("tap", "._catalog", function () {
    if ($(window).width() > 768) {
      $([document.documentElement, document.body]).animate(
        {
          scrollTop: 0,
        },
        700,
        function () {
          $(".catalog-button").trigger("tap");
          $("body").addClass("overflow-hidden");
        }
      );
    } else {
      // $('.mobile-menu .catalog-button').trigger('tap');
      $(".search-widget .menu-button").trigger("tap");
    }
  });

  // rotating headers
  let visibleElementIndex = 1;

  if ($(window).width() > 768) {
    $(`.grey-bar .rotating .callback`).remove();
  }

  $(
    `.grey-bar .rotating > span:not(:nth-child(${visibleElementIndex}))`
  ).hide();

  setInterval(() => {
    let prevVisibleElementIndex = visibleElementIndex;

    if (
      $(`.grey-bar .rotating span:nth-child(${visibleElementIndex})`).next()
        .length > 0
    ) {
      visibleElementIndex += 1;
    } else {
      visibleElementIndex = 1;
    }

    // $(`.grey-bar .rotating span:not(:nth-child(${visibleElementIndex}))`).fadeToggle(400);
    $(`.grey-bar .rotating span:nth-child(${visibleElementIndex})`).fadeIn(400);
    $(`.grey-bar .rotating span:nth-child(${prevVisibleElementIndex})`).fadeOut(
      400
    );
    // $('.grey-bar .rotating > .callback').fadeToggle(400);
  }, 2000);

  $(document).on("tap", ".rotating", function () {
    $("#callbackMobile").modal({
      show: true,
      backdrop: "static",
    });

    $("#callbackMobile").on("touchmove", function (e) {
      e.preventDefault();
    });
  });

  function freezeVp(e) {
    e.preventDefault();
  }

  $(document).on("touchmove", ".no-scroll", function (e) {
    e.preventDefault();
  });

  $(document).on("tap", "._search", function () {
    if ($(window).width() > 768) {
      $([document.documentElement, document.body]).animate(
        {
          scrollTop: 0,
        },
        700,
        function () {
          $(".header .search-widget input").focus();
        }
      );
    } else {
      $([document.documentElement, document.body]).animate(
        {
          scrollTop: 0,
        },
        700
      );
    }
  });


  // order modal
  $('#orderToggle').on('click', function (event) {
    event.preventDefault();
    $('#orderModal').addClass('active');
    $('#blocker').addClass('active-order');
    $('body').addClass('body-fixed');
  });
  $('#orderClose').on('click', function (event) {
    event.preventDefault();
    $('#orderModal').removeClass('active');
    $('#blocker').removeClass('active-order');
    $('body').removeClass('body-fixed');
  });

  // body fixed on search event
  const searchInput = document.getElementById('search');
  searchInput.addEventListener('keyup', () => {
    if (searchInput.value.length > 0) {
      $('body').addClass('body-fixed');
      $('.blocker').addClass('active');
    } else {
      $('body').removeClass('body-fixed');
      $('.blocker').removeClass('active');
    }
  });

  // open request modal on click
  $('#openOrderModal').on('click', function(event) {
    event.preventDefault();
    const button = $('#orderToggle');
    
    if (!button) {
      return;
    }
    button.trigger('click');
  });

});
