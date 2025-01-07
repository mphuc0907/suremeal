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
define( 'DB_NAME', 'suremealdevdb' );

/** Database username */
define( 'DB_USER', 'suremealdev' );

/** Database password */
define( 'DB_PASSWORD', '8XY7CruxZC8pqPTRssXk@123' );

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
define( 'AUTH_KEY',         'O;K<!*jbTs_iS09K:IpF5Sm%{HjLY3`$Tr9wiyMC*-Pyd*rfS!LyhA3=S+flaX9m' );
define( 'SECURE_AUTH_KEY',  '?V08}8t-#fuePasxA9#avcz.EOUsDO]z]s[jXMKTi#+RcNlb;SsHq-rq4(A!5yoq' );
define( 'LOGGED_IN_KEY',    ',WgJtMg.LP=[Z@&KV{z_heNvJ-<|jLcH8Z?iGo+!yLQaDUgZyD9S+G}&vOAc#ydm' );
define( 'NONCE_KEY',        '>;/;Yw(/F4jcbg1OO,v|SVPT<WyS-?DQ2m]CtRmLG@S5;8vIprA@!H%eaL;UOoS@' );
define( 'AUTH_SALT',        '5!]$DoEhPjK[S.y=yI4dHD,0Y.Y@b,q1H0zO704z8p;N lI(-sD.n//E4$N]w+*d' );
define( 'SECURE_AUTH_SALT', 's5NK%=G#SeAwpTzN+6<Y[5vqZT`F+ue}1%UAe|x|=p_n6U}Xqov+][@2kFuN>w;]' );
define( 'LOGGED_IN_SALT',   'k[PcZAJYa(+or~?sEB!^tr%jX mGiZp()aZR% IR|GWXreMHBmlU(tKsa]7P twX' );
define( 'NONCE_SALT',       'h`usIk=NI4&5J&Sg9baD*jqVm(K_2?B%Oi|cY|=+oBQZ;Lo{92-x$b7N,A@Ip2^S' );

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
// define( 'COOKIE_DOMAIN', '' );
define('WP_ALLOW_MULTISITE', true);
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'DOMAIN_CURRENT_SITE', 'suremealdev.wecan-group.info' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
