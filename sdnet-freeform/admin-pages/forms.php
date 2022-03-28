<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// menu_page_url('sdnet_freeform_forms');

if ( isset( $_GET['edit'] ) ) {
    if ( $_GET['edit'] === 'new' ) {
        $post = [
            'post_type' => 'sdnet_freeform_form',
            'post_name' => '',
            'post_title' => '',
            'post_content' => json_encode([
                'visitor_email_name' => 'visitor_email',
                'subject_name' => 'subject',
                'format' => [
                    [
                        'type' => 'input',
                        'name' => 'visitor_name',
                        'label' => 'Your Name:',
                        'inputType' => 'text',
                    ],
                    [
                        'type' => 'input',
                        'name' => 'visitor_email',
                        'label' => 'Your Email Address:',
                        'inputType' => 'email',
                    ],
                    [
                        'type' => 'input',
                        'name' => 'subject',
                        'label' => 'Subject:',
                        'inputType' => 'text',
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'content',
                        'label' => 'Message:',
                    ],
                ],
                'recaptcha' => [
                    'enabled' => false,
                    'site_key' => '',
                    'secret_key' => '',
                    'lazy_load' => true,
                ],
                'validation' => [
                    'email_format' => true,
                    'email_domain' => true,
                    'recaptcha' => true,
                ]
            ]),
        ];
        $post = (object) $post;
        include __DIR__ . '/inc/forms.edit.php';
    } else {
        $post = get_post( $_GET['edit'] );
        if ( $post && $post?->post_type === 'sdnet_freeform_form' ) {
            include __DIR__ . '/inc/forms.edit.php';
        } else {
            echo '<h2>Post cannot be edited</h2><p>Post ID not valid.</p>';
        }
    }
} else {
    if ( isset( $_GET['remove'] ) )
        include __DIR__ . '/inc/forms.remove.php';
    include __DIR__ . '/inc/forms.list.php';
}
