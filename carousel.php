<?php
/*
Plugin Name: UCSF HR Carousel
Plugin URI: http://hr.ucsf.edu
Description: A Carousel that lives on the home page
Version: 1.0
Author: Rob Saurini
Author URI: http://hr.ucsf.edu
License: GPL3

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

// Ensure all js relevant to the plugin is loaded
function ucsf_carousel_js( $hook ) {

	if( 'toplevel_page_ucsfhr-carousel' != $hook )
        return;

	wp_enqueue_script(
		'ucsf_carousel_admin',
		plugins_url( '/js/admin.js' , __FILE__ ),
		array( 'jquery', 'media-upload', 'thickbox' )
	);

}

add_action( 'admin_enqueue_scripts', 'ucsf_carousel_js' );

// Load relevant styles
function ucsf_carousel_styles(){
	wp_enqueue_style('thickbox');
	wp_enqueue_style(
		'ucsf_carousel_admin_css',
		plugins_url( '/css/admin_style.css' , __FILE__ )
	);
}
add_action( 'admin_enqueue_scripts', 'ucsf_carousel_styles' );

// Add the menu page to the WP Admin menu
function ucsfhr_carousel_page(){

	add_menu_page(
		'UCSFHR Carousel',						// Text to be displayed in the title bar
		'UCSFHR Carousel',						// Text to be used for the text in title
		'manage_options',						// User capabilities
		'ucsfhr-carousel',						// Slug
		'ucsfhr_carousel_options_display',		// Name of the callback to render
		plugins_url( '/images/icon_ucsfhr_carousel.gif' , __FILE__ )									// Adds Default icon to menu
	);

}

add_action( 'admin_menu', 'ucsfhr_carousel_page' );

// Set up the settings
function ucsfhr_initialize_carousel_options() {

	// General settings like Enable/Disable Carousel
	add_settings_section(
		'carousel_options_section',			// The ID to use for this section in attribute tags
		'General',							// The title of the section rendered to the screen
		'ucsfhr_carousel_general_form',		// The function used to render the options for this section
		'ucsfhr-carousel-general'			// The ID of the page on which this section is rendered
	);

	// Settings for adding/modifying/removing slides
	add_settings_section(						
		'carousel_options_section',			// The ID to use for this section in attribute tags
		'Slides',							// The title of the section rendered to the screen
		'ucsfhr_carousel_slides_form',		// The function used to render the options for this section
		'ucsfhr-carousel-slides'			// The ID of the page on which this section is rendered
	);

	// Define the settings field
	add_settings_field( 
		'enable_carousel',					// The ID to use for this section in attribute tags
		'Enable Carousel',					// The ID to use for this section in attribute tags
		'ucsfhr_carousel_enable_field',		// The function used to render the options for this section
		'ucsfhr-carousel-general',			// The ID of the page on which this section is rendered
		'carousel_options_section'			// The section to which this field belongs
		
	);

	// Define the settings field
	add_settings_field( 
		'slides',									// The ID to use for this section in attribute tags
		'',					// The ID to use for this section in attribute tags
		'ucsfhr_carousel_slides_field',	// The function used to render the options for this section
		'ucsfhr-carousel-slides',					// The ID of the page on which this section is rendered
		'carousel_options_section'					// The section to which this field belongs	
	);

	register_setting( 
		'carousel_options_section',		// The name of the group of settings
		'ucsfhr_carousel_enabled',		// The name of the actual option ( or setting )
		'intval'						// Integers only
	);

	register_setting( 
		'carousel_options_section',		// The name of the group of settings
		'ucsfhr_carousel_slides',		// The name of the actual option ( or setting )
		'ucsfhr_carousel_sanitize'		// Custom sanitization callback
	);

}

add_action( 'admin_init', 'ucsfhr_initialize_carousel_options' );

/**
 * Callbacks
 */

// Sanitize the input from the slide settings
function ucsfhr_carousel_sanitize( $input ){

	$valid_input = array();

	foreach( $input as $slide ){
	
		if( empty( $slide[ 'title' ] ) || empty( $slide[ 'text' ] ) )
			continue;

		$slide[ 'title' ] 		= esc_html( $slide[ 'title' ] );
		$slide[ 'text' ] 		= esc_html( $slide[ 'text' ] );
		$slide[ 'link' ] 		= esc_url( $slide[ 'link' ] );
		$slide[ 'image_url' ] 	= esc_url( $slide[ 'image_url' ] );

		$valid_input[] = $slide;

	}

	return $valid_input;

}

function ucsfhr_carousel_options_display(){
?>
<div class="wrap">
	<div id="ucsfhr-icon-32" class="icon32"></div>
	<h2>UCSF HR Carousel</h2>
	<p>Manage options for the carousel</p>
	<form method="post" action="options.php">
<?php
		//Render the settings for the settings section identified as 'Footer'
		settings_fields( 'carousel_options_section' );

		// Renders all of the settings for 'ucsfhr_theme_options' section
		do_settings_sections( 'ucsfhr-carousel-general' );
		do_settings_sections( 'ucsfhr-carousel-slides' );

		// Add the submit button
		submit_button();
?>
	</form>
</div>
<?php
}

function ucsfhr_carousel_general_form(){
	echo 'General settings that affect the carousel.';
}

function ucsfhr_carousel_enable_field(){
	
	$enabled = get_option( 'ucsfhr_carousel_enabled' );
	
	echo '<input type="checkbox" name="ucsfhr_carousel_enabled" id="ucsfhr_carousel_enabled" value="1" ' . checked( 1, $enabled, false ) . ' />';

}

function ucsfhr_carousel_number_of_slides_field(){

	$number_of_slides = (int) get_option( 'ucsfhr_carousel_num_slides' );

	echo '<input type="text" max="2" size="2" name="ucsfhr_carousel_num_slides" id="ucsfhr_carousel_num_slides" value="' . $number_of_slides . '" />';

}

function ucsfhr_carousel_slides_form(){

	$slides = get_option( 'ucsfhr_carousel_slides' );
	
	// Make sure there are slides, then display
	if( ! empty( $slides ) ){

		$slide_number = 0;

		foreach( $slides as $slide ){

			$slide_number++;

			echo <<< SLIDEHTML

<div class="slide">
	<h3>Slide #{$slide_number} (<span class="delete">Delete</span>)</h3>
	
	<input type="hidden" name="ucsfhr_carousel_slides[{$slide['id']}][id]" class="id" value="{$slide['id']}">
	<input type="hidden" name="ucsfhr_carousel_slides[{$slide['id']}][image_url]" class="image-url" value="{$slide['image_url']}" size="50">
	<div class="text-title-link-container">
		<div class="title-container">
			<p class="label">Slide Title</p>
			<input type="text" name="ucsfhr_carousel_slides[{$slide['id']}][title]" class="title" value="{$slide['title']}">
		</div>
		<div class="text-container">
			<p class="label">Slide Text</p>
			<textarea class="text" rows="4" cols="50" name="ucsfhr_carousel_slides[{$slide['id']}][text]">{$slide['text']}</textarea>
		</div>
		<div class="link-container">
			<p class="label">Read More Link</p>
			<input type="text" class="link" name="ucsfhr_carousel_slides[{$slide['id']}][link]" value="{$slide['link']}">
		</div>
	</div>
	<div class="image-display-container">
		<img class="slide-image-display" src="{$slide['image_url']}">
		<div class="upload-button-container">
			<button class="image-upload-button" type="button" value="Upload Image">Modify Image</button>
		</div>
	</div>
</div>

SLIDEHTML;

		}

	}
?>
	<div id="new-slides"></div>
	<div id="new-slide-button-container">
		<span class="new-slide-button" id="ucsfhr-add-slide">Add New Slide</span>
	</div>
<?php
}

function ucsfhr_carousel_slides_field(){
?>
<?php
}

/**
 * Front-end display funcs
 */

function ucsfhr_enqueue_scripts(){

	// Enqueue Scripts for front-end
	wp_enqueue_script(
		'ucsf_carousel_carouFredSel',
		plugins_url( '/js/jquery.carouFredSel-6.2.1-packed.js' , __FILE__ ),
		array( 'jquery' )
	);

	wp_enqueue_style(
		'ucsfhr_carousel_style',
		plugins_url( '/css/fe_style.css' , __FILE__ )
	);
/*
	wp_enqueue_script(
		'ucsf_carousel_fe',
		plugins_url( '/js/ucsfhr_carousel_fe.js' , __FILE__ ),
		array( 'jquery' )
	);
*/
}
add_action( 'wp_enqueue_scripts', 'ucsfhr_enqueue_scripts' );

function ucsfhr_carousel(){



	$enabled = (int) get_option( 'ucsfhr_carousel_enabled' );

	$slides = (array) get_option( 'ucsfhr_carousel_slides' );
	
	if( empty( $enabled ) || $enabled != 1 )
		return;

	$slide_count = count( $slides );

	if( $slide_count < 1 )
		return;
?>
	<div id="ucsfhr-carousel-wrapper">
		<div id="ucsfhr-carousel">
<?php
	foreach( $slides as $slide ){

		echo <<< SLIDEHTML
			<div class="slide slide{$slide['id']}"  style="background: url( {$slide['image_url']} ) 0 0 no-repeat;">
				<div class="slide-content">
					<h3>{$slide['title']}</h3>
					<p>{$slide['text']}</p>
					<a href="{$slide['link']}">Read More</a>
				</div>
			</div>
SLIDEHTML;

	}

?>
		</div>
	</div>
	<div id="ucsfhr-carousel-pagination"></div>
<script type="text/javascript">
( function( $ ) {
		var $carousel = $( '#ucsfhr-carousel' );
		var $wrapper = $( '#ucsfhr-carousel-wrapper' );
		var $window = $( window );
	 
		$carousel.carouFredSel({
			width: '100%',
			scroll: 1,
			items: {
<?php if( $slide_count > 3 ): ?>
				visible: 'odd+2',
<?php else: ?>
				visible: 1,
<?php endif; ?>
				start: -1
			},
			auto: {
				timeoutDuration: 8000
			},
			pagination: {
				container: $( '#ucsfhr-carousel-pagination' )
			}
	});

} )( jQuery );
</script>
<?php
		

}