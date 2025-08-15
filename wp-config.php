<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'my_dashboard_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'O@;s-pKe9XSZq9^e/{ bUSAo<#rcYcDZ*RlU}/--sk-=XzF@VBQc.D8J5``6}G~b' );
define( 'SECURE_AUTH_KEY',  '`$ZAR}:AV3g{P7G/jAGPY>,so@5#sZzTnLW ;!dwMGp(?vn9Jc*rtg<?,%Y+<-,a' );
define( 'LOGGED_IN_KEY',    '#Iv2|i{[anc9gYK- L,h2.HaI`W9Ol8rPu3#Chp0Va9!olhkB#i*j8Osk5Hef&[!' );
define( 'NONCE_KEY',        'EmsWl#r&1+ #fRl ^lcYM/C#3ob9)mvowm_ckuA<uB?#daqCAlDmp{-<Y_eL2+5B' );
define( 'AUTH_SALT',        'Zt48Z@? Q5;-&OAW?M~PZS?,^VKkabZ7fp$1!Qf4N;R FT&GcJ:<xKqlCeZ&^Lxa' );
define( 'SECURE_AUTH_SALT', ']h.[X)G=QBh2l*dFB2,07m~PLk0BjT:2iUz6iI@o3v3^j Jh~s24eQ2O}DY?MG1&' );
define( 'LOGGED_IN_SALT',   'dm3ud5+uA-WrdO25|?5uEEZLnIOQ~m%4P^,UBk|hk`W8%98T=bXGbT%351/mT#2F' );
define( 'NONCE_SALT',       ')%W:DG2Q2*yR]QmECqyMhly>lc9f5:{!KnN1oc.9X<DW|e?I,?dCGTw,z+_f9bm4' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
