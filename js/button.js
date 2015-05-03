/* globals generatePassword, spg_button */

jQuery( document ).ready( function( $ ) {
	var spg_button_html = '<p class="spg-button-container"><a href="#" id="spg-button" class="button button-secondary button-large"><span class="dashicons dashicons-admin-network"></span> ' + spg_button.i18n.button + '</a></p>';

	$( '#pass-strength-result' ).before( spg_button_html );

	$( document ).on( 'click', '#spg-button', function( e ) {
		e.preventDefault();

		var pass = generatePassword( parseInt( spg_button.length, 10 ), ( '' !== spg_button.memorable ) );

		$( 'input#pass1' ).val( pass );
		$( 'input#pass2' ).val( pass ).trigger( 'input' );

		window.alert( spg_button.i18n.alert + '\n\n' + pass );
	});
});
