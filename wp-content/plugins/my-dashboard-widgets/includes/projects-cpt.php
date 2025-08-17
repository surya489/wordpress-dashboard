<?php
// ============================
// Register Projects CPT
// ============================
function mdw_register_projects_cpt() {
    $labels = array(
        'name'               => 'Projects',
        'singular_name'      => 'Project',
        'menu_name'          => 'Projects',
        'name_admin_bar'     => 'Project',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Project',
        'edit_item'          => 'Edit Project',
        'new_item'           => 'New Project',
        'view_item'          => 'View Project',
        'all_items'          => 'All Projects',
        'search_items'       => 'Search Projects',
        'not_found'          => 'No projects found',
        'not_found_in_trash' => 'No projects found in Trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'author'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'projects'),
        'show_in_rest'       => true, // Gutenberg + API
    );

    register_post_type('mdw_project', $args);
}
add_action('init', 'mdw_register_projects_cpt');


// ============================
// Add Developers Meta Box
// ============================
function mdw_add_project_meta_boxes() {
    add_meta_box(
        'mdw_project_developers',
        'Assign Developers',
        'mdw_project_developers_callback',
        'mdw_project',
        'side'
    );
}
add_action('add_meta_boxes', 'mdw_add_project_meta_boxes');

function mdw_project_developers_callback($post) {
    $assigned = get_post_meta($post->ID, '_mdw_assigned_devs', true) ?: array();

    // ✅ Get only users with custom_role = employee
    $employees = get_users(array(
        'meta_key'   => 'custom_role',
        'meta_value' => 'employee',
    ));

    if ($employees) {
        foreach ($employees as $user) {
            $checked = in_array($user->ID, $assigned) ? 'checked' : '';
            echo '<label style="display:block;margin:2px 0;">
                    <input type="checkbox" name="mdw_assigned_devs[]" value="'.esc_attr($user->ID).'" '.$checked.'>
                    '.esc_html($user->display_name).'
                </label>';
        }
    } else {
        echo '<p>No employees found.</p>';
    }
}


// ============================
// Save Developers
// ============================
function mdw_save_project_developers($post_id) {
    if (isset($_POST['mdw_assigned_devs'])) {
        $devs = array_map('intval', $_POST['mdw_assigned_devs']);
        update_post_meta($post_id, '_mdw_assigned_devs', $devs);
    } else {
        delete_post_meta($post_id, '_mdw_assigned_devs');
    }
}
add_action('save_post_mdw_project', 'mdw_save_project_developers');


// ============================
// Helper: Display Developers (frontend)
// ============================
function mdw_display_project_devs($post_id) {
    $devs = get_post_meta($post_id, '_mdw_assigned_devs', true);
    if ($devs) {
        echo "<h4>Assigned Developers:</h4><ul>";
        foreach ($devs as $dev_id) {
            $user = get_userdata($dev_id);
            if ($user) {
                echo "<li>".esc_html($user->display_name)."</li>";
            }
        }
        echo "</ul>";
    }
}
