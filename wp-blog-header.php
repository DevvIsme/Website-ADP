<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

// Kiểm tra xem biến $wp_did_header có tồn tại không.
if ( ! isset( $wp_did_header ) ) {
    // Gán giá trị true cho biến $wp_did_header để đánh dấu rằng mã trong điều kiện này đã được thực thi.
    $wp_did_header = true;

    // Load thư viện WordPress.
    require_once __DIR__ . '/wp-load.php';

    // Thiết lập truy vấn của WordPress.
    wp();

    // Load template của theme.
    require_once ABSPATH . WPINC . '/template-loader.php';
}
