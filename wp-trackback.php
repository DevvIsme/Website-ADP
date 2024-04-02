<?php
/**
 * Handle Trackbacks and Pingbacks Sent to WordPress
 *
 * @since 0.71
 *
 * @package WordPress
 * @subpackage Trackbacks
 */
// Tải môi trường WordPress
if ( ! defined( 'ABSPATH' ) ) {
    require_once __DIR__ . '/wp-load.php';
}

// Đặt người dùng hiện tại là không xác thực
wp_set_current_user( 0 );

/**
 * Phản hồi cho một trackback.
 *
 * Phản hồi bằng một tin nhắn XML lỗi hoặc thành công.
 *
 * @since 0.71
 *
 * @param int|bool $error         Xác định có lỗi hay không.
 *                                Mặc định '0'. Chấp nhận '0' hoặc '1', true hoặc false.
 * @param string   $error_message Thông báo lỗi nếu có lỗi xảy ra. Mặc định là chuỗi rỗng.
 */
function trackback_response( $error = 0, $error_message = '' ) {
    header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );

    $error = (int) $error;
    $error_message = htmlspecialchars( $error_message );

    $response_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $response_xml .= "<response>\n";
    $response_xml .= "<error>{$error}</error>\n";
    $response_xml .= "<message>{$error_message}</message>\n";
    $response_xml .= "</response>";

    echo $response_xml;
    die();
}

// Kiểm tra xem các tham số cần thiết có tồn tại không
if ( ! isset( $_GET['tb_id'] ) || empty( $_GET['tb_id'] ) ) {
    trackback_response( 1, 'Thiếu ID Trackback.' );
}

// Lấy các tham số trackback
$post_id = (int) $_GET['tb_id'];
$trackback_url = isset( $_POST['url'] ) ? esc_url( $_POST['url'] ) : '';
$charset = isset( $_POST['charset'] ) ? wp_strip_all_tags( $_POST['charset'] ) : '';

// Xác thực ID bài đăng
if ( ! $post_id || ! get_post( $post_id ) ) {
    trackback_response( 1, 'ID bài đăng không hợp lệ.' );
}

// Xác thực URL trackback
if ( empty( $trackback_url ) || ! filter_var( $trackback_url, FILTER_VALIDATE_URL ) ) {
    trackback_response( 1, 'URL trackback không hợp lệ.' );
}

// Xác thực bảng mã
$charset = strtoupper( $charset );
$allowed_charsets = array( 'ASCII', 'UTF-8', 'ISO-8859-1', 'JIS', 'EUC-JP', 'SJIS' );
if ( ! in_array( $charset, $allowed_charsets ) ) {
    trackback_response( 1, 'Bảng mã không hợp lệ.' );
}

// Xử lý trackback
$title = isset( $_POST['title'] ) ? wp_strip_all_tags( $_POST['title'] ) : '';
$excerpt = isset( $_POST['excerpt'] ) ? wp_strip_all_tags( $_POST['excerpt'] ) : '';
$blog_name = isset( $_POST['blog_name'] ) ? wp_strip_all_tags( $_POST['blog_name'] ) : '';

// Thêm trackback như một bình luận
$commentdata = array(
    'comment_post_ID' => $post_id,
    'comment_author' => $blog_name,
    'comment_author_url' => $trackback_url,
    'comment_content' => "<strong>{$title}</strong>\n\n{$excerpt}",
    'comment_type' => 'trackback'
);

$comment_id = wp_insert_comment( $commentdata );

if ( is_wp_error( $comment_id ) ) {
    trackback_response( 1, 'Không thể chèn trackback như một bình luận.' );
}

// Gửi phản hồi thành công 

trackback_response( 0 );

?>