<?php
/* 
Template Name: Projects Page
*/

get_header();

// Get current user info
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get plugin custom role
if (function_exists('mdw_get_custom_role')) {
    $role = mdw_get_custom_role($user_id);
} else {
    // Fallback to default WordPress roles if plugin not active
    $role = $current_user->roles ? $current_user->roles[0] : '';
}

// Allowed roles
$allowed_roles = ['admin', 'manager', 'administrator']; // plugin + WP default

// Check if user has access
$has_access = in_array($role, $allowed_roles, true);
?>

<div class="projects-container">
    <h1>Projects</h1>

    <?php if (!$has_access) : ?>
        <div class="mdw-notice">
            You do not have permission to view projects.
        </div>
    <?php else : ?>
        <?php
        // Query projects
        $args = [
            'post_type'      => 'mdw_project',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        // For employees, limit to assigned projects
        if ($role === 'employee') {
            $assigned = get_posts([
                'post_type'  => 'mdw_project',
                'fields'     => 'ids',
                'meta_query' => [
                    [
                        'key'     => '_mdw_assigned_devs',
                        'value'   => '"' . $user_id . '"',
                        'compare' => 'LIKE'
                    ]
                ]
            ]);
            $args['post__in'] = $assigned ? $assigned : [0];
        }

        $projects = new WP_Query($args);

        if ($projects->have_posts()) :
            echo '<ul class="project-list">';
            while ($projects->have_posts()) : $projects->the_post(); ?>
                <li class="project-item">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <div class="project-excerpt"><?php the_excerpt(); ?></div>
                </li>
            <?php endwhile;
            echo '</ul>';
            wp_reset_postdata();
        else :
            echo '<p>No projects found.</p>';
        endif;
        ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
