function sanitizeNumber(n)
{
	n = parseFloat(n.toString().replace(/[^\d\.]/, ''));
	if (isNaN(n)) {
		return 0;
	}
	return n;
}


function formatNum(n)
{
	n = n.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
	if (/\./.test(n)) {
		var tmp = n.split('\.');
		tmp[1] = tmp[1].replace(/\,/g,'');
		n = tmp.join('.');
	}
	return n;
}


function precision(calculation, types)
{
	var $prec = $('#precision'),
		precision, p = $prec.val(),
		t = types || []
		;
	if (/^\d+$/.test(p)) {
		precision = parseInt(p);
	} else {
		precision = false;
	}
	if (precision) {
		calculation = Math.round(calculation * (10 * precision)) / (10 * precision).toFixed(precision);
	} else {
		calculation = calculation.toFixed(12);
	}
	return calculation;
}


function emailOut(option)
{
	$.post('./conversion-calc.php', option, function(response) {
		console.log(response);
	}).fail(function(err) {
		console.log(err);
	});
}

var Clipb = null;
$(document).ready(function() {
	Clipb = new Clipboard('.copy-button');
	Clipb.on('success', function(e) {
		$('.copy-text').each(function() {
			$(this).html('Copied').slideDown(300).delay(5000).slideUp();
		});
	});
});
