<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function sdnet_freeform_shortcode ( $attr, $content ) {
    $form = null;
    if ( isset( $attr['slug'] ) )
        $form = sdnet_freeform_getFormDetailFromSlug( $attr['slug'] );
    if ( isset( $attr['id'] ) )
        $form = sdnet_freeform_getFormDetailFromID( $attr['id'] );

    if ( !$form )
        return '';

    wp_enqueue_style( 'sdnet_freeform_form_style', plugin_dir_url( __FILE__ ) . 'css/shortcode.css' );

    ob_start();

    echo '<form method="post" action="#" class="sdnet_freeform_form">';

    wp_nonce_field('sdnet_freeform_' . $form->post_name );
    echo '<input type="hidden" name="honey" value="">';


    if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
        sdnet_freeform_handle_submission( $form );

    $formDetail = json_decode( $form->post_content, true );

    $format = preg_replace( "/\\\+\"/", '"', $formDetail['format'] );
    $format = json_decode( $format );

    foreach( $format as $field ) {
        if ( $field->label && $field->label !== '' )
            echo '<label><div>' . sanitize_text_field( $field->label ) . '</div>';


        if ( $field->type === 'input' ) {
            echo '<input';
            if ( $field->name && $field->name !== '' )
                echo ' name="' . sanitize_text_field( $field->name ) . '"';
            if ( isset( $field->inputType ) && $field->inputType !== '' )
                echo ' type="' . sanitize_text_field( $field->inputType ) . '"';
            echo '>';
        }
        if ( $field->type === 'textarea' ) {
            echo '<textarea';
            if ( $field->name && $field->name !== '' )
                echo ' name="' . sanitize_text_field( $field->name ) . '"';
            echo '></textarea>';
        }


        if ( $field->label && $field->label !== '' )
            echo '</label>';
    }

    if ( $formDetail['recaptcha']['enabled'] && !$formDetail['recaptcha']['lazy_load'] ) {
        echo '<div class="g-recaptcha-container"><div><div class="g-recaptcha" data-sitekey="' . sanitize_text_field( $formDetail['recaptcha']['site_key'] ) . '"></div></div><input type="submit"></div>';
        wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
    } elseif ( !$formDetail['recaptcha']['enabled'] ) {
        echo '<div class="g-recaptcha-container"><input type="submit"></div>';
    } else {
        echo '<div class="g-recaptcha-container" data-sitekey="' . sanitize_text_field( $formDetail['recaptcha']['site_key'] ) . '"><button class="recaptcha-load">Load ReCAPTCHA<br/><span class="small">[I AM NOT A ROBOT]</span></button></div>';
        wp_enqueue_script( 'sdnet_freeform_form_script', plugin_dir_url( __FILE__ ) . 'js/shortcode.js', ['jquery'] );
    }

    echo '</form>';

    return ob_get_clean();
}

function sdnet_freeform_getFormDetailFromSlug ( $slug ) {
    $posts = get_posts( [
        'post_name' => $slug,
        'post_type' => 'sdnet_freeform_form',
    ] );
    if ( $posts && $posts[0]->post_status === 'publish' )
        return $posts[0];
    return null;
}

function sdnet_freeform_getFormDetailFromID ( $id ) {
    $post = get_post( $id );
    if ( $post && $post->post_status === 'publish' && $post->post_type == 'sdnet_freeform_form' )
        return $post;
    return null;
}

function sdnet_freeform_handle_submission ( $form ) {
    if ( !isset( $_POST['_wpnonce'] ) || wp_verify_nonce( $_POST['_wpnonce'], 'sdnet_freeform_' . $form->ID ) ) {
        echo '<div class="form-submit-message error">There was an error submitting your form, please try again.</div>';
        return;
    }

    if ( !isset( $_POST['honey'] ) || $_POST['honey'] !== '' ) {
        echo '<div class="form-submit-message error">There was an error submitting your form, please try again.</div>';
        return;
    }

    $formDetail = json_decode( $form->post_content, true );

    $format = preg_replace( "/\\\+\"/", '"', $formDetail['format'] );
    $format = json_decode( $format );

    $responseItems = [];

    foreach ( $format as $item ) {
        if ( !isset( $item->required ) || $item->required ) {
            if ( !isset( $_POST[ $item->name ] ) ) {
                echo '<div class="form-submit-message error">There was an error submitting your form, it appeared to not be complete. Please try again.</div>';
                return;
            }
        }
        if ( isset( $_POST[ $item->name ] ) )
            $responseItems[ $item->name ] = sanitize_text_field( $_POST[ $item->name ] );
    }

    if ( $formDetail['recaptcha']['enabled'] && $formDetail['validation']['recaptcha'] ) {
        if ( !isset( $_POST['g-recaptcha-response'] ) ) {
            echo "<div class='form-submit-message error'>There was an error submitting your form, it appears you've not completed the ReCAPTCHA. Please try again.</div>";
            return;
        }
        $res = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=__1234__&response=" . $_POST['g-recaptcha-response'] . "&remoteip=" . $_SERVER['REMOTE_ADDR'] );
        $resObj = json_decode( $res );
        if ( $resObj->success !== true ) {
            echo "<div class='form-submit-message error'>There was an error submitting your form, it appears you've not completed or failed the ReCAPTCHA. Please try again.</div>";
            return;
        }
    }

    $inserts = [
        'form' => $form->ID,
        'response' => $responseItems,
    ];

    wp_insert_post( [
        'post_title' => sanitize_text_field( $_POST['subject'] ),
        'post_type' => 'sdnet_freeform_res',
        'post_content' => json_encode( $inserts ),
        'post_status' => 'publish',
    ] );

    echo '<div class="form-submit-message success">Thanks, we\'ve got that!</div>';
}