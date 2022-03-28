<?php

if ( ! defined( 'ABSPATH' ) ) exit;

$page = $_GET['p'] ?? 1;
$pages = ceil( wp_count_posts( 'sdnet_freeform_res' )->publish / 50 );

$responses = [];
$responses = get_posts([
    'post_type' => 'sdnet_freeform_res',
    'order' => 'DESC',
    'order_by' => 'date',
    'paged' => $page,
    'posts_per_page' => 50,
    'post_status' => 'publish',
]);

echo '<h1>FreeForm Responses</h1>';
echo '<div></div>';
echo '<div>';

foreach ( $responses as $response ) {
    echo '<div style="min-height: 4rem; border: 1px solid lightgrey; padding: 0.25rem 0.5rem; position: relative;">';
    echo '<p style="margin: 0; margin-bottom: 0.3rem; padding: 0; font-size: 1.5rem;"><a style="color: black;" href="' . menu_page_url('sdnet_freeform_responses', false ) . '&view=' . $response->ID . '">';
    echo $response->post_title == '' ? '<em style="color: grey; text-decoration-color: grey;">&lt;No Subject&gt;</em>' : $response->post_title;
    echo '</a>';
    echo '<p style="margin: 0;">' . $response->post_date . ' - ' . 'Contact Form' . '</p>';
    echo '</div>';
}

echo '</div>';

if ( $pages > 1 ) {
    echo '<div style="text-align: center;">';
    echo 'Pages: ';
    if ( $page > 6 )
        echo ' <a href="' . menu_page_url( 'sdnet_freeform_responses', false ) . '&p=1">1</a> ';
    if ( $page > 7 )
        echo ' ... ';
    for ( $i=max( 1, $page-5 ); $i<=min( $pages, $page+5 ); $i++ ) {
        if ( $i != $page ) {
            echo ' <a href="' . menu_page_url( 'sdnet_freeform_responses', false ) . '&p=' . $i . '">' . $i . '</a> ';
        } else {
            echo '<a>' . $i . '</a>';
        }
    }
    if ( $page < $pages - 6 )
        echo ' ... ';
    if ( $page < $pages - 5 )
        echo ' <a href="' . menu_page_url( 'sdnet_freeform_responses', false ) . '&p=' . $pages . '">' . $pages . '</a> ';

    echo '</div>';
}

echo '<style>#wpcontent { margin-right: 16px; }</style>';
