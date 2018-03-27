<?php

/**
 * Define the processed status
 *
 * @link       https://github.com/thermesmarins/
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Customeramdin
 * @subpackage Tmsm_Woocommerce_Customadmin/includes
 */

class Tmsm_Woocommerce_Customadmin_Processedstatus {


	/**
	 * Register post status: processed
	 */
	public function register_post_status_processed() {
		register_post_status( 'wc-processed', array(
			'label'                     => __('Processed', 'tmsm-woocommerce-vouchers'),
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			'label_count'               => _n_noop( 'Processed <span class="count">(%s)</span>',
				'Processed <span class="count">(%s)</span>', 'tmsm-woocommerce-vouchers' ),
		) );
	}


}
