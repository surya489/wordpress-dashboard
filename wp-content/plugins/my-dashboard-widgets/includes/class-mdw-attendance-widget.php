<?php
if (!defined('ABSPATH')) exit;

class MDW_Attendance_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'mdw_attendance_widget',
            __('MDW: Attendance Doughnut', 'my-dashboard-widgets'),
            ['description' => __('Displays a doughnut chart for attendance.', 'my-dashboard-widgets')]
        );
    }

    public function widget($args, $instance) {
        $title   = !empty($instance['title']) ? $instance['title'] : __('Attendance', 'my-dashboard-widgets');
        $present = isset($instance['present']) ? (int)$instance['present'] : 75;
        $absent  = isset($instance['absent']) ? (int)$instance['absent'] : 25;
        $roles   = !empty($instance['roles']) ? (array)$instance['roles'] : ['admin','manager','employee'];

        // Role-gate
        $user_role = mdw_get_custom_role(get_current_user_id());
        if (!in_array($user_role, $roles, true)) return;

        $chart_id = 'mdw_attendance_' . $this->number;

        echo $args['before_widget'];
        ?>
        <div class="mdw-card">
            <?php if (!empty($title)): ?>
                <h3 class="mdw-card-title"><?php echo esc_html($title); ?></h3>
            <?php endif; ?>
            <div class="mdw-doughnut-wrap">
                <canvas id="<?php echo esc_attr($chart_id); ?>" width="240" height="240"></canvas>
                <div class="mdw-doughnut-center">
                    <span><?php echo esc_html($present); ?>%</span>
                    <small>Present</small>
                </div>
            </div>
        </div>
        <script>
            window.MDW_ATTENDANCE = window.MDW_ATTENDANCE || [];
            window.MDW_ATTENDANCE.push({
                id: "<?php echo esc_js($chart_id); ?>",
                data: { present: <?php echo (int)$present; ?>, absent: <?php echo (int)$absent; ?> }
            });
        </script>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title   = isset($instance['title']) ? $instance['title'] : __('Attendance', 'my-dashboard-widgets');
        $present = isset($instance['present']) ? (int)$instance['present'] : 75;
        $absent  = isset($instance['absent']) ? (int)$instance['absent'] : 25;
        $roles   = isset($instance['roles']) ? (array)$instance['roles'] : ['admin','manager','employee'];
        $all_roles = ['admin','manager','employee'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('present'); ?>">Present (%)</label>
            <input class="small-text" id="<?php echo $this->get_field_id('present'); ?>"
                   name="<?php echo $this->get_field_name('present'); ?>" type="number" min="0" max="100"
                   value="<?php echo esc_attr($present); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('absent'); ?>">Absent (%)</label>
            <input class="small-text" id="<?php echo $this->get_field_id('absent'); ?>"
                   name="<?php echo $this->get_field_name('absent'); ?>" type="number" min="0" max="100"
                   value="<?php echo esc_attr($absent); ?>">
        </p>
        <p>
            <label>Visible to roles:</label><br>
            <?php foreach ($all_roles as $r): ?>
                <label>
                    <input type="checkbox" name="<?php echo $this->get_field_name('roles'); ?>[]"
                           value="<?php echo esc_attr($r); ?>" <?php checked(in_array($r, $roles, true)); ?>>
                    <?php echo esc_html(ucfirst($r)); ?>
                </label><br>
            <?php endforeach; ?>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $inst = [];
        $inst['title']   = sanitize_text_field($new_instance['title'] ?? '');
        $inst['present'] = max(0, min(100, (int)($new_instance['present'] ?? 0)));
        $inst['absent']  = max(0, min(100, (int)($new_instance['absent'] ?? 0)));
        $roles = isset($new_instance['roles']) ? array_map('sanitize_text_field', (array)$new_instance['roles']) : [];
        $inst['roles'] = array_values(array_intersect($roles, ['admin','manager','employee']));
        if (empty($inst['roles'])) $inst['roles'] = ['admin','manager','employee'];
        return $inst;
    }
}
