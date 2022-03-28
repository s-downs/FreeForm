(function(){ jQuery(document).ready(()=>{

    jQuery('.sdnet_freeform_form .recaptcha-load').click((e)=>{
        e.preventDefault();
        let cont = jQuery('.sdnet_freeform_form .g-recaptcha-container');
        cont.html('');
        cont.append('<div><div class="g-recaptcha" data-sitekey="' + cont.attr('data-sitekey') + '"></div></div>');
        cont.append('<input type="submit">');
        jQuery('body').append('<script src="https://www.google.com/recaptcha/api.js"></script>');
    });

})})()
