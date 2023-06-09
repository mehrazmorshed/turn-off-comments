<?php

/*
 * Plugin Name:       Turn Off Comments
 * Plugin URI:        https://wordpress.org/plugins/turn-off-comments/
 * Description:       Turn Off Comments from your WordPress Website.
 * Version:           1.0
 * Requires at least: 4.0
 * Requires PHP:      7.0
 * Author:            Mehraz Morshed
 * Author URI:        https://profiles.wordpress.org/mehrazmorshed/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       turn-off-comments
 * Domain Path:       /languages
 */

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

if( !defined( 'ABSPATH' ) ) {
    exit();
}
define( 'TURN_OFF_COMMENTS_VERSION', '1.0' );
// function 1
function turn_off_comments_load_textdomain() {
    load_plugin_textdomain( 'turn-off-comments', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'turn_off_comments_load_textdomain' );
// function 2
function turn_off_comments_no_wp_comments() {
    wp_die('No comments');
}
add_action('pre_comment_on_post', 'turn_off_comments_no_wp_comments'); 
// function 3
function turn_off_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'turn_off_comments_admin_menu'); 
// function 4
function turn_off_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'turn_off_comments_dashboard'); 
// function 5
function turn_off_comments_status() {
    return false;
}
add_filter('comments_open', 'turn_off_comments_status', 20, 2); 
add_filter('pings_open', 'turn_off_comments_status', 20, 2);
// function 6
function turn_off_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'turn_off_comments_hide_existing_comments', 10, 2); 
// function 7
function turn_off_comments_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'turn_off_comments_admin_bar_render');  
// function 8
function turn_off_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit();
    }
}
add_action('admin_init', 'turn_off_comments_admin_menu_redirect'); 
// function 9
function turn_off_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'turn_off_comments_post_types_support'); 
// function 10
function turn_off_comments_activation_hook() {
    set_transient( 'turn-off-comments-notification', true, 5 );
}
register_activation_hook( __FILE__, 'turn_off_comments_activation_hook' );
// function 11
function turn_off_comments_activation_notification(){
    if( get_transient( 'turn-off-comments-notification' ) ) {
        ?>
        <div class="updated notice is-dismissible">
            <p><?php esc_attr_e( 'Thank you for installing Turn Off Comments!', 'turn-off-comments' ); ?></p>
        </div>
        <?php
        delete_transient( 'turn-off-comments-notification' );
    }
}
add_action( 'admin_notices', 'turn_off_comments_activation_notification' );