<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( isset( $_GET['view'] ) )
    include __DIR__ . '/inc/response.view.php';
else
    include __DIR__ . '/inc/response.list.php';