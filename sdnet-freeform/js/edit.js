(function(){

    let checkReCAPTCHA = () => {
        let opt = jQuery('#sdnet_freeform_form_recaptcha .inside #options');
        if ( jQuery('#sdnet_freeform_form_recaptcha .inside #recaptcha-on-off').is(':checked' ) )
            opt.show();
        else
            opt.hide();
    }

    checkReCAPTCHA();
    jQuery('#sdnet_freeform_form_recaptcha .inside #recaptcha-on-off').click(()=>{
        checkReCAPTCHA();
    });

})();
