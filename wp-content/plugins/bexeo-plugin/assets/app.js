jQuery(document).ready(function ($) {
  var path = new URL(window.location.href);

  $("#roles").on("change", function () {
    if ($(this).val() !== "") {
      $("#discountSubmit").removeAttr("disabled");
    } else {
      $("#discountSubmit").attr("disabled", "disabled");
    }
  });

  // go back
  $(".go-back").on("click", function (e) {
    e.preventDefault();
    window.location.reload();
  });

  $("#discountSubmit").on("click", function (e) {
    e.preventDefault();
    $valid = true;
    $form = $(this).closest("form");
    $form.find(".field-required").each(function () {
      if ($(this).val() === "") {
        $(this).css("border", "1px solid red");
        $valid = false;
      } else {
        $(this).css("border", "1px solid #7e8993");
      }
    });
    if ($valid) {
      $form[0].submit();
    }
  });

  // clonable
  $(".clone-btn").on("click", function (e) {
    e.preventDefault();
    $empty = false;
    $last = $(".clone-content").last();
    $last.find("input").each(function () {
      if ($(this).val() === "") {
        $empty = true;
      }
    });
    if ($empty) {
      return false;
    }
    $clone = $(".clone-content").first().clone();
    $clone.find("input").val("");
    $clone.appendTo(".clonable");
  });

  $(document).on("click", ".clone-remove-row", function (e) {
    e.preventDefault();
    $(this).closest(".clone-content").remove();
  });

  $(".bexeo-folder").on("click", function () {
    path.searchParams.set("folder", $(this).data("pid"));
    window.location.href = path.href;
  });

  /**
   * custom order form on submit
   */
  $("#customOrderForm").on("submit", function (e) {
    e.preventDefault();
    $form = $(this);
    $useDiffAdd = $form.find("#useDiffShippingAdd");

    $submitButton = $form.find('button[type="submit"]');
    $submitButton
      .attr("disabled", "disabled")
      .append('<i class="fas fa-spin fa-spinner"></i>');

    var data = {
      use_diff_shipping: $useDiffAdd.is(":checked"),
      products: CustomOrder.products,
      shipping_post: CustomOrder.post,
    };

    $form
      .find('.co-field[name^="billing_"], .co-field[name^="shipping_"]')
      .each(function () {
        data[$(this).attr("name")] = $(this).val();
      });

    $.ajax({
      type: "POST",
      url: "/wp-json/api/save-custom-order",
      data: data,
      success: function (data) {
        $submitButton.removeAttr("disabled").find("i.fa-spin").remove();
        $form[0].reset();

        Swal.fire({
          title: "Bestellung erfolgreich erstellt",
          icon: "success",
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText:
            '<a style="color: white; text-decoration: none" target="_blank" href="/wp-admin/post.php?post=' +
            data.data.order_id +
            '&action=edit">Bestelldetails</a>',
          confirmButtonAriaLabel: "Thumbs up, great!",
          cancelButtonText: "Fertig",
        });

        $(".co-shipping-address").empty();
        CustomOrder.restore();
        $("#addProductForm")[0].reset();
        $(".shipping-methods").empty();
      },
    });
  });

  /**
   * custom order product on change
   */
  $("#customOrderProduct").on("change", function () {
    $variations = $("#productVariations");
    $shippingMethods = $(".shipping-methods");
    $selected = $(this).find("option:selected");
    var price = $selected.data("price");

    if (!CustomOrder.post.price) {
      if ($selected.data("categories").indexOf("buecher") > -1) {
        $shippingMethods.html($("#shippingMethods").html());
      } else {
        $shippingMethods.empty();
      }
    }

    if ($selected.data("variation") !== 1) {
      $("#priceEl").val(price);
      $variations.empty();
      return;
    }

    $addProductButton = $("#addProductBtn");
    $addProductButton.attr("disabled", "disabled");
    $variations.html('<i class="fas fa-spin fa-spinner"></i>');

    $.ajax({
      type: "GET",
      url: "/wp-json/api/product-variations?product_id=" + $(this).val(),
      success: function (data) {
        var template = "";
        data.data.variations.map(function (variation) {
          template +=
            '<label class="co-radio-label"><input type="radio" name="variation_id" class="co-field" value="' +
            variation.id +
            '" data-price="' +
            variation.price +
            '"> ' +
            variation.name +
            "</label>";
        });

        $variations.html(template).removeClass("d-none");
        $addProductButton.removeAttr("disabled");

        // make first variation selected
        $variations.find('input[type="radio"]').first().click();
      },
    });
  });

  /**
   * custom order radio on click
   */
  $(document).on("click", ".co-radio-label", function () {
    var price = $(this).find("input").data("price");
    $("#priceEl").val(price);
    $("#orderItemType").val($(this).data("type"));
  });

  /**
   * Use different shipping address
   * useDiffShippingAdd on change
   */
  $("#useDiffShippingAdd").on("change", function () {
    if ($(this).is(":checked")) {
      $(".co-shipping-address").html($("#shippingTemplate").html());
    } else {
      $(".co-shipping-address").empty();
    }
  });

  /**
   * Add new product to order
   * addProductBtn on click
   */
  $("#addProductForm").on("submit", function (e) {
    e.preventDefault();

    $product = $(this).find("#customOrderProduct");
    $price = $(this).find("#priceEl");
    $qty = $(this).find("#qtyEl");
    $selected = $product.find("option:selected");

    var productName = $product.find("option:selected").text();
    var productId = $product.val();

    var variation_id = null;
    var variation_name = null;

    if ($selected.data("variation") == "1") {
      $radio = $(this).find('input[name="variation_id"]:checked');
      variation_id = $radio.val();
      variation_name = $radio.parent().text().trim();
    }

    CustomOrder.addProduct(
      $product.find("option:selected").text(),
      $product.val(),
      variation_name,
      variation_id,
      $price.val(),
      $qty.val()
    );
  });

  /**
   * shippingMethodTitle on change
   */
  $(document).on("change", "#shippingMethodTitle", function () {
    if ($(this).val() == "") {
      $("#shippingMethodPrice").val("");
    }
  });

  /**
   * save shipping method
   */
  $(document).on("click", "#saveShippingMethod", function () {
    var title = $("#shippingMethodTitle").val();
    var price = $("#shippingMethodPrice").val();
    CustomOrder.setPost(title, price);
  });

  /**
   * init datepickers
   */
  $(".air-datepicker").datepicker({
    format: "de",
  });
});

var CustomOrder = {
  products: [],
  post: {
    title: null,
    price: null,
  },
  index: 1,
  setPost: function (title, price) {
    this.post.title = title;
    this.post.price = price;
    this.updateTable();
  },
  addProduct: function (
    product_name,
    product_id,
    variation_name,
    variation_id,
    price,
    qty
  ) {
    this.products.push({
      id: this.index++,
      product_name,
      product_id,
      variation_name,
      variation_id,
      price,
      qty,
    });
    this.updateTable();
  },
  removeProduct: function (id) {
    this.products = this.products.filter(function (item) {
      return item.id !== id;
    });
    this.updateTable();
  },
  updateTable: function () {
    $table = jQuery("#orderProductsTable");
    var template = "";

    this.products.forEach(function (item) {
      template += '<tr data-id="' + item.id + '">';
      template += "<td>" + item.id + "</td>";
      template += "<td>" + item.product_name + "</td>";
      template +=
        "<td>" + (item.variation_name ? item.variation_name : "") + "</td>";
      template += "<td>" + item.price + "</td>";
      template += "<td>" + item.qty + "</td>";
      template += "<td>";
      template +=
        '<a href="javascript:void(0)" class="button" onclick="CustomOrder.removeProduct(' +
        item.id +
        ')"><i class="fas fa-times"></i></a>';
      template += "</td>";
      template += "</tr>";
    });
    $table.find("tbody").html(template);

    var total = this.calculateTotal();
    if (this.post.price) {
      $table
        .find("tfoot")
        .html(
          "<tr>" +
            "<th></th>" +
            "<th>" +
            this.post.title +
            "</th>" +
            "<th></th>" +
            "<th>" +
            this.post.price +
            "</th>" +
            "<th></th>" +
            "<th></th>" +
            "</tr>"
        );
    } else {
      $table.find("tfoot").html("");
    }

    if (this.products.length < 1) {
      $table.hide();
    } else {
      $table.show();
    }
  },
  calculateTotal: function () {
    var total = Number(this.post.price);
    this.products.forEach((item) => {
      total += Number(item.price) * Number(item.qty);
    });
    return total;
  },
  restore: function () {
    this.products = [];
    this.updateTable();
  },
};
