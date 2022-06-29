$('.pop').click(function() {
    const product = $(this).data('product');
    $('#product').val(product);
}); 