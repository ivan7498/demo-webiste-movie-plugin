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
define('DB_NAME', 'local');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'root');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',          ':tnpk,eBs04S,2`o2V5iri7tM`9_9(spN#ADh,W+^Hz@t8<S+ipI!GM[xL[o)mFf');
define('SECURE_AUTH_KEY',   ']jbT_jh1QJl@Ia[} C]jFm$ oA7-wR)}e`zaQ.{O1ZHNchbcuP;ZJ>#7g`@DM_7H');
define('LOGGED_IN_KEY',     'n<9,2+@(9xYx]WaUdyEF3[s,X*pd.Z?t;^I&!:1:,~O=x[z $p{U2WL-Te2LkgZ]');
define('NONCE_KEY',         'U(bUl{MC$g{)-7+%4`/H?Lc)7|SDmT#Vd^v@di+[1lCf<U_6m|@M3ay,`y2l8ZGP');
define('AUTH_SALT',         '-<1kut#P> Z.j8z`_(y^v_WA#rWw|:L{7QH0&9!OrwNY=nl>|_$-iE?/1b13%zw!');
define('SECURE_AUTH_SALT',  '=<rY!`]z9jXeq)8+$}a-keQ=k;G/~<~X=pmdP,,2<skH*$ +jp?I|rVV[Z6+Bi;E');
define('LOGGED_IN_SALT',    '>BCPoh,}=8mrqRfHq0=6Zl2b|H4/b[yEUze%6*$-?.SSi#xB*,eac&^v];UyZ~a4');
define('NONCE_SALT',        'ZYl$C7n#]AL1NZkb8OV;i$~+7=SvBaiZnr`_l*ilb%]qxzqETfO|gHz0=3Qn~JtL');
define('WP_CACHE_KEY_SALT', '[eO!%F$]/1oV=9b614Cn#xl>H,68VnMd7pW-J0}&E/ yJ+/LIRai =P6j4|j2d(4');


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
if (! defined('WP_DEBUG')) {
	define('WP_DEBUG', false);
}

define('WP_ENVIRONMENT_TYPE', 'local');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
