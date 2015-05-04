/* globals generatePassword, spg_button */

jQuery( document ).ready( function( $ ) {
	var spg_length     = parseInt( spg_button.length, 10 ),
	    spg_min_length = parseInt( spg_button.min, 10 ),
	    spg_max_length = parseInt( spg_button.max, 10 ),
	    spg_html       = '<div class="spg-container"><p><a href="#" id="spg-button" class="button button-secondary button-large"><span class="dashicons dashicons-admin-network"></span> ' + spg_button.i18n.button + '</a></p><div id="spg-controls"><p>' + spg_button.i18n.range + ' <input type="range" name="spg-length" id="spg-length" min="' + spg_min_length + '" max="' + spg_max_length + '" value="' + spg_length + '"><span id="spg-display-length">' + spg_length + '</span></p><p><code id="spg-display-pass"></code></p></div></div>';

	$( '#pass-strength-result' ).before( spg_html );

	$( document ).on( 'click', '#spg-button', function( e ) {
		e.preventDefault();

		$( this ).addClass( 'active' );
		$( '#spg-controls' ).slideDown();

		spgGeneratePassword();
	});

	$( document ).on( 'input', '#spg-length', function() {
		var length = $( this ).val();

		$( '#spg-display-length' ).text( length );

		spgGeneratePassword();
	});

	$( document ).on( 'focus', 'input#pass1, input#pass2', function() {
		$( 'input#pass1' ).val( '' );
		$( 'input#pass2' ).val( '' );
		$( '#spg-button' ).removeClass( 'active' );
		$( '#spg-controls' ).slideUp( 'fast', function() {
			$( '#spg-length' ).val( spg_length );
			$( '#spg-display-length' ).text( spg_length );
		});
	});

	function spgGeneratePassword() {
		var length = parseInt( $( '#spg-length' ).val(), 10 ),
		    pass   = generatePassword( length, ( '' !== spg_button.memorable ) );

		$( '#spg-display-pass' ).text( pass );
		$( 'input#pass1' ).val( pass );
		$( 'input#pass2' ).val( pass ).trigger( 'input' );
	}
});
