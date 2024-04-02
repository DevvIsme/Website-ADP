<?php
/**
 * Handles Comment Post to WordPress and prevents duplicate comment posting.
 *
 * @package WordPress
 */

if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( 'Allow: POST' );
	header( "$protocol 405 Method Not Allowed" );
	header( 'Content-Type: text/plain' );
	exit;
}

/** Sets up the WordPress Environment. */
require __DIR__ . '/wp-load.php';

nocache_headers();

// Xử lý việc gửi bình luận và gán kết quả cho biến $comment
$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

// Kiểm tra nếu có lỗi xảy ra khi xử lý bình luận
if ( is_wp_error( $comment ) ) {
    // Lấy dữ liệu lỗi từ đối tượng $comment
    $data = (int) $comment->get_error_data();
    
    // Nếu dữ liệu lỗi không rỗng
    if ( ! empty( $data ) ) {
        // Hiển thị thông báo lỗi và kết thúc chương trình
        wp_die(
            '<p>' . $comment->get_error_message() . '</p>',
            __( 'Comment Submission Failure' ),
            array(
                'response'  => $data,
                'back_link' => true,
            )
        );
    } else {
        // Nếu không có dữ liệu lỗi, kết thúc chương trình
        exit;
    }
}


$user            = wp_get_current_user();
$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );

/**
 * Fires after comment cookies are set.
 *
 * @since 3.4.0
 * @since 4.9.6 The `$cookies_consent` parameter was added.
 *
 * @param WP_Comment $comment         Comment object.
 * @param WP_User    $user            Comment author's user object. The user may not exist.
 * @param bool       $cookies_consent Comment author's consent to store cookies.
 */
do_action( 'set_comment_cookies', $comment, $user, $cookies_consent );

$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;

// Nếu người dùng không đồng ý với cookie, thêm các đối số truy vấn cụ thể để hiển thị thông báo chờ duyệt.
if ( ! $cookies_consent && 'unapproved' === wp_get_comment_status( $comment ) && ! empty( $comment->comment_author_email ) ) {
    // Thêm các tham số truy vấn cụ thể để hiển thị thông báo chờ duyệt.
    $location = add_query_arg(
        array(
            'unapproved'      => $comment->comment_ID,
            'moderation-hash' => wp_hash( $comment->comment_date_gmt ),
        ),
        $location
    );
}


/**
 * Filters the location URI to send the commenter after posting.
 *
 * @since 2.0.5
 *
 * @param string     $location The 'redirect_to' URI sent via $_POST.
 * @param WP_Comment $comment  Comment object.
 */
$location = apply_filters( 'comment_post_redirect', $location, $comment );

wp_safe_redirect( $location );
exit;
