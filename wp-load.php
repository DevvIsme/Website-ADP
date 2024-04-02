<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the wp-config.php file. The wp-config.php
 * file will then load the wp-settings.php file, which
 * will then set up the WordPress environment.
 *
 * If the wp-config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * wp-config.php file.
 *
 * Will also search for wp-config.php in WordPress' parent
 * directory to allow the WordPress directory to remain
 * untouched.
 *
 * @package WordPress
 */

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

// Load necessary files
require_once ABSPATH . 'wp-includes/version.php';
require_once ABSPATH . 'wp-includes/compat.php';
require_once ABSPATH . 'wp-includes/load.php';
require_once ABSPATH . 'wp-includes/functions.php';

// Error reporting
if ( function_exists( 'error_reporting' ) ) {
    error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
}

// Check if wp-config.php exists in the root directory or parent directory
if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
    require_once ABSPATH . 'wp-config.php';
} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
    require_once dirname( ABSPATH ) . '/wp-config.php';
} else {
    // No wp-config.php found, redirect to setup-config.php
    redirect_to_setup_config();
}

/**
 * Redirect to setup-config.php
 */
function redirect_to_setup_config() {
    $path = wp_guess_url() . '/wp-admin/setup-config.php';

    // Redirect to setup-config.php if not already on that page
    if ( strpos( $_SERVER['REQUEST_URI'], 'setup-config' ) === false ) {
        header( 'Location: ' . $path );
        exit;
    }

    // Die with an error message
    $error_message = generate_error_message();
    wp_die( $error_message, __( 'WordPress &rsaquo; Error' ) );
}

/**
 * Generate error message
 */
function generate_error_message() {
    $error_message = '<p>' . sprintf(
        /* translators: %s: wp-config.php */
        __( "There doesn't seem to be a %s file. It is needed before the installation can continue." ),
        '<code>wp-config.php</code>'
    ) . '</p>';
    $error_message .= '<p>' . sprintf(
        /* translators: 1: Documentation URL, 2: wp-config.php */
        __( 'Need more help? <a href="%1$s">Read the support article on %2$s</a>.' ),
        __( 'https://wordpress.org/documentation/article/editing-wp-config-php/' ),
        '<code>wp-config.php</code>'
    ) . '</p>';
    $error_message .= '<p>' . sprintf(
        /* translators: %s: wp-config.php */
        __( "You can create a %s file through a web interface, but this doesn't work for all server setups. The safest way is to manually create the file." ),
        '<code>wp-config.php</code>'
    ) . '</p>';
    $error_message .= '<p><a href="' . $path . '" class="button button-large">' . __( 'Create a Configuration File' ) . '</a></p>';

    return $error_message;
}
