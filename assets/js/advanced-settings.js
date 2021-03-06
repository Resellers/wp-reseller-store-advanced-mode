/* global ajaxurl, jQuery */

( function( $ ) {
	'use strict';

	var save = function( e ) {
		e.preventDefault();
		var $this = $( this ),
			submit = $this.find( 'button' ),
			spinner = $this.find( 'img' );

		$this.find( "[name='action']" ).val( 'rstore_settings_save' );

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			submit.prop( 'disabled', false );
			spinner.css( 'visibility', 'hidden' );
			if ( response.success ) {
				return false;
			}

			// eslint-disable-next-line no-alert
			window.alert( response.data );
		} );
	};

	var exportProduct = function( e ) {
		e.preventDefault();
		var $this = $( this );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			if ( response ) {
				$( '#json-text' ).text( JSON.stringify( response ) );
				$( '#json-generator' ).show();
			}
		} );
		return false;
	};

	var importProduct = function( e ) {
		e.preventDefault();
		var $this = $( this );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			if ( response.success ) {
				window.location.replace( response.data.redirect );

				return false;
			}
		} );
		return false;
	};

	$( document ).ready( function() {
		$( '#rstore-settings-form' ).on( 'submit', save );
		$( '#rstore-settings-export' ).on( 'submit', exportProduct );
		$( '#rstore-settings-import' ).on( 'submit', importProduct );
	} );
} )( jQuery );
