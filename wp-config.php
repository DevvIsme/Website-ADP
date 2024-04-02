<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'webbanhang' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'password_here' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'zdcA)ACDQ?~<?{;ltE)(9wQVuG2{Sk^Gh(;-Ac{&T9h:H7x{~/j4V.I(sLG;X@33' );
define( 'SECURE_AUTH_KEY',  'x+EZ,N>GDr<Q(4~M~r:[8zP/-)*0Oocay,7Yz/eJMG]P|m)x4z*:ocif~]TLdaQ~' );
define( 'LOGGED_IN_KEY',    '^1lQx## ,i;3<n21vQBDR2t:iA(:GGo^i50wd+QtkK@-*flhPtv:&qN?]vGLB_c5' );
define( 'NONCE_KEY',        'V[@Fl#If6/AtX;bpT):w@ib]O_GGPx8%f=~RsWj7t;@HUf^N%hW;gOpUa`&7JDoj' );
define( 'AUTH_SALT',        '7K-t t3HS_:ih%TdG&Ck7o)O1]HyjHzLM11#*|~*OL05czyiM^(l0engbH|~U~-V' );
define( 'SECURE_AUTH_SALT', '_#e/?P0t>mok8)[jnoJPyrpXCt|Z;qxsOm^sJf-SI`_qnGQ80Isjk?6 ~Ga}rA?~' );
define( 'LOGGED_IN_SALT',   'xN$CA/LAo#i.s}Ks@4SR/+JHS?H&9EV[v3[mMghc>MW0M~ILZ#8j<t7G5(2UVYfS' );
define( 'NONCE_SALT',       'h]Wp/k1UfWT1}6A/%ZNaJfg%1,:%o,dVq3_E6e+an ` 35NTp702}]^2=1yJ Udv' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
