/* globals generatePassword */

jQuery( document ).ready( function( $ ) {
	var spg_length    = parseInt( $( '#spg-default-length' ).val(), 10 ),
	    spg_memorable = parseInt( $( '#spg-memorable' ).val(), 10 ),
	    $spg_html     = $( '.spg-container' );

	$( '#pass-strength-result' ).before( $spg_html );

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
		$( 'input#pass2' ).val( '' ).trigger( 'input' );
		$( '#spg-button' ).removeClass( 'active' );
		$( '#spg-controls' ).slideUp( 'fast', function() {
			$( '#spg-length' ).val( spg_length );
			$( '#spg-display-length' ).text( spg_length );
		});
	});

	function spgGeneratePassword() {
		var length = parseInt( $( '#spg-length' ).val(), 10 ),
		    pass   = generatePassword( length, ( 1 === spg_memorable ) );

		$( '#spg-display-pass' ).text( pass );
		$( 'input#pass1' ).val( pass );
		$( 'input#pass2' ).val( pass ).trigger( 'input' );
	}
});
