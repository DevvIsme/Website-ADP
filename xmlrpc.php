<?php
/**
 * XML-RPC protocol support for WordPress
 *
 * @package WordPress
 */

/**
 * Whether this is an XML-RPC Request.
 *
 * @var bool
 */
define( 'XMLRPC_REQUEST', true );

// Loại bỏ các cookie không cần thiết được gửi bởi một số trình duyệt nhúng.
$_COOKIE = array();

// $HTTP_RAW_POST_DATA đã bị loại bỏ trong PHP 7.0.
// phpcs:disable PHPCompatibility.Variables.RemovedPredefinedGlobalVariables.http_raw_post_dataDeprecatedRemoved
if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
	$HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
}

// Sửa lỗi cho trường hợp mozBlog và các trường hợp khác khi '<?xml' không nằm ở dòng đầu tiên.
if ( isset( $HTTP_RAW_POST_DATA ) ) {
	$HTTP_RAW_POST_DATA = trim( $HTTP_RAW_POST_DATA );
}
// phpcs:enable

/** Bao gồm các file cần thiết để thiết lập môi trường WordPress */
require_once __DIR__ . '/wp-load.php';

// Nếu có tham số 'rsd', đây là một yêu cầu RSD (Really Simple Discovery)
if ( isset( $_GET['rsd'] ) ) {
	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
	echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
	?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
	<service>
		<engineName>WordPress</engineName>
		<engineLink>https://wordpress.org/</engineLink>
		<homePageLink><?php bloginfo_rss( 'url' ); ?></homePageLink>
		<apis>
			<api name="WordPress" blogID="1" preferred="true" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="Movable Type" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<api name="Blogger" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
			<?php
			/**
			 * Fired when adding APIs to the Really Simple Discovery (RSD) endpoint.
			 *
			 * @link https://cyber.harvard.edu/blogs/gems/tech/rsd.html
			 *
			 * @since 3.5.0
			 */
			do_action( 'xmlrpc_rsd_apis' );
			?>
		</apis>
	</service>
</rsd>
	<?php
	exit;
}

// Bao gồm các file và lớp cần thiết cho XML-RPC server
require_once ABSPATH . 'wp-admin/includes/admin.php';
require_once ABSPATH . WPINC . '/class-IXR.php';
require_once ABSPATH . WPINC . '/class-wp-xmlrpc-server.php';

/**
 * Tiêu đề mặc định của bài đăng được gửi qua giao diện XML-RPC.
 *
 * @name post_default_title
 * @var string
 */
$post_default_title = '';

/**
 * Bộ lọc lớp được sử dụng để xử lý các yêu cầu XML-RPC.
 *
 * @since 3.1.0
 *
 * @param string $class Tên của lớp máy chủ XML-RPC.
 */
$wp_xmlrpc_server_class = apply_filters( 'wp_xmlrpc_server_class', 'wp_xmlrpc_server' );
$wp_xmlrpc_server       = new $wp_xmlrpc_server_class();

// Xử lý yêu cầu.
$wp_xmlrpc_server->serve_request();

exit;

/**
 * logIO() - Ghi thông tin đăng nhập vào một tệp.
 *
 * @deprecated 3.4.0 Sử dụng error_log()
 * @see error_log()
 *
 * @param string $io Loại đầu vào hoặc đầu ra.
 * @param string $msg Thông tin mô tả lý do đăng nhập.
 */
function logIO( $io, $msg ) {
	_deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
	if ( ! empty( $GLOBALS['xmlrpc_logging'] ) ) {
		error_log( $io . ' - ' . $msg );
	}
}
