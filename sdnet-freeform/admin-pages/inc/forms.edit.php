<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !isset( $post ) ) {
    return;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
    include __DIR__ . '/forms.save.php';

$post_content = json_decode($post->post_content);
$post_content->format = json_encode( $post_content->format );
$post_content->recaptcha = (object) $post_content->recaptcha;
$post_content->validation = (object) $post_content->validation;


?>
<form action="#" method="post" class="sdnet_freeform_form">
    <h1>Editing FreeForm Form</h1>
    <?php wp_nonce_field('sdnet_freeform_form_edit_' . sanitize_text_field( $_GET['edit'] ) ); ?>
    <label>
        <p>Form Name:</p>
        <input type="text" name="form_title" value="<?php echo $post->post_title; ?>">
    </label>
    <label>
        <p>Form Slug:</p>
        <input type="text" name="form_name" value="<?php echo $post->post_name; ?>">
    </label>
    <input type="hidden" name="form_fields" id="sdnet_form_fields" value='<?php echo str_replace( "'", "&lquo;", $post_content->format ); ?>'>
    <p>Form Fields</p>
    <div class="sdnet_form_fields_ui">
        <div class="field_list">&nbsp;<br/><br/></div>
        <div class="field_options">
            <div>
                <p class="small">Add:</p>
                <p class="centered">
                    <button class="icon-button" id="add-input-type-input">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-textarea-resize half-height" viewBox="0 0 16 16">
                            <path d="M0 4.5A2.5 2.5 0 0 1 2.5 2h11A2.5 2.5 0 0 1 16 4.5v7a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 0 11.5v-7zM2.5 3A1.5 1.5 0 0 0 1 4.5v7A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 13.5 3h-11zm10.854 4.646a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708l3-3a.5.5 0 0 1 .708 0zm0 2.5a.5.5 0 0 1 0 .708l-.5.5a.5.5 0 0 1-.708-.708l.5-.5a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </button>
                    <button class="icon-button" id="add-input-type-textarea">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-textarea-resize" viewBox="0 0 16 16">
                            <path d="M0 4.5A2.5 2.5 0 0 1 2.5 2h11A2.5 2.5 0 0 1 16 4.5v7a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 0 11.5v-7zM2.5 3A1.5 1.5 0 0 0 1 4.5v7A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 13.5 3h-11zm10.854 4.646a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708l3-3a.5.5 0 0 1 .708 0zm0 2.5a.5.5 0 0 1 0 .708l-.5.5a.5.5 0 0 1-.708-.708l.5-.5a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </button>
                </p>
                <p class="small">Options:</p>
                <div class="selected-options">&nbsp;</div>
            </div>
        </div>
    </div>
    <div class="flex">
        <div class="flex-50 sdnet_form_recaptcha">
            <p>ReCAPTCHA</p>
            <label>
                <input type="checkbox" name="recaptcha_enabled" value="yes" <?php echo $post_content->recaptcha->enabled ? 'checked' : ''; ?>> Enable ReCAPTCHA
            </label>
            <label>
                <p class="small">Site Key:</p>
                <input type="text" name="recaptcha_site_key" value="<?php echo $post_content->recaptcha->site_key; ?>">
            </label>
            <label>
                <p class="small">Secret Key:</p>
                <input type="text" name="recaptcha_secret_key" value="<?php echo $post_content->recaptcha->secret_key; ?>">
            </label>
            <label>
                <input type="checkbox" name="recaptcha_lazy_load" value="yes" <?php echo $post_content->recaptcha->lazy_load ? 'checked' : ''; ?>> Enable "lazy loading"
            </label>
        </div>
        <div class="flex-50 sdnet_form_validation">
            <p>Validation</p>
            <label>
                <input type="checkbox" name="validate_recaptcha" value="yes" <?php echo $post_content->validation->recaptcha ? 'checked' : ''; ?>> Validate ReCAPTCHA (if enabled)
            </label>
            <label>
                <input type="checkbox" name="validate_email_format" value="yes" <?php echo $post_content->validation->email_format ? 'checked' : ''; ?>> Validate email format
            </label>
            <label>
                <input type="checkbox" name="validate_email_domain" value="yes" <?php echo $post_content->validation->email_domain ? 'checked' : ''; ?>> Validate email domain
            </label>
        </div>
    </div>
    <div id="save-button-container">
        <input type="submit" value="Save" id="save-button">
    </div>
</form>

<?php

$url = plugin_dir_url( __DIR__ );
$css = $url . 'css/edit.css';
$js  = $url . 'js/edit.js';

echo "<link rel='stylesheet' href='$css'>";
echo "<script src='$js'></script>";
