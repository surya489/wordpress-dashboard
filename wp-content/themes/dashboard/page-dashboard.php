<?php
/* 
Template Name: Custom Dashboard
*/

if ( !is_user_logged_in() ) {
    wp_redirect( site_url('/login') );
    exit;
}

$current_user = wp_get_current_user();
get_header();
?>

<div class="dashboard-container">
    <h1>Welcome, <?php echo esc_html( $current_user->display_name ); ?> 👋</h1>
    <p>Your role: <?php echo implode(', ', $current_user->roles); ?></p>

    <div class="widgets-grid">
        <div class="widget-card">📊 Stats Widget</div>
        <div class="widget-card">🗂 Projects Widget</div>
        <div class="widget-card">📅 Calendar Widget</div>
        <div class="widget-card">⚙ Settings Widget</div>
    </div>
</div>

<?php get_footer(); ?>
