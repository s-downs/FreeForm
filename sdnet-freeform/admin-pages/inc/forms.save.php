<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !isset( $_POST['_wpnonce'] ) )
    return;

$nonce = wp_verify_nonce( $_POST['_wpnonce'], 'sdnet_freeform_form_edit_' . sanitize_text_field( $_GET['edit'] ) );

if ( !$nonce ) {
    return;
}

$formDetail = [];
// TODO: Check format...
$formDetail['format'] = sanitize_text_field( $_POST['form_fields'] );
$formDetail['recaptcha'] = [
    'enabled' => isset( $_POST['recaptcha_enabled'] ) && $_POST['recaptcha_enabled'] === 'yes',
    'site_key' => isset( $_POST['recaptcha_site_key'] ) ? sanitize_text_field( $_POST['recaptcha_site_key'] ) : '',
    'secret_key' => isset( $_POST['recaptcha_secret_key'] ) ? sanitize_text_field( $_POST['recaptcha_secret_key'] ) : '',
    'lazy_load' => isset( $_POST['recaptcha_lazy_load'] ) && $_POST['recaptcha_lazy_load'] === 'yes',
];
$formDetail['validation'] = [
    'recaptcha' => isset( $_POST['validate_recaptcha'] ) && $_POST['validate_recaptcha'] === 'yes',
    'email_format' => isset( $_POST['validate_email_format'] ) && $_POST['validate_email_format'] === 'yes',
    'email_domain' => isset( $_POST['validate_email_domain'] ) && $_POST['validate_email_domain'] === 'yes',
];
$post->post_content = sanitize_text_field( json_encode( $formDetail ) );

$post->post_title = sanitize_text_field( $_POST['form_title'] );
$post->post_name = sanitize_text_field( $_POST['form_name'] );

$id = 'new';
if ( $_GET['edit'] === 'new' ) {
    $post->post_status = 'publish';
    $id = wp_insert_post( $post, true );
} else {
    $id = wp_update_post( $post );
}
wp_redirect( menu_page_url('sdnet_freeform_forms') . '&edit=' . $id );
exit;
