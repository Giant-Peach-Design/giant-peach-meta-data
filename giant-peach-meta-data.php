<?php

/*
Plugin Name: Giant Peach Meta Data
Plugin URI: http://giantpeach.agency
Description: Ability to add meta data to posts and pages.
Version: 0.0.1
Author: Giant Peach
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
Giant Peach Meta Data is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Giant Peach Meta Data is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Giant Peach Meta Data. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

defined( 'ABSPATH' ) or die( 'No direct script access.' );

add_action( 'add_meta_boxes', 'giant_peach_add_meta_data_meta_box' );
add_action( 'save_post', 'giant_peach_save_meta_data', 10, 3 );

add_filter( 'document_title_parts', 'giant_peach_render_meta_title', 10 );
add_filter( 'wp_head', 'giant_peach_render_meta_description' );

function giant_peach_add_meta_data_meta_box() {
	add_meta_box( 'giant-peach-meta-data', 'Meta Data', 'giant_peach_meta_data_meta_box_markup', null, 'normal', 'low' );
}

function giant_peach_meta_data_meta_box_markup( $object ) {
	wp_nonce_field( basename( __FILE__ ), 'meta-box-nonce' );

	$meta_box_description = 'Add the title, description and keywords for the page here.';
	$title_description = "Displayed in the browser tab and on search result pages. It should be relevant to the page's content. Defaults to the page title.";
	$description_description = 'Displayed on search result pages to give the user a preview of the page. It is recommended to be around 160 characters.';

	?>
	<div class="inside">
		<div>
	        <span class="description"><?php echo $meta_box_description;?></span>
	    </div>

        <div>
        	<br>
            <label>
            	<strong>Title</strong> - <span class="description"><?php echo $title_description;?></span><br>
            </label>
            <input style="width: 100%;" name="meta-title" value="<?php echo get_post_meta($object->ID, "meta-title", true); ?>" />
        </div>

        <div>
        	<br>
            <label>
            	<strong>Description</strong> - <span class="description"><?php echo $description_description;?></span><br>
            </label>
            <input style="width: 100%;" name="meta-description" value="<?php echo get_post_meta($object->ID, "meta-description", true); ?>" />
        </div>
    </div>

	<?php
}

function giant_peach_save_meta_data( $post_id, $post, $update ) {
	if ( ! isset( $_POST['meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['meta-box-nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	$meta_data_title = '';
	$meta_data_description = '';

	if ( isset( $_POST['meta-title'] ) ) {
		$meta_data_title = $_POST['meta-title'];
	}
	update_post_meta( $post_id, 'meta-title', $meta_data_title );

	if ( isset( $_POST['meta-description'] ) ) {
		$meta_data_description = $_POST['meta-description'];
	}
	update_post_meta( $post_id, 'meta-description', $meta_data_description );
}

function giant_peach_render_meta_title( $title ) {
	$post = get_post();
	$meta_title = get_post_meta( $post->ID, 'meta-title', true );

	if ( ! $meta_title ) {
		$meta_title = get_the_title( $post );
	}

	if ( is_home() ) {
		$title['title'] = get_bloginfo();
	} else {
		$title['title'] = $meta_title; 
	}
    
    return $title; 
}

function giant_peach_render_meta_description() {
	$meta_description = get_post_meta( get_post()->ID, 'meta-description', true );

	if ( $meta_description ) {
		echo '<meta name="description" content="' . $meta_description . '">' . "\n";
	}
}