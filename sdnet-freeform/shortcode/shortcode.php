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
        $label = isset($field->label) && $field->label && $field->label !== '';
        if ( $label )
            echo '<label><div>' . sanitize_text_field( $field->label ) . '</div>';


        if ( $field->type === 'input' ) {
            if ( $label ) echo sanitize_text_field( $field->label );
            echo '<input';
            sdnet_freeform_generic_attributes( $field );
            echo '>';
        }
        if ( $field->type === 'textarea' ) {
            if ( $label ) echo sanitize_text_field( $field->label );
            echo '<textarea';
            sdnet_freeform_generic_attributes( $field );
            echo '></textarea>';
        }
        if ( $field->type === 'checkbox' ) {
            echo '<input type="checkbox"';
            sdnet_freeform_generic_attributes( $field );
            echo '> ';
            if ( $label ) echo sanitize_text_field( $field->label );
        }
        if ( $field->type === 'radio' ) {
            echo '<input type="radio"';
            sdnet_freeform_generic_attributes( $field );
            echo '> ';
            if ( $label ) echo sanitize_text_field( $field->label );
        }


        if ( $label )
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

function sdnet_freeform_generic_attributes ( $field ) {
    // Type
    if ( isset( $field->inputType ) && $field->inputType !== '' )
        echo ' type="' . sanitize_text_field( $field->inputType ) . '"';
    // Name
    if ( isset( $field->name ) && $field->name !== '' )
        echo ' name="' . sanitize_text_field( $field->name ) . '"';
    // Class(es)
    if ( isset( $field->classes ) && $field->classes !== '' ) {
        echo ' class="';
        if ( gettype( $field->classes ) === 'object' || gettype( $field->classes ) === 'array' ) {
            $firstDone = false;
            foreach ( $field->classes as $class ) {
                echo $firstDone ? ' ' : '';
                echo sanitize_text_field( $class );
                $firstDone = true;
            }
        }
        if ( gettype( $field->classes ) === 'string' ) {
            echo sanitize_text_field( $field->classes );
        }
        echo '"';
    }
    // ID
    if ( isset( $field->id ) && $field->id !== '' ) {
        echo ' id="' . sanitize_text_field( $field->id ) . '"';
    }
    // Style(s)
    if ( isset( $field->style ) && $field->style !== '' ) {
        if ( gettype( $field->style ) === 'string' ) {
            echo ' style="' . sanitize_text_field( $field->style ) . '"';
        }
        if ( gettype( $field->style ) === 'object' || gettype( $field->style ) === 'array' ) {
            echo ' style="';
            $firstDone = false;
            foreach ( $field->style as $style => $value ) {
                echo $firstDone ? ' ' : '';
                echo sanitize_text_field( $style ) . ':' . sanitize_text_field( $value ) . ';';
                $firstDone = true;
            }
            echo '"';
        }
    }
    // Placeholder
    if ( isset( $field->placeholder ) && $field->placeholder !== '' ) {
        echo ' placeholder="' . sanitize_textarea_field( $field->placeholder ) . '"';
    }
    // Default (value)
    if ( isset( $field->default ) && $field->default !== '' ) {
        echo ' value="' . sanitize_text_field( $field->default ) . '"';
    }
    // Min
    if ( isset( $field->min ) && $field->min !== '' ) {
        echo ' min="' . sanitize_text_field( $field->min ) . '"';
    }
    // Max
    if ( isset( $field->max ) && $field->max !== '' ) {
        echo ' max="' . sanitize_text_field( $field->max ) . '"';
    }
}

function sdnet_freeform_label ( $field ) {

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