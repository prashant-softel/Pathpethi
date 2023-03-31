<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hostmjbt_way2soc');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'aws123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '^#)%eHBjLzM0eO#ag+Y)t0;39n|8FB.t9;4k0]%(*%@7~>!z*<WP6:]pN::w~~4o');
define('SECURE_AUTH_KEY',  ':`[`TI*7^ve_hHQW)=}>RB9K>|}2y{GmWdlEeF!3!}%m&|o]@MVpwLIH4,uoY@wo');
define('LOGGED_IN_KEY',    '/vgM(Bi2i[z~>_L8Z]anGJe8~&kA5P^{~j=i?96Q@,D0m1mplcr)wZQP5Q kzuOM');
define('NONCE_KEY',        ';9_6I$K|nW@bHdW}1qb%@L|G9,C:67nV=F!PyqVMJUSq$%0X>bB>rv(Dtr4|)rQ`');
define('AUTH_SALT',        'd%q]#1Q$,G$W[<-V|(1%V1ai[?ia(3UCWBqxbxLPQ2ECA>}eW3*`ItqV4:[gH{?@');
define('SECURE_AUTH_SALT', 'PP8O324{G4t|x0mpcL.<)qYQEg4PMVqw!*MMFFkdiyX /)l2A_v-]5:ostQ{1dL0');
define('LOGGED_IN_SALT',   '#$Y?vw}S(Je:}76$=P)m3[9;<VKb7&:=8Xdrnf]bT_0Fk*]c]OIoY%y?r6@CF6f(');
define('NONCE_SALT',       'j@}#t0cmO xvwqfOOK:WW*e%YZRN46hW3/%X~s.K0``.E[a1$?z/5M#fx$sFDo7Y');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
