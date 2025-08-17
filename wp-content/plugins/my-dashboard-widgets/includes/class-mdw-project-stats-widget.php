<?php
if (!defined('ABSPATH')) exit;

class MDW_Project_Stats_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'mdw_project_stats_widget',
            __('Project Status Stats', 'my-dashboard-widgets'),
            ['description' => __('Displays a chart of project statuses', 'my-dashboard-widgets')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Count statuses
        $statuses = ['In Progress', 'Completed', 'On Hold'];
        $counts   = [];
        $total   = wp_count_posts('mdw_project')->publish;

        foreach ($statuses as $s) {
            $q = new WP_Query([
                'post_type'  => 'mdw_project',
                'meta_key'   => '_mdw_status',
                'meta_value' => $s
            ]);
            $counts[$s] = $q->found_posts;
        }

        $id = 'chart_' . rand(1000, 9999);
        ?>

        <div class="mdw-card project-status">
            <h3 class="mdw-card__title">📊 Project Status</h3>
            <div class="project-status__content">
                <!-- Chart -->
                <div class="chart-wrapper">
                    <canvas id="<?php echo $id; ?>" class="canvas-wrapper" width="200" height="200"></canvas>
                    <div class="mdw-doughnut-center">
                        <span><?php echo esc_html($total); ?></span>
                        <small>Total</small>
                    </div>
                </div>

                <!-- Custom Legend -->
                <div class="project-status__legend">
                    <?php foreach ($counts as $status => $count): ?>
                        <div class="status-item status-<?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                            <div class="status-wrapper">
                                <span class="status-dot"></span>
                                <span class="status-label"><?php echo esc_html($status); ?></span>
                            </div>
                            <span class="status-count">(<?php echo esc_html($count); ?>)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
            window.MDW_PROJECT = window.MDW_PROJECT || [];
            window.MDW_PROJECT.push({
                id: "<?php echo esc_js($id); ?>",
                data: <?php echo json_encode($counts); ?>
            });
        </script>

        <?php

        echo $args['after_widget'];
    }
}
