/* global ajaxurl, jQuery */

( function( $ ) {

	'use strict';

	var save = function( e ) {

		e.preventDefault();

		var $this    = $( this ),
				submit  = $this.find( 'button' ),
				spinner = $this.find( 'img' ),
				data     = {
				'action': 'rstore_advanced_save',
				'pl_id': $('#pl_id').val(),
				'currency': $('#currency').val(),
				'api_tld': $('#api_tld').val(),
				'api_tld_override': $('#api_tld_override').prop('checked')? 1 : 0
			};

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, data, function( response ) {
			console.dir(response);
			submit.prop( 'disabled', false );
			spinner.css( 'visibility', 'hidden' );
			if ( response.success ) {

				// window.location.replace( response.data.redirect );

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
