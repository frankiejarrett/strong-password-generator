/* globals generatePassword, spg_button */

jQuery( document ).ready( function( $ ) {
	var spg_button_html = '<p><a href="#" id="spg-button" class="button button-secondary">' + spg_button.i18n.button + '</a></p><br>';

	$( '#pass-strength-result' ).before( spg_button_html );

	$( document ).on( 'click', '#spg-button', function( e ) {
		e.preventDefault();

		var pass = generatePassword(
			parseInt( spg_button.length, 10 ),
			( true == spg_button.memorable )
		);

		$( '#pass1' ).val( pass ).trigger( 'input' );
		$( '#pass2' ).val( pass );

		window.alert( spg_button.i18n.alert + "\n\n" + pass );
	});
});
