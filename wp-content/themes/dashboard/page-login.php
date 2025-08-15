<?php
/* 
Template Name: Custom Login Page
*/

// Redirect if logged in
if ( is_user_logged_in() ) {
    wp_redirect( site_url('/login') );
    exit;
}

// Handle login form
if ( isset($_POST['custom_login_nonce']) && wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action') ) {
    $creds = array(
        'user_login'    => sanitize_user($_POST['username']),
        'user_password' => $_POST['password'],
        'remember'      => isset($_POST['remember']),
    );

    $user = wp_signon($creds, false);

    if ( !is_wp_error($user) ) {
        if ( in_array('manager', (array) $user->roles) ) {
            wp_redirect( site_url('/login') );
        } else {
            wp_redirect( site_url('/access-denied') );
        }
        exit;
    } else {
        $error_message = $user->get_error_message();
    }
}

get_header();
?>

<div class="login-container">
    <h2>Dashboard Login</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
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
