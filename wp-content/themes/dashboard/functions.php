<?php

// Add custom role selection in the admin user profile
add_action('show_user_profile', 'custom_user_role_field');
add_action('edit_user_profile', 'custom_user_role_field');
function custom_user_role_field($user) {
    $current_role = get_user_meta($user->ID, 'custom_role', true);
    $roles = ['admin', 'manager', 'employee'];
    ?>
    <h3>Custom Role</h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_role">Select Role</label></th>
            <td>
                <select name="custom_role" id="custom_role">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo esc_attr($role); ?>" <?php selected($current_role, $role); ?>>
                            <?php echo ucfirst($role); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Save the custom role when profile is updated
add_action('personal_options_update', 'save_custom_user_role');
add_action('edit_user_profile_update', 'save_custom_user_role');
function save_custom_user_role($user_id) {
    if (!current_user_can('edit_user', $user_id)) return false;
    update_user_meta($user_id, 'custom_role', sanitize_text_field($_POST['custom_role']));
}

function is_active_sidebar_link($link_path) {
    $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $link_path_trimmed = trim(parse_url($link_path, PHP_URL_PATH), '/');

    return $current_path === $link_path_trimmed ? 'active' : '';
}

// Enqueue Styles & Scripts
function dashboard_enqueue_assets() {
    $styles = [
        'custom-dashboard'        => '/assets/css/dashboard.css',
        'dashboard-custom-login'  => '/assets/css/custom-login.css',
        'font-awesome'            => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css'
    ];

    foreach ($styles as $handle => $path) {
        if (strpos($path, 'http') === 0) {
            wp_enqueue_style($handle, $path, [], null);
        } else {
            wp_enqueue_style(
                $handle,
                get_template_directory_uri() . $path,
                [],
                filemtime(get_template_directory() . $path)
            );
        }
    }

    // JS Files
    wp_enqueue_script(
        'chart-js',
        'https://cdn.jsdelivr.net/npm/chart.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'dashboard-charts',
        get_template_directory_uri() . '/assets/js/dashboard-charts.js',
        ['jquery', 'chart-js'],
        filemtime(get_template_directory() . '/assets/js/dashboard-charts.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'dashboard_enqueue_assets');
