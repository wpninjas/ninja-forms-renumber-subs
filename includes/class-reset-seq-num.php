<?php if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('NF_Step_Processing' ) ) {
	return false;
}

class NF_Reset_Seq_Num extends NF_Step_Processing {

	function __construct() {
		$this->action = 'reset_seq_num';

		parent::__construct();
	}

	public function loading() {

		$form_id  = isset( $this->args['form_id'] ) ? absint( $this->args['form_id'] ) : 0;

		if ( empty( $form_id ) ) {
			return array( 'complete' => true );
		}
			
	 	$sub_count = nf_get_sub_count( $form_id );

		if( empty( $this->total_steps ) || $this->total_steps <= 1 ) {
			$this->total_steps = round( ( $sub_count / 250 ), 0 ) + 2;
		}

		$args = array(
			'total_steps' => $this->total_steps,
		);

		return $args;
	}

	public function step() {
		
		$args = array(
			'posts_per_page' 	=> 250,
			'paged' 			=> $this->step,
			'post_type' 		=> 'nf_sub',
			'orderby'			=> 'date',
			'order'				=> 'ASC',
			'meta_query' 		=> array(
				array( 
					'key' 		=> '_form_id',
					'value' 	=> $this->args['form_id'],
				),
			),
		);

		$subs_results = get_posts( $args );

		if ( is_array( $subs_results ) && ! empty( $subs_results ) ) {
			$x = 1;
			foreach ( $subs_results as $sub ) {
				Ninja_Forms()->sub( $sub->ID )->update_seq_num( $x );
				$x++;
			}
		}
	}

	public function complete() {
		
	}
}

return new NF_Reset_Seq_Num();