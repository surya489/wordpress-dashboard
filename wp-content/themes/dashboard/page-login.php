<?php
/* 
Template Name: Custom Login Page
*/

// Redirect if logged in
if ( is_user_logged_in() ) {
    wp_redirect( site_url('/dashboard') );
    exit;
}

global $wpdb;

// Handle login form
if ( isset($_POST['custom_login_nonce']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action') ) {
    
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];

    // Check if user exists in DB
    $user_obj = get_user_by('login', $username);
    if ( !$user_obj ) {
        $user_obj = get_user_by('email', $username); // allow email login too
    }

    if ( !$user_obj ) {
        $error_message = "User does not exist in the system.";
    } else {
        // Authenticate user
        $creds = array(
            'user_login'    => $user_obj->user_login,
            'user_password' => $password,
            'remember'      => isset($_POST['remember']),
        );

        $user = wp_signon($creds, false);

        if ( !is_wp_error($user) ) {
            
            // Allowed roles (from custom DB field)
            $allowed_roles = array('admin', 'manager', 'employee');
            $user_custom_role = get_user_meta($user->ID, 'custom_role', true);

            if (in_array($user_custom_role, $allowed_roles)) {
                wp_redirect( site_url('/dashboard') );
            } else {
                wp_logout();
                $error_message = "You do not have permission to access this dashboard.";
            }
            exit;
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}

get_header();
?>

<div class="login-container">
    <h2>Dashboard Login</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="error"><?php echo esc_html($error_message); ?></div>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="remember-container">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="margin: 0; cursor: pointer;">Remember Me</label>
        </div>
        <button type="submit" class="login-btn">Login</button>
    </form>
</div>

<?php get_footer(); ?>
