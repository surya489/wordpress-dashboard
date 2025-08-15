<?php
function dashboard_enqueue_styles() {
    wp_enqueue_style(
        'dashboard-custom-login',
        get_template_directory_uri() . '/assets/css/custom-login.css',
        array(),
        filemtime(get_template_directory() . '/assets/css/custom-login.css')
    );
}
add_action('wp_enqueue_scripts', 'dashboard_enqueue_styles');
