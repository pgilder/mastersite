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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wlsf');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         ' ^1pXQEj[Y;QNbbT47accE.X2~6/;pS(S#TEIFJp;a1o_H9`%m<4M(T;0@;`.cx3');
define('SECURE_AUTH_KEY',  '}>;n+F~A^^7m^~.#.CT6he{yh374pN@5&]1W{urJib4rjKVEwik?]?}>___{*S!l');
define('LOGGED_IN_KEY',    ';u&{nR7l`Lu8Se~.6[F`<HL1H_Q|^PnrI#5YG+|AS;2Ih8jo!V]5ri)N|-Fb~8)r');
define('NONCE_KEY',        'q~z*&o|E1>g:qTd{+-{3%MPX#Or><(@2hNC4gl_J/Yi(hdK!9Urv`7U+,E`LH|1w');
define('AUTH_SALT',        'L:J:..9g}~d7D<23?D2Gn1fSpMWQ]IG5hp$&sHQ=+*[6s@i)`|Fwi~+PHD?-OYI1');
define('SECURE_AUTH_SALT', 'k5$(#SXdzWSY3hmXXuf!^NvDeA(YU#Rc4}mj%K^wFvfAIbZ}g_R*k/7YUEins}If');
define('LOGGED_IN_SALT',   'B`tWHTK&A8FM47t-bRUwBVf[3y?LynNv;VGX?6{Uv9_,cnIb@K5`Lchh-r;rUP#Q');
define('NONCE_SALT',       '@9>41S^_st0(NKZ/Q.Y0>4:E<e*Az[=,jcCB(W`h2zFUyJf?{RnWWb?5pFlo{_oK');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wlsf_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
