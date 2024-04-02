<?php
/**
 * Outputs the OPML XML format for getting the links defined in the link
 * administration. This can be used to export links from one blog over to
 * another. Links aren't exported by the WordPress export, so this file handles
 * that.
 *
 * This file is not added by default to WordPress theme pages when outputting
 * feed links. It will have to be added manually for browsers and users to pick
 * up that this file exists.
 *
 * @package WordPress
 */

// Bao gồm các chức năng cốt lõi của WordPress
require_once __DIR__ . '/wp-load.php';

// Đặt tiêu đề để chỉ định nội dung XML
header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
$link_cat = '';
// Kiểm tra nếu có tham số 'link_cat' được truyền và gán giá trị tương ứng cho $link_cat
if ( ! empty( $_GET['link_cat'] ) ) {
	$link_cat = $_GET['link_cat'];

	// Kiểm tra xem giá trị của $link_cat có phải là 'all' hoặc '0' không
	if ( ! in_array( $link_cat, array( 'all', '0' ), true ) ) {
		// Chuyển đổi $link_cat sang kiểu số nguyên dương nếu không phải là 'all' hoặc '0'
		$link_cat = absint( (string) urldecode( $link_cat ) );
	}
}

// Đầu ra định dạng XML
echo '<?xml version="1.0"?' . ">\n";
?>
<opml version="1.0">
	<head>
		<title>
		<?php
			/* Dịch các từ ngữ */
			printf( __( 'Liên kết cho %s' ), esc_attr( get_bloginfo( 'name', 'display' ) ) );
		?>
		</title>
		<dateCreated><?php echo gmdate( 'D, d M Y H:i:s' ); ?> GMT</dateCreated>
		<?php
		/**
		 * Fires in the OPML header.
		 *
		 * @since 3.0.0
		 */
		do_action( 'opml_head' );
		?>
	</head>
	<body>
<?php
// Lấy danh mục liên kết nếu không có tham số 'link_cat' hoặc lấy danh mục liên kết chỉ định nếu có tham số 'link_cat'
if ( empty( $link_cat ) ) {
	$cats = get_categories(
		array(
			'taxonomy'     => 'link_category',
			'hierarchical' => 0,
		)
	);
} else {
	$cats = get_categories(
		array(
			'taxonomy'     => 'link_category',
			'hierarchical' => 0,
			'include'      => $link_cat,
		)
	);
}

foreach ( (array) $cats as $cat ) :
	/** This filter is documented in wp-includes/bookmark-template.php */
	$catname = apply_filters( 'link_category', $cat->name );

	?>
<outline type="category" title="<?php echo esc_attr( $catname ); ?>">
	<?php
	$bookmarks = get_bookmarks( array( 'category' => $cat->term_id ) );
	foreach ( (array) $bookmarks as $bookmark ) :
		/**
		 * Filters the OPML outline link title text.
		 *
		 * @since 2.2.0
		 *
		 * @param string $title The OPML outline title text.
		 */
		$title = apply_filters( 'link_title', $bookmark->link_name );
		?>
<outline text="<?php echo esc_attr( $title ); ?>" type="link" xmlUrl="<?php echo esc_url( $bookmark->link_rss ); ?>" htmlUrl="<?php echo esc_url( $bookmark->link_url ); ?>" updated="
							<?php
							if ( '0000-00-00 00:00:00' !== $bookmark->link_updated ) {
								echo $bookmark->link_updated;
							}
							?>
" />
		<?php
	endforeach; // $bookmarks
	?>
</outline>
	<?php
endforeach; // $cats
?>
</body>
</opml>
