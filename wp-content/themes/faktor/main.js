jQuery(document).ready(function ($) {
  var path = new URL(window.location.href);

  /*
     Start: Sidebar Filters
     */
  // sorting on change
  $("#secondary .orderby").on("change", function () {
    path.searchParams.set($(this).attr("name"), $(this).val());
    window.location.href = path.href;
  });
  // set value to sort element
  if (path.searchParams.has("orderby")) {
    $("#secondary .orderby option").each(function () {
      $(this).removeAttr("selected");
      if (path.searchParams.get("orderby") === $(this).val()) {
        $("#secondary .orderby").val($(this).val());
      }
    });
  }

  // submit product tags
  $(document).on("change", "#productTagFilter", function () {
    if ($(this).val() === "-1") {
      path.searchParams.delete("product_tag");
    } else if (path.searchParams.has("product_tag")) {
      path.searchParams.set("product_tag", $(this).val());
    } else {
      path.searchParams.append("product_tag", $(this).val());
    }
    window.location.href = path.href;
  });

  // ordertype li on click
  $("#secondary .products-filter-list li").on("click", function () {
    window.location.href = $(this).find("a").attr("href");
  });

  // check if filter_order-type exists
  if (path.searchParams.has("product_tag")) {
    $("#productTagFilter").val(path.searchParams.get("product_tag"));
    var orderTypes = [].slice.call(
      $(
        ".woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item a"
      )
    );
    orderTypes.forEach(function (type) {
      var href = type.getAttribute("href");
      if (href.indexOf("?") > -1) {
        href += "&product_tag=" + path.searchParams.get("product_tag");
      } else {
        href += "?product_tag=" + path.searchParams.get("product_tag");
      }
      type.setAttribute("href", href);
    });
  }
  // check if orderby exists
  if (path.searchParams.has("orderby")) {
    var orderTypes = [].slice.call(
      $(
        ".woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item a"
      )
    );
    orderTypes.forEach(function (type) {
      var href = type.getAttribute("href");
      if (href.indexOf("?") > -1) {
        href += "&orderby=" + path.searchParams.get("orderby");
      } else {
        href += "?orderby=" + path.searchParams.get("orderby");
      }
      type.setAttribute("href", href);
    });
  }

  // check if ordertype exists on url
  if (path.searchParams.has("filter_order-type")) {
    var addToCartButtons = [].slice.call($(".add_to_cart_button "));
    addToCartButtons.forEach(function (button) {
      $(button).attr(
        "data-order-type-cat",
        path.searchParams.get("filter_order-type")
      );
    });
  }
  /*
     End: Sidebar Filters
     */

  // on product hover show buttons
  $(".home-products .home-product, .products .product")
    .on("mouseenter", function () {
      $(this).find(".product-buttons").fadeIn(300);
    })
    .on("mouseleave", function () {
      $(this).find(".product-buttons").fadeOut(300);
    });

  $(
    ".home-products .home-product .ajax_add_to_cart, .products .product .ajax_add_to_cart"
  ).attr("data-order_type", "Print");

  $(document).on("mouseenter", ".site-header-cart", function () {
    $(".site-header-cart .widget_shopping_cart").addClass("d-none");
  });
  $(document)
    .on("mouseenter", ".cart-contents", function () {
      $(".site-header-cart .widget_shopping_cart").addClass("woo-cart-menu");
    })
    .on("mouseleave", ".cart-contents", function () {
      $cart = $(".site-header-cart .widget_shopping_cart");
      if (!$cart.is(":hover")) {
        $cart.removeClass("woo-cart-menu");
      }
    });
  $(document).on(
    "mouseleave",
    ".site-header-cart .widget_shopping_cart",
    function () {
      $(".site-header-cart .widget_shopping_cart").removeClass("woo-cart-menu");
    }
  );
  function setCartMenuWidth() {
    $(".site-header-cart .widget_shopping_cart").css(
      "width",
      $(".site-header .site-search").width() + "px"
    );
  }
  setCartMenuWidth();

  // product accordion
  $(".product-accordion .acc-open").on("click", function () {
    $(".product-accordion .acc-open").removeClass("link");
    $(this).addClass("link");
    $acc = $(this).closest(".product-accordion");
    $acc.find(".acc-content > div").hide();
    $($(this).data("target")).show();
  });

  // set my account link on header menu
  $(".my-account-top").attr(
    "href",
    $(".storefront-handheld-footer-bar .my-account a").attr("href")
  );

  // change price position on single product page
  $(".order-type-parent").after($(".product.type-product .price"));

  // teaser title click event
  $(".teaser-title").on("click", function () {
    window.location.href = $(this).data("href");
  });

  /**
   * #pa_order-type on change
   */
  $orderType = $("#pa_order-type");

  if ($orderType.val() !== "") {
    $("#term-" + $orderType.val()).click();
  }

  $orderType.on("change", function () {
    $term = $(this).val();
    if ($term !== "") {
      $("#term-" + $term).click();
    } else {
      $(".order-type-parent input").removeAttr("checked");
    }
  });

  /**
   * set abo, name and price to abo contact form
   */
  $(".single-abo #aboProductEl").val($("#aboTitle").text().trim());
  $(".single-abo #aboPriceEl").val($("#aboPrice").data("price"));
  $(".single-abo #aboEl").val($(".single-abo").data("abo"));
});
