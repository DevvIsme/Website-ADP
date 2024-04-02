<?php
/**
 * Cấu hình kết nối cơ sở dữ liệu.
 */
define( 'DB_NAME', 'ten_cua_ban' );
define( 'DB_USER', 'ten_nguoi_dung_cua_ban' );
define( 'DB_PASSWORD', 'mat_khau_cua_ban' );
define( 'DB_HOST', 'localhost' ); // Hoặc địa chỉ IP của máy chủ cơ sở dữ liệu.

/**
 * Tiền tố bảng cho cơ sở dữ liệu WordPress.
 */
$table_prefix = 'wp_'; // Bạn có thể thay đổi 'wp_' thành bất kỳ điều gì bạn muốn.

/**
 * Kích hoạt chế độ gỡ lỗi.
 */
define( 'WP_DEBUG', true ); // Bật chế độ debug.
define( 'WP_DEBUG_LOG', true ); // Ghi log lỗi vào wp-content/debug.log.
define( 'WP_DEBUG_DISPLAY', false ); // Ẩn lỗi trực tiếp trên trang web.

/**
 * Thay đổi các khóa xác thực và mật khẩu.
 */
define( 'AUTH_KEY', 'chuoi_khoa_xac_thuc' );
define( 'SECURE_AUTH_KEY', 'chuoi_khoa_xac_thuc_an_toan' );
define( 'LOGGED_IN_KEY', 'chuoi_khoa_dang_nhap' );
define( 'NONCE_KEY', 'chuoi_khoa_nonce' );
define( 'AUTH_SALT', 'muoi_xac_thuc' );
define( 'SECURE_AUTH_SALT', 'muoi_xac_thuc_an_toan' );
define( 'LOGGED_IN_SALT', 'muoi_da_dang_nhap' );
define( 'NONCE_SALT', 'muoi_nonce' );

/**
 * Thiết lập đường dẫn tuyệt đối đến thư mục wp-content.
 */
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
define( 'WP_CONTENT_URL', 'http://example.com/wp-content' );

/**
 * Thiết lập đường dẫn tuyệt đối đến thư mục plugin.
 */
define( 'WP_PLUGIN_DIR', dirname( __FILE__ ) . '/wp-content/plugins' );
define( 'WP_PLUGIN_URL', 'http://example.com/wp-content/plugins' );

/**
 * Thiết lập đường dẫn tuyệt đối đến thư mục theme.
 */
define( 'TEMPLATEPATH', '/duong_dan_tuyet_doi_den_thu_muc_wp-content/themes/' );
define( 'STYLESHEETPATH', '/duong_dan_tuyet_doi_den_thu_muc_wp-content/themes/' );

/**
 * Ghi chú: Hãy chắc chắn thay đổi các giá trị được đặt cho các hằng số trên
 * để phản ánh cài đặt cụ thể của bạn.
 */
