<?php
/* 
Template Name: Custom Dashboard
*/

if ( !is_user_logged_in() ) {
    wp_redirect( site_url('/login') );
    exit;
}

$current_user = wp_get_current_user();
$user_role = get_user_meta($current_user->ID, 'custom_role', true);
get_header();
?>

<?php 
    echo the_content();
?>

<?php get_footer(); ?>
