jQuery(document).ready(function($) {
	/*
	Instantiate the modal
	*/
	$( '.reset-seq-num' ).nfAdminModal( { title: nf_sub.reset_seq_num_title, buttons: '.reset-seq-num-buttons' } );

	$( document ).on( 'click', '.nf-reset-seq-num', function( e ) {
		e.preventDefault();
		$( '.reset-seq-num' ).nfAdminModal( 'open' );
	} );

});