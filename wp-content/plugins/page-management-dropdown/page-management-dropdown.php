<?php

/*

Plugin Name: Page Management Dropdown
Plugin URI: http://jaschaephraim.com/wordpress/
Description: Adds a link to edit each individual page to the Pages admin menu.
Version: 2.6
Author: Jascha Ephraim
Author URI: http://jaschaephraim.com/
License: GPL3

    Page Management Dropdown (Wordpress Plugin)
    Copyright (C) 2008 Jascha Ephraim

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
add_action( 'admin_menu', 'jeml_page_management_dropdown' );

function jeml_page_management_dropdown() {
	global $submenu, $wpdb;
	$pages = $wpdb->get_results( "SELECT ID, post_title, post_name, post_parent FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY menu_order ASC, post_title ASC" );
	$indexed_pages = get_page_hierarchy( $pages );
	
  foreach ( $pages as $page )
		$indexed_pages[ $page->ID ] = $page;
	
  foreach ( $indexed_pages as $page ) {
		$indent = '';
		$parent = $page->post_parent;

		while ( $parent != 0 ) {
      $indent .= '&nbsp;&nbsp;&nbsp;';
			$parent = $indexed_pages[ $parent ]->post_parent;
		}

		$submenu[ 'edit.php?post_type=page' ][] = array( $indent . $page->post_title, 'edit_pages', 'post.php?action=edit&post='.$page->ID );
	}
}

?>
