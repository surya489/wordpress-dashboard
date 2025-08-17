<?php
/**
 * Plugin Name: My Dashboard Widgets
 * Description: Build a custom dashboard page using widgets. Admins design; Managers view.
 * Version:     1.0.0
 * Author:      Jaya Surya
 * Text Domain: my-dashboard-widgets
 */

if (!defined('ABSPATH')) exit;

define('MDW_PATH', plugin_dir_path(__FILE__));
define('MDW_URL', plugin_dir_url(__FILE__));

require_once MDW_PATH . 'includes/helpers.php';
require_once MDW_PATH . 'includes/class-mdw-attendance-widget.php';
require_once MDW_PATH . 'includes/projects-cpt.php';
require_once MDW_PATH . 'includes/class-mdw-projects-widget.php';
require_once MDW_PATH . 'includes/class-mdw-calendar-widget.php';
require_once MDW_PATH . 'includes/class-mdw-project-stats-widget.php';

/**
 * Register dashboard widget area (sidebar)
 */
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Dashboard Builder Area', 'my-dashboard-widgets'),
        'id'            => 'mdw_dashboard_area',
        'description'   => __('Add dashboard widgets here. Admins design; Managers view.', 'my-dashboard-widgets'),
        'before_widget' => '<div class="mdw-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="mdw-widget__title">',
        'after_title'   => '</h3>',
    ]);

    // Register our custom widgets here
    register_widget('MDW_Attendance_Widget');
    register_widget('MDW_Calendar_Widget');
    register_widget('MDW_Projects_Widget');
    register_widget('MDW_Project_Stats_Widget');
});

/**
 * Enqueue assets (front-end only when shortcode/page used)
 */
add_action('wp_enqueue_scripts', function () {
    if (!mdw_is_dashboard_context()) return;

    // Styles
    wp_enqueue_style(
        'mdw-dashboard',
        MDW_URL . 'assets/css/dashboard.css',
        [],
        filemtime(MDW_PATH . 'assets/css/dashboard.css')
    );

    // Chart.js
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);

    // Our custom dashboard scripts
    $scripts = [
        'mdw-attendance'     => 'attendance.js',
        'mdw-project-status' => 'project-status.js',
    ];

    foreach ($scripts as $handle => $file) {
        wp_enqueue_script(
            $handle,
            MDW_URL . 'assets/js/' . $file,
            ['jquery', 'chart-js'], // common deps
            filemtime(MDW_PATH . 'assets/js/' . $file),
            true
        );
    }
});


/**
 * Shortcode to render the dashboard
 * Usage: [mdw_dashboard]
 */
add_shortcode('mdw_dashboard', function ($atts = []) {
    if (!is_user_logged_in()) {
        wp_safe_redirect(wp_login_url());
        exit;
    }

    // Only Admin/Manager can view; tweak as needed.
    $role = mdw_get_custom_role(get_current_user_id());
    if (!in_array($role, ['admin', 'manager'], true)) {
        return '<div class="mdw-notice">You do not have permission to view this dashboard.</div>';
    }

    ob_start(); ?>
    <div class="mdw-dashboard">
        <aside class="mdw-sidebar">
            <div class="mdw-logo">My Dashboard</div>
            <?php echo mdw_render_static_menu($role); ?>
        </aside>

        <main class="mdw-main">
            <div class="mdw-topbar">
                <?php $u = wp_get_current_user(); ?>
                <div class="mdw-topbar__title">Dashboard</div>
                <div class="mdw-user">
                    <img class="mdw-avatar" src="<?php echo esc_url(get_avatar_url($u->ID)); ?>" alt="">
                    <span class="mdw-user__name"><?php echo esc_html($u->display_name); ?></span>
                    <i class="fas fa-caret-down dropdown-icon"></i>
                    <ul class="user-dropdown">
                        <li>
                            <a href="<?php echo site_url('/profile'); ?>">
                                <i class="fas fa-user"></i>
                                <span class="">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('/account'); ?>">
                                <i class="fas fa-cog"></i> 
                                <span class="">Account</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo wp_logout_url(site_url('/login')); ?>">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mdw-widgets-grid">
                <?php if (is_active_sidebar('mdw_dashboard_area')) {
                    dynamic_sidebar('mdw_dashboard_area');
                } else {
                    echo '<div class="mdw-widget"><h3 class="mdw-widget__title">Add Widgets</h3><p>Go to Appearance → Widgets and add items to <strong>Dashboard Builder Area</strong>.</p></div>';
                } ?>
            </div>
        </main>
    </div>

    <?php
    return ob_get_clean();
});

/**
 * Load Font Awesome for icons
 */
add_action('wp_enqueue_scripts', function () {
    if (!mdw_is_dashboard_context()) return;
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], '6.5.0');
});

/**
 * On activation: create a "Dashboard" page with shortcode if not exists
 */
register_activation_hook(__FILE__, function () {
    // Create page only if missing
    $page = get_page_by_path('dashboard');
    if (!$page) {
        wp_insert_post([
            'post_title'   => 'Dashboard',
            'post_name'    => 'dashboard',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '[mdw_dashboard]',
        ]);
    }
});

// Limit Employees to Only Their Projects
// ============================
function mdw_restrict_projects_to_employees($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    global $pagenow;
    if ($pagenow !== 'edit.php' || $query->get('post_type') !== 'mdw_project') {
        return;
    }

    $user_id = get_current_user_id();
    $custom_role = get_user_meta($user_id, 'custom_role', true);

    // Only apply for employees
    if ($custom_role === 'employee') {
        $assigned = get_posts(array(
            'post_type'  => 'mdw_project',
            'fields'     => 'ids',
            'meta_query' => array(
                array(
                    'key'     => '_mdw_assigned_devs',
                    'value'   => '"' . $user_id . '"',
                    'compare' => 'LIKE'
                )
            )
        ));

        // Show only their assigned projects
        $query->set('post__in', $assigned ? $assigned : array(0));
    }
}
add_action('pre_get_posts', 'mdw_restrict_projects_to_employees');

add_filter('template_include', function ($template) {
    if (is_singular('mdw_project')) {
        return MDW_PATH . 'templates/single-mdw_project.php';
    }
    return $template;
});
