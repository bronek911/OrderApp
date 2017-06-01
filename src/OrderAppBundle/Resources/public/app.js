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

        var quantity = $('#product-quantity').val();
        var price = $('input[name=radio]:checked', '#addProductForm').val();
        var sum = quantity * price;
        var id = $('#product-id').val();

        var $thumbnail = $('.thumbnail[data-product-id=' + id + ']');
        $thumbnail.find('.caption .quantity').text(quantity);
        $thumbnail.find('.caption .price').text(sum.toFixed(2));

        var newListedProduct = $("<tr><td></td>");

        //var newListedProduct = $("#product_row").clone(true);
        var listSum = $('#productsListSum');
        listSum.before(newListedProduct);
    });


});