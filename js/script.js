$('.pop').click(function() {
    const product = $(this).data('product');
    $('#product').val(product);
}); 


$(document).ready(function() {
	$('#form').submit(function() { 
		if (document.form.name.value == '' || document.form.phone.value == '' ) {
			valid = false;
			return valid;
		}
		$.ajax({
			type: "POST",
			url: "mail.php",
			data: $(this).serialize()
		}).done(function() {
			$('.js-overlay-thank-you').fadeIn();
			$(this).find('input').val('');
			$('#form').trigger('reset');
		});
		return false;
	});
});

$(document).mouseup(function (e) { 
	var popup = $('.popup');
	if (e.target!=popup[0]&&popup.has(e.target).length === 0){
		$('.js-overlay-thank-you').fadeOut();
	}
});

$(function($){
	$('[name="phone"]').mask("+7(999) 999-9999");
});
