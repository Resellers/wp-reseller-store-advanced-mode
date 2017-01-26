/* global ajaxurl, jQuery */

( function( $ ) {

	'use strict';

	var save = function( e ) {

		e.preventDefault();
		var $this = $( this ),
			submit  = $this.find( 'button' ),
			spinner = $this.find( 'img' );

		$this.find("[name='action']").val('rstore_advanced_save');

		submit.prop( 'disabled', true );
		spinner.css( 'visibility', 'visible' );

		$.post( ajaxurl, $this.serialize(), function( response ) {
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

	var exportProduct = function( e ) {

		e.preventDefault();
		var $this = $( this );

		$.post( ajaxurl, $this.serialize(), function( response ) {
			if ( response ) {
				console.log(response);
				$('#json-text').text(JSON.stringify(response));
				$.magnificPopup.open({
      		mainClass: 'mfp-zoom-in',
      		items: {
        		src: '#json-generator'
      		},
      		type: 'inline',
      		removalDelay: 500
    		}, 0);
			}
		} );
		return false;

	};

	$( document ).ready( function( $ ) {

		$( '#rstore-settings-form' ).on( 'submit', save );
		$( '#rstore-settings-export' ).on( 'submit', exportProduct );
		new Clipboard('#clipboard');


	} );

} )( jQuery );
