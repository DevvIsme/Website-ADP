<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( ! isset( $wp_did_header ) ) {
    $wp_did_header = true;

    // Kiểm tra xem tệp wp-load.php có tồn tại không
    $wp_load_path = __DIR__ . '/wp-load.php';
    if ( file_exists( $wp_load_path ) ) {
        // Nếu có, tiến hành tải thư viện WordPress
        require_once $wp_load_path;

        // Thiết lập truy vấn WordPress
        wp();

        // Tải template của chủ đề
        require_once ABSPATH . WPINC . '/template-loader.php';
    } else {
        // Nếu không tìm thấy tệp wp-load.php, hiển thị thông báo lỗi
        echo "Lỗi: Không tìm thấy tệp wp-load.php";
    }
}

