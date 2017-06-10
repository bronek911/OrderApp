$(function () {
    $('.product_form').change(function (event) {
        if ($(this).hasClass('old_product') && $(this).val() !== 'null') {

        } else if ($(this).val() !== 'null') {
            var $copy = $(this).parent().parent().parent().clone(true);
            $('#product-holder').append($copy);
        } else if ($(this).val() === 'null') {
            $(this).parent().parent().parent().remove();
        }
    });



    $('.thumbnail').on('click', function () {

        var thumbnail = $(this);
        $('.modal-title').text(thumbnail.data('product-name'));

        $('#product-id').attr('value', thumbnail.data('product-id'));

        $('#radio-price').text(thumbnail.data('product-price').toFixed(2));
        $('#radio1').attr('value', thumbnail.data('product-price'));

        $('#radio-price-kg').text(thumbnail.data('product-price-kg').toFixed(2));
        $('#radio2').attr('value', thumbnail.data('product-price-kg'));

    });


    $('#addProduct').on('click', function () {

        var ownPrice = $('#radio-price-own').val();
        $('#radio3').attr('value', ownPrice);

        var name = $('.modal-title').text();
        var quantity = $('#product-quantity').val();
        var price = $('input[name=radio]:checked', '#addProductForm').val();
        var unit = $('input[name=radio-unit]:checked', '#addProductForm').val();
        var sum = quantity * price;
        var id = $('#product-id').val();

        var $thumbnail = $('.thumbnail[data-product-id=' + id + ']');
        $thumbnail.find('.caption .quantity').text(quantity);
        $thumbnail.find('.caption .price').text(sum.toFixed(2));

        if ($("#receipt").find("." + name).length === 0) {

            var newListedProduct = $("" +
                "<tr class='" + name + "'>" +
                "<td class='name'>" + name + "<input type='hidden' name='product[]' value='" + name + "'></td>" +
                "<td class='price'>" + price + " zł<input type='hidden' name='price[]' value='" + price + "'></td>" +
                "<td class='quantity'>" + quantity + "<input type='hidden' name='quantity[]' value='" + quantity + "'></td>" +
                "<td class='sum'>" + sum.toFixed(2) + " zł<input type='hidden' name='sum[]' value='" + sum + "'></td>" +
                "</tr>");

            var listSum = $('#productsListSum');
            listSum.before(newListedProduct);

        } else {

            var productRow = $("#receipt").find("." + name);
            productRow.find(".name").html(name + "<input type='hidden' name='product[]' value='" + name + "'>");
            productRow.find(".price").html(price + " zł<input type='hidden' name='price[]' value='" + price + "'>");
            productRow.find(".quantity").html(quantity + "<input type='hidden' name='quantity[]' value='" + quantity + "'>");
            productRow.find(".sum").html(sum.toFixed(2) + " zł<input type='hidden' name='sum[]' value='" + sum + "'>");

        }

    });


});