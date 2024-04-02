<?php
/**
 * Đaemon giả lập cron cho việc lập lịch các nhiệm vụ trong WordPress.
 *
 * WP-Cron được kích hoạt khi trang web nhận được một lượt truy cập. Trong các trường hợp mà trang web có thể không nhận đủ lượt truy cập để thực hiện các nhiệm vụ được lập lịch đúng hạn, tệp này có thể được gọi trực tiếp hoặc thông qua một daemon cron máy chủ cho một số lần.
 *
 * Việc xác định DISABLE_WP_CRON là true và gọi trực tiếp tệp này là độc quyền, và cái sau không phụ thuộc vào cái trước để hoạt động.
 *
 * Yêu cầu HTTP đến tệp này sẽ không làm chậm người truy cập nào rơi vào khi một sự kiện cron được lập lịch chạy.
 *
 * @package WordPress
 */

// Bỏ qua việc người dùng hủy và cho phép script chạy vô thời hạn
ignore_user_abort(true);

// Ngăn chặn việc caching
if (!headers_sent()) {
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
}

// Cố gắng tăng giới hạn bộ nhớ PHP cho xử lý sự kiện cron
wp_raise_memory_limit('cron');

// Định nghĩa DOING_CRON để chỉ ra rằng cron đang chạy
define('DOING_CRON', true);

// Tải môi trường WordPress nếu chưa được tải
if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/wp-load.php';
}

// Lấy khóa cron hiện tại
function _get_cron_lock() {
    global $wpdb;

    $value = 0;
    if (wp_using_ext_object_cache()) {
        $value = wp_cache_get('doing_cron', 'transient', true);
    } else {
        $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", '_transient_doing_cron'));
        if ($row !== null) {
            $value = $row->option_value;
        }
    }

    return $value;
}

// Lấy tất cả các sự kiện cron đã lập lịch
$crons = wp_get_ready_cron_jobs();
if (empty($crons)) {
    die();
}

// Lấy thời gian GMT hiện tại
$gmt_time = microtime(true);

// Lấy khóa cron hiện tại
$doing_cron_transient = get_transient('doing_cron');

// Sử dụng khóa cron toàn cục $doing_wp_cron, hoặc khóa GET nếu có sẵn
if (empty($doing_wp_cron)) {
    if (empty($_GET['doing_wp_cron'])) {
        if ($doing_cron_transient && ($doing_cron_transient + WP_CRON_LOCK_TIMEOUT > $gmt_time)) {
            return;
        }
        $doing_wp_cron = sprintf('%.22F', microtime(true));
        $doing_cron_transient = $doing_wp_cron;
        set_transient('doing_cron', $doing_wp_cron);
    } else {
        $doing_wp_cron = $_GET['doing_wp_cron'];
    }
}

// Kiểm tra xem khóa cron khớp với khóa hiện tại không
if ($doing_cron_transient !== $doing_wp_cron) {
    return;
}

// Duyệt qua mỗi sự kiện cron và kích hoạt các sự kiện đã lập lịch
foreach ($crons as $timestamp => $cronhooks) {
    if ($timestamp > $gmt_time) {
        break;
    }

    foreach ($cronhooks as $hook => $keys) {
        foreach ($keys as $k => $v) {
            $schedule = $v['schedule'];

            // Lập lịch lại sự kiện nếu cần thiết
            if ( $schedule ) {
                $result = wp_reschedule_event( $timestamp, $schedule, $hook, $v['args'] );

                if ( is_wp_error( $result ) ) {
                    error_log( sprintf( __( 'Cron reschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s' ), $hook, $result->get_error_code(), $result->get_error_message(), wp_json_encode( $v ) ) );
                    do_action( 'cron_reschedule_event_error', $result, $hook, $v );
                }
            }


            // Hủy lịch sự kiện
            $result = wp_unschedule_event($timestamp, $hook, $v['args'], true);

            if (is_wp_error($result)) {
                error_log(sprintf(__('Cron unschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s'), $hook, $result->get_error_code(), $result->get_error_message(), wp_json_encode($v)));
                do_action('cron_unschedule_event_error', $result, $hook, $v);
            }

            // Kích hoạt sự kiện đã lập lịch
            do_action_ref_array($hook, $v['args']);
        }
    }
}

// Nếu khóa hiện tại khớp, xóa khóa cron
if (_get_cron_lock() === $doing_wp_cron) {
    delete_transient('doing_cron');
}

// Kết thúc việc thực thi script
die();
?>
`