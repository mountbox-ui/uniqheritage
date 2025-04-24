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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u387277923_uniq' );

/** Database username */
define( 'DB_USER', 'u387277923_uniq' );

/** Database password */
define( 'DB_PASSWORD', 'Uniq@123#' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'iG-Aro<Oj.!n[[{fe`rm`4iSG2DcIx8_TGtZ)D!}k>vc5?1zE.c5s~3w7?P8Y%>/' );
define( 'SECURE_AUTH_KEY',   'kanP}TKKEb k/q9.R8w@%#~:<Yh/K`i+3tA%;WT{]dY2A[V7H3S;@Yf`Ya;mFl9c' );
define( 'LOGGED_IN_KEY',     '4DR*2U{0;rKAb:I)q6m()2wzPXNiTvW]u]-*yD!s/~N RFK#Wvb6k4p=J!?e+P{4' );
define( 'NONCE_KEY',         '8CfA-m`wdmZva)u%h*;jA@>]z,5Zi6 $R&R2fQ-7fCT*M|d)r#Ts!.9Tol*UJ!fu' );
define( 'AUTH_SALT',         'sp@;KY/EobdONqHRjI9@s{Rs4H|09sM3gjH>:TF,<Bn9E;WHf3udq^i5*C0r*5:z' );
define( 'SECURE_AUTH_SALT',  ')0%;& -q8)9s<?#TT];g0 }C!5ma^J|%tE|hOv/Zc,CnQWK3 WC)cN`vL*F_ L9_' );
define( 'LOGGED_IN_SALT',    'E%ms0*A-d{,kWcsrS{4`6|PnFMxB,EyV{{dlpP:(u>)009_n]l Y:F?h^:,+SlM{' );
define( 'NONCE_SALT',        ']N#Klp#3[=!*S*gu9DE4AcdJ6K3]Z`hnrqn<5.?%r5PU4>Tv*Wr{2,o,YR)0tlD]' );
define( 'WP_CACHE_KEY_SALT', 'EK{;mb.r>W<nTJm=oq2./<MwLI=Rk&^H7)moM,ryJ6dsz2Xi~GOWTX;0sWc[-[jZ' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

define( 'WP_HOME', '	
http://green-baboon-531854.hostingersite.com/' );
define( 'WP_SITEURL', '	
http://green-baboon-531854.hostingersite.com/' );

