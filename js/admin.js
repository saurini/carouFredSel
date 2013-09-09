/*
Admin Javscript for UCSFHR Carousel
*/

( function( $ ){

	$( document ).on ( 'click', function( e ){

		var target_id = e.target.id || '';
		var target_class = $( e.target ).prop( 'class' );

		switch( target_id ){

			case 'ucsfhr-add-slide':
				add_slide();
			break;

			default:
			break;

		}

		switch( target_class ){

			case 'image-upload-button':
				handle_upload( e.target );
			break;

			case 'delete':
				handle_delete( e.target );
			break;

			default:
			break;

		}

	});

	function add_slide(){

		var new_slide = 0;
		
		var last_slide = $( '.slide' ).last();
		console.log( last_slide );
		// If there are no slides the new_slide_id will be 1
		if( last_slide.length == 0 )
			new_slide = 1;
		else
			new_slide = parseInt( last_slide.children( 'input.id' ).val() ) + 1;

		// Verify the slide id was found
		if( new_slide == 0 )
			return;

		// The elements to add to the DOM
		var slide_div 				= $( '<div class="slide" />' );

		// Hidden fields	
		var slide_id 				= $( '<input type="hidden" name="ucsfhr_carousel_slides['+new_slide+'][id]" class="id" value="'+new_slide+'" />' );
		var slide_image_url 		= $( '<input type="hidden" name="ucsfhr_carousel_slides['+new_slide+'][image_url]" class="image-url" value="" size="50" />' );

		// The visible fields
		var slide_number_container	= $( '<h3 class="slide-number-container">Slide #'+new_slide+' (<span class="delete">Delete</span>)</h3>' );

		var slide_title_label		= $( '<p class="label">Slide Title</p>' );
		var slide_title 			= $( '<input type="text" name="ucsfhr_carousel_slides['+new_slide+'][title]" class="title" value="" />' );
		var title_container 		= $( '<div class="title-container" /> ' ).append( slide_title_label, slide_title );

		var slide_text_label	 	= $( '<p class="label">Slide Text</p>' );
		var slide_text 				= $( '<textarea class="text" rows="4" cols="50" name="ucsfhr_carousel_slides['+new_slide+'][text]" />' );
		var text_container			= $( '<div class="text-container" />' ).append( slide_text_label, slide_text );

		var slide_link_label		= $( '<p class="label">Read More Link</p>' );
		var slide_link 				= $( '<input type="text" class="link" name="ucsfhr_carousel_slides['+new_slide+'][link]" value="" />')
		var link_container	= $( '<div class="link-container" />').append( slide_link_label, slide_link );

		var text_title_link			= $( '<div class="text-title-link-container" />' ).append( title_container, text_container, link_container );

		var slide_upload_button 	= $( '<button class="image-upload-button" type="button" value="Upload Image">Upload Image</button>' );
		var upload_button_container	= $( '<div class="upload-button-container" />' ).append( slide_upload_button );

		var slide_image_display		= $( '<img class="slide-image-display" />' );
		var image_display_container	= $( '<div class="image-display-container" />' ).append( slide_image_display, upload_button_container );
/*<div class="slide-display-options">
		<p>Slide Text Alignment</p>
		{$alignment_html}
		<p>Text width</p>
		<input type="text" name="ucsfhr_carousel_slides[{$slide[$slide_number]}][text_width]" class="text-width" value="{$slide['text_width']}">
	</div>*/
		var slide_display_alignment_label = $( '<p class="label">Text Alignment</p>' );
		var slide_display_alignment_select = $( '<select name="ucsfhr_carousel_slides['+new_slide+'][text_alignment]" class="text-alignment"><option value="right">Default</option><option value="left">Left</option><option value="right">Right</option></select>' );
		var slide_display_width_label = $( '<p class="label">Text Width</p>' );
		var slide_display_width_input = $( '<input type="text" name="ucsfhr_carousel_slides['+new_slide+'][text_width]" class="text-width" value="">' );
		var slide_display_options_container = $( '<div class="slide-display-options" />' ).append( slide_display_alignment_label, slide_display_alignment_select, slide_display_width_label, slide_display_width_input );

		slide_div.append( slide_id, slide_image_url, slide_number_container, text_title_link, image_display_container, slide_display_options_container );

		$( '#new-slides' ).append( slide_div );

	}

	// Handle enabling/disabling (removing ) a slide
	function handle_delete( target ){

		$( target ).parents( '.slide' ).remove();

	}

	// Handle file uploads
	var image_url_field;

	function handle_upload( target ){

		image_url_field = $( target ).parents( '.slide' ).find( 'input.image-url' );

		tb_show('','media-upload.php?TB_iframe=true');		
 
        return false;

	}
 
    window.original_send_to_editor = window.send_to_editor;

    window.send_to_editor = function( html ){

        var url = '';

        if ( image_url_field ) {
            
            url = $( 'img', html ).attr( 'src' );
            
            $( image_url_field ).val( url );

            var image_display = $( image_url_field ).parents( '.slide' ).find( '.slide-image-display' );

            image_display.attr( 'src', url );
            
            image_display.parents( '.image-display-container' ).slideDown( '250' );
 
            image_url_field = null;
            tb_remove();
        
        } else {
        
            window.original_send_to_editor(html);
        
        }
    
    };

})( jQuery );