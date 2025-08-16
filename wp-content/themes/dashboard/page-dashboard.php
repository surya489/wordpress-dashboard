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

<div class="dashboard-wrapper">

    <!-- Sidebar (static placeholder) -->
    <aside class="sidebar">
        <div class="logo">My Dashboard</div>
        <nav>
            <ul>
                <li><a href="<?php echo site_url('/dashboard'); ?>" class="<?php echo is_active_sidebar_link(site_url('/dashboard')); ?>">Dashboard</a></li>
                <li><a href="<?php echo site_url('/projects'); ?>" class="<?php echo is_active_sidebar_link(site_url('/projects')); ?>">Projects</a></li>
                <li><a href="<?php echo site_url('/calendar'); ?>" class="<?php echo is_active_sidebar_link(site_url('/calendar')); ?>">Calendar</a></li>
                <?php if($user_role === 'admin' || $user_role === 'manager') : ?>
                    <li><a href="<?php echo site_url('/settings'); ?>" class="<?php echo is_active_sidebar_link(site_url('/settings')); ?>">Settings</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <h2>Dashboard</h2>
            </div>

            <div class="topbar-right">
                <div class="user-menu">
                    <img src="<?php echo get_avatar_url($current_user->ID); ?>" alt="Avatar" class="user-avatar">
                    <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
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
        </div>

        <!-- Widgets Grid -->
        <div class="widgets-grid">
            <div class="widget-card">📊 Stats Widget</div>
            <div class="widget-card">🗂 Projects Widget</div>
            <div class="widget-card">📅 Calendar Widget</div>
            <?php if($user_role === 'admin' || $user_role === 'manager') : ?>
                <div class="widget-card">⚙ Settings Widget</div>
                <div class="widget-card attendence-chart">
                    <h3>Attendance</h3>
                    <canvas id="attendanceChart" width="200" height="200"></canvas>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php get_footer(); ?>
