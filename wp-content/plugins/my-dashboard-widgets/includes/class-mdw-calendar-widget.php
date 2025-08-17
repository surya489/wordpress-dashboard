<?php

if (!defined('ABSPATH')) exit;

class MDW_Calendar_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'mdw_calendar_widget',
            __('Dashboard Calendar', 'my-dashboard-widgets'),
            ['description' => __('Displays a simple calendar widget', 'my-dashboard-widgets')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget']; ?>
        <div class="mdw-card">
            <h3>📅 Calendar</h3>
            <?php echo get_calendar(false); // WordPress built-in calendar ?>
        </div>
        <?php
        echo $args['after_widget'];
    }
}