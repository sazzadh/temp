<?php

if (!class_exists('cs_candidate_fnctions')) {

    class cs_candidate_fnctions {

	public function __construct() {
	    
	}

	public function cs_date_conv($cs_duration, $cs_format) {
	    if ($cs_format == "days") {
		$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " days"));
	    } elseif ($cs_format == "months") {
		$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " months"));
	    } elseif ($cs_format == "years") {
		$cs_adexp = date('Y-m-d H:i:s', strtotime("+" . $cs_duration . " years"));
	    } else {
		$cs_adexp = '';
	    }
	    return $cs_adexp;
	}

	public function cs_manage_connects_after_job_applied_callback($connects_args) {

	    $is_job_invited = false;

	    if ($is_job_invited == false) {
		$cs_trans_id = $connects_args['trans_id'];
		$cs_candi_id = $connects_args['candidate_id'];

		$cs_current_connects = get_post_meta($cs_trans_id, 'cs_transaction_connects_remaining', true);
		$cs_remaining_connects = $cs_current_connects - 1;

		$cs_current_connects_used = get_post_meta($cs_trans_id, 'cs_transaction_connects_used', true);
		$cs_connects_used = $cs_current_connects_used + 1;

		update_post_meta($cs_trans_id, 'cs_transaction_connects_used', $cs_connects_used);
		update_post_meta($cs_trans_id, 'cs_transaction_connects_remaining', $cs_remaining_connects);
	    }
	}

	public function cs_candidate_pay_process($values = array()) {
	    $cs_transaction_fields = $values;
	    extract($values);
	    $cs_trans_id = isset($cs_trans_id) ? $cs_trans_id : '';
	    $cs_trans_user = isset($cs_trans_user) ? $cs_trans_user : '';
	    $cs_trans_pkg = isset($cs_trans_package) ? $cs_trans_package : '';
	    $cs_trans_featured = isset($cs_trans_featured) && $cs_trans_featured == 'on' ? 'yes' : 'no';
	    $cs_trans_amount = isset($cs_trans_amount) ? $cs_trans_amount : '';
	    $cs_trans_pkg_expiry = isset($cs_trans_pkg_expiry) ? $cs_trans_pkg_expiry : '';
	    $s_trans_pkg_expiry_dur = isset($s_trans_pkg_expiry_dur) ? $s_trans_pkg_expiry_dur : '';
	    $cs_trans_pkg_expiry_dur_period = isset($cs_trans_pkg_expiry_dur_period) ? $cs_trans_pkg_expiry_dur_period : '';
	    $cs_trans_pkg_connects = isset($cs_trans_pkg_connects) ? $cs_trans_pkg_connects : '';
	    $cs_trans_pkg_connects_rollover = isset($cs_trans_pkg_connects_rollover) ? $cs_trans_pkg_connects_rollover : '';
	    $post_author = $cs_trans_user;

	    $transaction_post = array(
		'post_title' => '#' . $cs_trans_id,
		'post_status' => 'publish',
		'post_author' => $post_author,
		'post_type' => 'cs-transactions',
		'post_date' => current_time('Y-m-d H:i:s')
	    );

	    $trans_id = wp_insert_post($transaction_post);
	    $cs_trans_pay_method = isset($_POST['cs_payment_gateway']) ? $_POST['cs_payment_gateway'] : '';

	    $cs_trans_array = array(
		'transaction_id' => $cs_trans_id,
		'transaction_user' => $cs_trans_user,
		'transaction_feature' => $cs_trans_featured,
		'transaction_package' => $cs_trans_pkg,
		'transaction_amount' => $cs_trans_amount,
		'transaction_currency_sign' => jobcareer_get_currency_sign(),
		'transaction_currency_position' => jobcareer_get_currency_position(),
		'transaction_pay_method' => $cs_trans_pay_method,
		'transaction_expiry_date' => $cs_trans_pkg_expiry,
		'transaction_connects' => $cs_trans_pkg_connects,
		'transaction_connects_used' => 0,
		'transaction_connects_remaining' => $cs_trans_pkg_connects,
		'transaction_connects_rollever' => $cs_trans_pkg_connects_rollover,
	    );
	    if ($cs_trans_amount <= 0) {
		$cs_trans_array['transaction_status'] = esc_html__('approved', 'jobhunt');
	    }

	    if (isset($cs_trans_only_featued) && $cs_trans_only_featued == 'yes') {
		$cs_trans_array['transaction_type'] = 'job_apply';
	    } else {
		$cs_trans_array['transaction_type'] = 'job_apply';
	    }
	    foreach ($cs_trans_array as $trans_key => $trans_val) {
		update_post_meta($trans_id, "cs_{$trans_key}", $trans_val);
	    }
	    $cs_transaction_fields['cs_order_id'] = $trans_id;

	    if ($cs_trans_amount > 0) {
		if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'cs_wooC_GATEWAY' && !empty($cs_transaction_fields)) {
		    global $Payment_Processing;
		    $payment_args = array(
			'package_id' => $cs_transaction_fields['cs_trans_id'],
			'package_name' => $cs_transaction_fields['cs_package_title'],
			'price' => $cs_transaction_fields['cs_trans_amount'],
			'custom_var' => array(
			    'cs_order_id' => $cs_transaction_fields['cs_order_id'],
			),
			'redirect_url' => get_option('wooC_current_page')
		    );
		    $Payment_Processing->processing_payment($payment_args);
		} else if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PAYPAL_GATEWAY' && !empty($cs_transaction_fields)) {
		    $paypal_gateway = new CS_PAYPAL_GATEWAY();
		    $paypal_gateway->cs_proress_request($cs_transaction_fields);
		} else if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_AUTHORIZEDOTNET_GATEWAY' && !empty($cs_transaction_fields)) {
		    $authorizedotnet = new CS_AUTHORIZEDOTNET_GATEWAY();
		    $authorizedotnet->cs_proress_request($cs_transaction_fields);
		} else if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_SKRILL_GATEWAY' && !empty($cs_transaction_fields)) {
		    $skrill = new CS_SKRILL_GATEWAY();
		    $skrill->cs_proress_request($cs_transaction_fields);
		} else if (isset($_POST['cs_payment_gateway']) && $_POST['cs_payment_gateway'] == 'CS_PRE_BANK_TRANSFER' && !empty($cs_transaction_fields)) {
		    $banktransfer = new CS_PRE_BANK_TRANSFER();
		    return $banktransfer->cs_proress_request($cs_transaction_fields);
		} else {
		    // Do Nothing
		}
	    }
	}

	public function cs_is_membership_pkg_subscribed($cs_package = array()) {
	    global $post, $current_user;
	    $cs_candidate_func = new cs_candidate_fnctions();
	    $cs_current_date = strtotime(current_time('d-m-Y'));

	    $args = array(
		'posts_per_page' => "1",
		'post_type' => 'cs-transactions',
		'post_status' => 'publish',
		'meta_query' => array(
		    'relation' => 'AND',
		    array(
			'key' => 'cs_transaction_user',
			'value' => $current_user->ID,
			'compare' => '=',
		    ),
		    array(
			'key' => 'cs_transaction_expiry_date',
			'value' => $cs_current_date,
			'compare' => '>',
		    ),
		    array(
			'key' => 'cs_transaction_status',
			'value' => 'approved',
			'compare' => '=',
		    ),
		    array(
			'key' => 'cs_transaction_connects_remaining',
			'value' => 0,
			'compare' => '>',
		    ),
		    array(
			'key' => 'cs_transaction_package',
			'value' => $cs_package,
			'compare' => 'IN',
		    ),
		),
	    );
	    $return = array();
	    $custom_query = new WP_Query($args);
	    $cs_trans_count = $custom_query->found_posts;
	    $return['cs_trans_count'] = $cs_trans_count;
	    if ($cs_trans_count != 0) {
		while ($custom_query->have_posts()): $custom_query->the_post();
		    global $post;
		    $cs_trans_id = $post->ID;
		endwhile;

		$return['cs_trans_id'] = $cs_trans_id;
	    }
	    else {
		$return['cs_trans_id'] = 0;
	    }

	    return $return;
	}

    }

    $cs_candidate_func = new cs_candidate_fnctions();
    add_action('johunt_manage_connects_after_job_applied', array($cs_candidate_func, 'cs_manage_connects_after_job_applied_callback'));
}

