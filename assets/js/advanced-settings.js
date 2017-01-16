/* global ajaxurl, jQuery */

( function( $ ) {

	'use strict';

	var save = function( e ) {

		e.preventDefault();
		var $this = $( this ),
			submit  = $this.find( 'button' ),
			spinner = $this.find( 'img' ),
			data;

		$this.find("[name='action']").val('rstore_advanced_save');
		data = $( this ).serialize();

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {
			console.dir(response);
			submit.prop( 'disabled', false );
			spinner.css( 'visibility', 'hidden' );
			if ( response.success ) {
				return false;
			}

			window.console.log( response );

			window.alert( response.data );

		} );

	};

	$( document ).ready( function( $ ) {

		$( '#rstore-settings-form' ).on( 'submit', save );

	} );

} )( jQuery );
