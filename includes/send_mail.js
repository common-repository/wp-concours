jQuery(document).ready(function($) {
	var data = {
		'action': 'olyosconc_send_test_mail',
	};

    var btn = document.getElementById("send-test-mail");
    if (btn !== null) {
        btn.addEventListener("click", function(ev) {
            ev.preventDefault();
            jQuery.post(ajax_object.ajax_url, data, function(response) {
                alert(response);
            });
        });
    }

});