<?php
// Handle Form Submission BEFORE output
if (isset($_POST['mdw_update_project']) && (current_user_can('administrator') || current_user_can('manager'))) {
    $post_id = intval($_POST['post_id']);

    update_post_meta($post_id, '_mdw_status', sanitize_text_field($_POST['mdw_status']));
    update_post_meta($post_id, '_mdw_priority', sanitize_text_field($_POST['mdw_priority']));
    update_post_meta($post_id, '_mdw_deadline', sanitize_text_field($_POST['mdw_deadline']));

    // Redirect to avoid resubmission
    wp_redirect(get_permalink($post_id));
    exit;
}

get_header();

while (have_posts()) : the_post();
    $post_id = get_the_ID();

    $status   = get_post_meta($post_id, '_mdw_status', true) ?: 'In Progress';
    $priority = get_post_meta($post_id, '_mdw_priority', true) ?: 'High';
    $deadline = get_post_meta($post_id, '_mdw_deadline', true) ?: '';
    $devs     = get_post_meta($post_id, '_mdw_assigned_devs', true);
    ?>
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
            <!-- Editable Form -->
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
            <!-- Read Only View -->
            <ul>
                <li><strong>Status:</strong> <?php echo esc_html($status); ?></li>
                <li><strong>Priority:</strong> <?php echo esc_html($priority); ?></li>
                <li><strong>Deadline:</strong> <?php echo $deadline ?: 'Not set'; ?></li>
            </ul>
        <?php endif; ?>
    </div>
    <?php
endwhile;

get_footer();
