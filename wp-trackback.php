<?php
/**
 * Handle Trackbacks and Pingbacks Sent to WordPress
 *
 * @since 0.71
 *
 * @package WordPress
 * @subpackage Trackbacks
 */

// Load WordPress environment
if ( ! defined( 'ABSPATH' ) ) {
    require_once __DIR__ . '/wp-load.php';
}

// Set current user as unauthenticated
wp_set_current_user( 0 );

/**
 * Response to a trackback.
 *
 * Responds with an error or success XML message.
 *
 * @since 0.71
 *
 * @param int|bool $error         Whether there was an error.
 *                                Default '0'. Accepts '0' or '1', true or false.
 * @param string   $error_message Error message if an error occurred. Default empty string.
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

// Check if required parameters are present
if ( ! isset( $_GET['tb_id'] ) || empty( $_GET['tb_id'] ) ) {
    trackback_response( 1, 'Trackback ID is missing.' );
}

// Get trackback parameters
$post_id = (int) $_GET['tb_id'];
$trackback_url = isset( $_POST['url'] ) ? esc_url( $_POST['url'] ) : '';
$charset = isset( $_POST['charset'] ) ? wp_strip_all_tags( $_POST['charset'] ) : '';

// Validate post ID
if ( ! $post_id || ! get_post( $post_id ) ) {
    trackback_response( 1, 'Invalid post ID.' );
}

// Validate trackback URL
if ( empty( $trackback_url ) || ! filter_var( $trackback_url, FILTER_VALIDATE_URL ) ) {
    trackback_response( 1, 'Invalid trackback URL.' );
}

// Validate charset
$charset = strtoupper( $charset );
$allowed_charsets = array( 'ASCII', 'UTF-8', 'ISO-8859-1', 'JIS', 'EUC-JP', 'SJIS' );
if ( ! in_array( $charset, $allowed_charsets ) ) {
    trackback_response( 1, 'Invalid charset.' );
}

// Process trackback
$title = isset( $_POST['title'] ) ? wp_strip_all_tags( $_POST['title'] ) : '';
$excerpt = isset( $_POST['excerpt'] ) ? wp_strip_all_tags( $_POST['excerpt'] ) : '';
$blog_name = isset( $_POST['blog_name'] ) ? wp_strip_all_tags( $_POST['blog_name'] ) : '';

// Add trackback as comment
$commentdata = array(
    'comment_post_ID' => $post_id,
    'comment_author' => $blog_name,
    'comment_author_url' => $trackback_url,
    'comment_content' => "<strong>{$title}</strong>\n\n{$excerpt}",
    'comment_type' => 'trackback'
);

$comment_id = wp_insert_comment( $commentdata );

if ( is_wp_error( $comment_id ) ) {
    trackback_response( 1, 'Failed to insert trackback as comment.' );
}

// Send success response
trackback_response( 0 );
?>
