<?php
if (!defined('ABSPATH')) exit;

/**
 * Fetch custom role stored in user meta (admin/manager/employee)
 */
function mdw_get_custom_role($user_id) {
    $role = get_user_meta($user_id, 'custom_role', true);
    return $role ?: 'employee';
}

/**
 * Simple context detector: only load assets on /dashboard page or when shortcode present
 */
function mdw_is_dashboard_context() {
    if (is_admin()) return false;
    if (is_page() && function_exists('get_queried_object')) {
        $p = get_queried_object();
        if (!empty($p->post_content) && has_shortcode($p->post_content, 'mdw_dashboard')) {
            return true;
        }
    }
    // fallback: check URL path
    $path = trim(parse_url(home_url(add_query_arg([], $_SERVER['REQUEST_URI'])), PHP_URL_PATH), '/');
    return ($path === 'dashboard');
}

/**
 * Tiny static menu (replace with wp_nav_menu if you want)
 */
function mdw_render_static_menu($role) {
    $items = [
        ['label' => 'Dashboard', 'url' => site_url('/dashboard')],
        ['label' => 'Projects',  'url' => site_url('/projects')],
        ['label' => 'Calendar',  'url' => site_url('/calendar')],
    ];
    if (in_array($role, ['admin','manager'], true)) {
        $items[] = ['label' => 'Settings', 'url' => site_url('/settings')];
    }

    // active class based on path
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $out  = '<nav class="mdw-nav"><ul>';
    foreach ($items as $it) {
        $path = trim(parse_url($it['url'], PHP_URL_PATH), '/');
        $active = ($current_path === $path) ? ' class="active"' : '';
        $out .= '<li><a'.$active.' href="'.esc_url($it['url']).'">'.esc_html($it['label']).'</a></li>';
    }
    $out .= '</ul></nav>';
    return $out;
}
