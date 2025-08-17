<?php

if (!defined('ABSPATH')) exit;

class MDW_Projects_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'mdw_projects_widget',
            __('Projects Summary', 'my-dashboard-widgets'),
            ['description' => __('Shows total projects and quick info', 'my-dashboard-widgets')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $total   = wp_count_posts('mdw_project')->publish;
        $current = new WP_Query([
            'post_type'  => 'mdw_project',
            'meta_key'   => '_mdw_status',
            'meta_value' => 'In Progress'
        ]);

        $onHold = new WP_Query([
            'post_type' => 'mdw_project',
            'meta_key' => '_mdw_status',
            'meta_value' => 'On Hold'
        ])

        ?>
        <div class="mdw-card">
            <h3>📂 Projects</h3>
            <p><strong>Total:</strong> <?php echo $total; ?></p>
            <p><strong>In Progress:</strong> <?php echo $current->found_posts; ?></p>
            <p><strong>On Hold:</strong> <?php echo $onHold->found_posts; ?></p>
            <a href="<?php echo site_url('/projects'); ?>" class="mdw-btn">View All</a>
        </div>
        <?php

        wp_reset_postdata();
        echo $args['after_widget'];
    }
}