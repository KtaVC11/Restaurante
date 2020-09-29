<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'restaurante' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '$XL?2O`Ctq>sMo*;*i2,BFCV;+Z?]z,Imlx7^|+_4Bk`ZUZ.vSm}@D|708*<y$R~' );
define( 'SECURE_AUTH_KEY',  'UwD;cyk`>/ES`e;&fqO%Nj$*(3~w^(7SQ6 7$4&]_MZsZHmX&RUFsm~{z;t[>B6k' );
define( 'LOGGED_IN_KEY',    '-!eLT~jh|2gbJKa- dAI?>LV~5?u8KmMEr#c`JS4hG.]PjMQ%>lUGT  w*2;Gq%A' );
define( 'NONCE_KEY',        '}rA!{x~_cabhL8?|~hA5v.EO{(4T.lG_bF=yrXMyaF+Y$/>IEDl79d3~n?[s*g<.' );
define( 'AUTH_SALT',        'I1NS%r-v.e.E`S`WX].)K#VUrU>04Swt,lX}@E=DPZ.>k,B_EXs@*k)os/N:| Xg' );
define( 'SECURE_AUTH_SALT', '%_H=@pW_SSH2]9KuF+0ra<=6oI$X,OX1wQL,l6GgOF>2nt/g~8aOx?u)QT#lhBOB' );
define( 'LOGGED_IN_SALT',   '1>(wFC{WvIyk|GL#k>QBH[]_]?e7FL_o:ODtV];ui=OTqFr_CFnYYUb-iEr>;smK' );
define( 'NONCE_SALT',       '^a1)F{Dpe1Kf|*OIHVN,+a0(1a#>s:t8k[)oMO>4xJN|qkf{N54:D6ff(z33g4m!' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
