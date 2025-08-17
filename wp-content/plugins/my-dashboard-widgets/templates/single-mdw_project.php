<?php

get_header();

if (isset($_POST['mdw_update_project']) && (current_user_can('administrator') || current_user_can('manager'))) {
    $post_id = intval($_POST['post_id']);
    update_post_meta($post_id, '_mdw_status', sanitize_text_field($_POST['mdw_status']));
    update_post_meta($post_id, '_mdw_priority', sanitize_text_field($_POST['mdw_priority']));
    update_post_meta($post_id, '_mdw_deadline', sanitize_text_field($_POST['mdw_deadline']));
    wp_redirect(get_permalink($post_id));
    exit;
}

while (have_posts()) : the_post();

    ob_start();

    // Current logged-in user
    $current_user = wp_get_current_user();
    $role = mdw_get_custom_role($current_user->ID);

    // Permission check
    if (!in_array($role, ['admin', 'manager'], true)) {
        echo '<div class="mdw-notice">You do not have permission to view this project.</div>';
        continue;
    }

    $post_id = get_the_ID();
    $status   = get_post_meta($post_id, '_mdw_status', true) ?: 'In Progress';
    $priority = get_post_meta($post_id, '_mdw_priority', true) ?: 'High';
    $deadline = get_post_meta($post_id, '_mdw_deadline', true) ?: '';
    $devs     = get_post_meta($post_id, '_mdw_assigned_devs', true);
    ?>

    <div class="mdw-dashboard">
        <aside class="mdw-sidebar">
            <div class="mdw-logo">My Dashboard</div>
            <?php echo mdw_render_static_menu($role); ?>
        </aside>

        <main class="mdw-main">
            <div class="mdw-topbar">
                <div class="mdw-topbar__title"><?php the_title(); ?></div>
                <div class="mdw-user">
                    <img class="mdw-avatar" src="<?php echo esc_url(get_avatar_url($current_user->ID)); ?>" alt="">
                    <span class="mdw-user__name"><?php echo esc_html($current_user->display_name); ?></span>
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

            <div class="project-single">
                <h1><?php the_title(); ?></h1>
                <p><strong>Created Date:</strong> <?php echo get_the_date(); ?></p>
                <p><strong>Project Description:</strong></p>
                <div><?php the_content(); ?></div>

                <?php if ($devs): ?>
                    <h3>Assigned Developers</h3>
                    <ul>
                        <?php foreach ($devs as $dev_id):
                            $user = get_userdata($dev_id); ?>
                            <li><?php echo esc_html($user->display_name); ?> (<?php echo esc_html($user->user_email); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No developers assigned yet.</p>
                <?php endif; ?>

                <h3>Additional Info</h3>
                <?php if (current_user_can('administrator') || current_user_can('manager')): ?>
                    <form method="post">
                        <p>
                            <label><strong>Status:</strong></label>
                            <select name="mdw_status">
                                <option value="In Progress" <?php selected($status, 'In Progress'); ?>>In Progress</option>
                                <option value="Completed" <?php selected($status, 'Completed'); ?>>Completed</option>
                                <option value="On Hold" <?php selected($status, 'On Hold'); ?>>On Hold</option>
                            </select>
                        </p>
                        <p>
                            <label><strong>Priority:</strong></label>
                            <select name="mdw_priority">
                                <option value="High" <?php selected($priority, 'High'); ?>>High</option>
                                <option value="Medium" <?php selected($priority, 'Medium'); ?>>Medium</option>
                                <option value="Low" <?php selected($priority, 'Low'); ?>>Low</option>
                            </select>
                        </p>
                        <p>
                            <label><strong>Deadline:</strong></label>
                            <input type="date" name="mdw_deadline" value="<?php echo esc_attr($deadline); ?>">
                        </p>
                        <input type="hidden" name="mdw_update_project" value="1">
                        <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                        <button type="submit">Update Project</button>
                    </form>
                <?php else: ?>
                    <ul>
                        <li><strong>Status:</strong> <?php echo esc_html($status); ?></li>
                        <li><strong>Priority:</strong> <?php echo esc_html($priority); ?></li>
                        <li><strong>Deadline:</strong> <?php echo $deadline ?: 'Not set'; ?></li>
                    </ul>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php
    
    echo ob_get_clean();

endwhile;

get_footer();
