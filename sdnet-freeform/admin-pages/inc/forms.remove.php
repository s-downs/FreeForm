<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !isset( $_POST['_wpnonce'] ) )
    return;

$nonce = wp_verify_nonce( $_POST['_wpnonce'], 'sdnet_freeform_remove_' . $_GET['remove'] );

if ( !$nonce )
    return;

$post = get_post( $_GET['remove'] );

if ( !$post )
    return;

if ( $post->post_type === 'sdnet_freeform_form' )
    wp_delete_post( $_GET['remove'] );
