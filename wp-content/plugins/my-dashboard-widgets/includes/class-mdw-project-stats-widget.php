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
                    <canvas id="<?php echo $id; ?>" width="200" height="200"></canvas>
                    <div class="chart-center-text" id="center_<?php echo $id; ?>"></div>
                </div>

                <!-- Custom Legend -->
                <div class="project-status__legend">
                    <?php foreach ($counts as $status => $count): ?>
                        <div class="status-item status-<?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                            <span class="status-dot"></span>
                            <span class="status-label"><?php echo esc_html($status); ?></span>
                            <span class="status-count">(<?php echo esc_html($count); ?>)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <style>
            .project-status__content {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            .chart-wrapper {
                position: relative;
                width: 200px;
                height: 200px;
            }
            .chart-center-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 1.1rem;
                font-weight: 600;
                color: #374151;
            }
            .project-status__legend {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .status-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 0.9rem;
                color: #374151;
            }
            .status-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                display: inline-block;
            }
            .status-in-progress .status-dot { background: #3498db; }
            .status-completed .status-dot { background: #2ecc71; }
            .status-on-hold .status-dot { background: #f39c12; }
        </style>

        <script>
            jQuery(document).ready(function($) {
                const ctx = document.getElementById("<?php echo $id; ?>").getContext("2d");
                const data = <?php echo json_encode(array_values($counts)); ?>;
                const total = data.reduce((a, b) => a + b, 0);

                const chart = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: <?php echo json_encode(array_keys($counts)); ?>,
                        datasets: [{
                            data: data,
                            backgroundColor: ["#3498db", "#2ecc71", "#f39c12"],
                            borderColor: "#ffffff",
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: "70%",
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: "#111827",
                                titleColor: "#F9FAFB",
                                bodyColor: "#D1D5DB",
                                callbacks: {
                                    label: function(ctx) {
                                        let val = ctx.raw;
                                        let percent = ((val / total) * 100).toFixed(1);
                                        return ctx.label + ": " + val + " (" + percent + "%)";
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });

                // Add center text (Total projects)
                $("#center_<?php echo $id; ?>").text(total + " Total");
            });
        </script>
        <?php

        echo $args['after_widget'];
    }
}
