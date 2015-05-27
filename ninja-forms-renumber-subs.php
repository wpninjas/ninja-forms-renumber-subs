<?php
/*
Plugin Name: Ninja Forms - Re-number Submissions
Plugin URI: http://ninjaforms.com/
Description: Adds a button to reset submission sequential numbers to the submissions screen.
Version: 1.0
Author: The WP Ninjas
Author URI: http://ninjaforms.com
Text Domain: ninja-forms-seq
Domain Path: /lang/

Copyright 2014 WP Ninjas.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class NF_Renumber_Subs
{
	function __construct()
	{
		/*
		Define our constants
		 */
		
		if ( ! defined( 'NF_RENUMBER_SUBS_URL' ) ) {
			define( 'NF_RENUMBER_SUBS_URL', plugin_dir_url( __FILE__ ) );
		}		

		if ( ! defined( 'NF_RENUMBER_SUBS_DIR' ) ) {
			define( 'NF_RENUMBER_SUBS_DIR', plugin_dir_path( __FILE__ ) );
		}

		add_action( 'nf_init', array( $this, 'init' ) );
	}

	public function init()
	{
		/*
		Bail if Ninja Forms isn't active.
		 */
		if ( ! is_admin() ) {
			return false;
		}
		/*
		Require our step processor class
		 */
		require_once( NF_RENUMBER_SUBS_DIR . 'includes/class-reset-seq-num.php' );

		add_action( 'admin_print_styles', array( $this, 'load_js' ), 11 );
		add_action( 'admin_print_styles', array( $this, 'load_css' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'add_button' ), 11 );		
	}

	/**
	 * Enqueue our submissions JS file.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function load_js()
	{
		global $pagenow, $typenow;
		// Bail if we aren't on the edit.php page or we aren't editing our custom post type.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;

		$form_id = isset ( $_REQUEST['form_id'] ) ? $_REQUEST['form_id'] : '';

		ninja_forms_admin_js();

		wp_enqueue_script( 'nf-reset-subs',
			NF_RENUMBER_SUBS_URL . 'assets/js/reset-subs.js',
			array( 'subs-cpt', 'jquery', 'jquery-ui-datepicker') );

		wp_localize_script( 'nf-reset-subs', 'nf_reset_subs', array( 'reset_seq_num_title' => __( 'Re-number Submissions', 'ninja-forms' ) ) );

	}

	/**
	 * Enqueue our submissions CSS file.
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function load_css()
	{
		global $pagenow, $typenow;

		// Bail if we aren't on the edit.php page or the post.php page.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;

		ninja_forms_admin_css();
	}

	/**
	 * Add our button to the CPT page
	 */
	public function add_button()
	{
		global $pagenow, $typenow;

		// Bail if we aren't on the edit.php page or the post.php page.
		if ( ( $pagenow != 'edit.php' && $pagenow != 'post.php' ) || $typenow != 'nf_sub' )
			return false;

		$redirect = urlencode( remove_query_arg( array( 'download_all', 'download_file' ) ) );
		$reset_url = admin_url( 'admin.php?page=nf-processing&action=reset_seq_num&form_id=' . $_REQUEST['form_id'] . '&redirect=' . $redirect );
		$reset_url = esc_url( $reset_url );
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function() {
				var reset_button = '<a href="#" class="button-secondary nf-reset-seq-num"><?php echo __( 'Re-number Submissions', 'ninja-forms' ); ?></a>';
				jQuery( '.nf-download-all' ).after( reset_button );
			} );
		</script>

		<div class="reset-seq-num" style="display:none;"> <!-- adding display:none; will keep the element from flashing on load. -->
			<?php _e( 'This will re-number all submissions for this form beginning with 1.', 'ninja-forms' ); ?>
		</div>
		<div class="reset-seq-num-buttons" style="display:none;"> <!-- the buttons div defines the bottom of the modal.-->
			<div id="nf-admin-modal-cancel"> <!-- nf-admin-modal-cancel is the class that aligns the button to the left. -->
	            	<a class="submitdelete deletion modal-close" href="#"><?php _e( 'Cancel', 'ninja-forms' ); ?></a>
	        	</div>
			<div id="nf-admin-modal-update"> <!-- nf-admin-modal-update is the class that aligns the button to the right. -->
	            	<a class="button-primary" href="<?php echo $reset_url; ?>"><?php _e( 'Continue', 'ninja-forms' ); ?></a>
			</div>
		</div>
		<?php
	}
}

new NF_Renumber_Subs();