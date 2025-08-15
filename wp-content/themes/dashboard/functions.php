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

// Enque custom styles
function dashboard_enqueue_styles() {
    $styles = [
        'custom-dashboard'    => '/assets/css/dashboard.css',
        'dashboard-custom-login' => '/assets/css/custom-login.css',
    ];

    foreach ($styles as $handle => $path) {
        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . $path,
            array(),
            filemtime(get_template_directory() . $path)
        );
    }
}
add_action('wp_enqueue_scripts', 'dashboard_enqueue_styles');
