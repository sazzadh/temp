<?php

/*
 * @Shortcode Name : Job Package
 * Start Function how to create Shortcode of Job Package
 */
if (!function_exists('cs_memberhsip_package_shortcode')) {

    function cs_memberhsip_package_shortcode($atts) {
	global $post, $current_user, $cs_form_fields2, $cs_plugin_options;
	$cs_user_apply_job = isset($cs_plugin_options['cs_user_apply_job']) ? $cs_plugin_options['cs_user_apply_job'] : '';
	$defaults = array(
	    'column_size' => '',
	    'membership_package_title' => '',
	    'membership_package_style' => '',
	    'membership_packages' => '',
	    'membership_package_columns' => '4',
	);
	extract(shortcode_atts($defaults, $atts));

	$column_class = '';
	if ($column_size != '') {
	    $column_class = jobcareer_custom_column_class($column_size);
	}
	if ($membership_package_columns <> '') {
	    $grid_columns = 12 / $membership_package_columns;
	} else {
	    $grid_columns = 3;
	}
	$cs_html = '';
	if ($column_class != '') {
	    $cs_html .= '<div class="' . $column_class . '">';
	}
	$cs_plugin_options = get_option('cs_plugin_options');
	if (class_exists('cs_employer_functions')) {
	    $cs_emp_funs = new cs_employer_functions();
	}
	if ($membership_package_title != '') {
	    $cs_html .= '<div class="cs-element-title"><h2>' . $membership_package_title . '</h2></div>';
	}
	$membership_packages = explode(',', $membership_packages);
	$currency_sign = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';
	$cs_candidate_dashboard = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : '';
	$cs_membership_pkgs_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';

	$user_role = cs_get_loginuser_role();

	global $cs_plugin_options;




	if (is_user_logged_in() && $user_role == 'cs_employer') {
	    $cs_html .= '<div id="cs-not-emp" class="alert alert-warning" style="display:none;">' . esc_html__('Become an Candidate first to Subscribe the Package.', 'jobhunt') . '<a href="#" class="close" data-dismiss="alert">&times;</a></div>';
	} else {
	    $cs_html .= '<div id="cs-not-emp" class="alert alert-warning" style="display:none;">' . esc_html__('Packages Are Currently Disabled by Admin.', 'jobhunt') . '<a href="#" class="close" data-dismiss="alert">&times;</a></div>';
	}


	$rand_id = rand(0, 9999999);
	$cs_html .= '<div class="cs-packeges" id="cs-cv-form' . $rand_id . '" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
	if ($membership_package_style == 'basic') {
	    $cs_html .= '<div class="price-tables">';
	}


	if ($membership_package_style == 'classic' || $membership_package_style == 'fancy' || $membership_package_style == 'modern' || $membership_package_style == 'advance') {
	    $cs_html .= '<div class="row">';
	}
//        $cs_html    .=  $column_size.'<br>';
//        $cs_html    .=  $membership_package_title.'<br>';
//        $cs_html    .=  $membership_package_style.'<br>';
//        $cs_html    .=  $membership_packages.'<br>';
//        $cs_html    .=  $membership_package_columns.'<br>';
	if (is_array($cs_membership_pkgs_options) && sizeof($cs_membership_pkgs_options) > 0) {
	    if ($membership_package_style == 'simple') {
		$cs_html .= '<ul class="cs-pricetable">';
	    }
	    if ($membership_package_style == 'modern') {
		$cs_html .= '<ul class="cs-pricetable fancy">';
	    }
	    $cs_membership_pkg_counter = 0;

	    $package_feature = '';
	    foreach ($cs_membership_pkgs_options as $package_key => $package) {
		if (isset($package_key) && $package_key <> '' && in_array($package_key, $membership_packages)) {
		    $cs_rand_id = rand(53445, 65765);
		    $membership_pkg_id = isset($package['membership_pkg_id']) ? $package['membership_pkg_id'] : '';
		    $memberhsip_pkg_title = isset($package['memberhsip_pkg_title']) ? $package['memberhsip_pkg_title'] : '';
		    $memberhsip_pkg_price = isset($package['memberhsip_pkg_price']) ? $package['memberhsip_pkg_price'] : '';
		    $memberhsip_pkg_connects = isset($package['memberhsip_pkg_connects']) ? $package['memberhsip_pkg_connects'] : '';
		    $membership_pkg_dur = isset($package['membership_pkg_dur']) ? $package['membership_pkg_dur'] : '';
		    $membership_pkg_dur_period = isset($package['membership_pkg_dur_period']) ? $package['membership_pkg_dur_period'] : '';
		    $membership_pkg_desc = isset($package['membership_pkg_desc']) ? $package['membership_pkg_desc'] : '';
		    $package_desc = isset($package['package_description']) ? $package['package_description'] : '';
		    $cs_membership_pkg_connects_rollover = isset($package['cs_membership_pkg_connects_rollover']) ? $package['cs_membership_pkg_connects_rollover'] : '';
		    if ($membership_package_style == 'simple') {

			$pkg_feat_class = $package_feature == 'yes' ? ' active' : '';
			$cs_html .= '<li class="col-lg-' . $grid_columns . ' col-md-' . $grid_columns . ' col-sm-6 col-xs-12 " >';
			$cs_html .= '<div style="background-color:#fff;" class="pricetable-holder ' . CS_FUNCTIONS()->cs_special_chars($pkg_feat_class) . '">';
			$cs_html .= '<h2 class="cs-bgcolor" style="color:#fff; padding:55px 20px;">' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_title) . '</h2>';
			$cs_html .= '<div class="price-holder">';
			$cs_html .= '<div class="cs-price">';
			$cs_html .= ' <p>' . CS_FUNCTIONS()->cs_special_chars($membership_pkg_desc) . '</p>';
			$cs_html .= ' <span class="cs-color"><em>' . esc_attr($currency_sign) . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_price) . '</em><small>' . '<em>' . sprintf(esc_html__('%s connects / %s %s', 'jobhunt'), $memberhsip_pkg_connects, $membership_pkg_dur, $membership_pkg_dur_period) . '</em></span>';
			$cs_html .= ' </div>';
			if (!is_user_logged_in()) {
			    $cs_html .= '<a class="cs-button" style="background:#f5f5f5; color:#999" onclick="trigger_func(\'#btn-header-main-login\');">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
			    $cs_html .= '<a class="cs-button" style="background:#f5f5f5; color:#999" id="1cs_emp_check_' . absint($cs_rand_id) . '">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else {
			    $cs_opt_submit_array = array(
				'id' => '',
				'std' => esc_html__('Buy Now', 'jobhunt'),
				'cust_id' => "",
				'cust_name' => "",
				'cust_type' => 'submit',
				'classes' => 'cs-bgcolor slct-membership-pkg cs-buy',
				'extra_atr' => '',
				'return' => true,
			    );
			    $cs_opt_radio_array = array(
				'id' => '',
				'std' => absint($membership_pkg_id),
				'cust_id' => "",
				'cust_name' => "cs_package",
				'return' => true,
				'extra_atr' => 'style="display:none; position:absolute;" ',
			    );
			    $cs_opt_hidden_array = array(
				'id' => '',
				'std' => '1',
				'cust_id' => "",
				'cust_name' => "cs_membership_pkg_trans",
				'return' => true,
			    );
			    $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_candidate_dashboard)) . '">
                                ' . $cs_form_fields2->cs_form_text_render($cs_opt_submit_array) . $cs_form_fields2->cs_form_hidden_render($cs_opt_radio_array) . $cs_form_fields2->cs_form_hidden_render($cs_opt_hidden_array) . '
                                   </form>';
			}
			$cs_html .= '</div>';
			$cs_html .= '</div>';
			$cs_html .= ' </li>';
		    } else if ($membership_package_style == 'classic' || $membership_package_style == 'advance') {

			$pkg_feat_class = $package_feature == 'yes' ? ' active' : '';
			$pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
			$main_class = 'modren';
			if ($membership_package_style == 'advance') {
			    $main_class = 'advance';
			}
			$cs_html .= '<div class="col-lg-' . $grid_columns . ' col-md-' . $grid_columns . ' col-sm-6 col-xs-12">
                                        <div class="pricetable-holder ' . $main_class . ' ' . $pkg_feat_class . '">
                                                <h2' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_title) . '</h2> 
                                                <div class="price-holder ">
                                                        <div class="cs-price">
                                                                <span>' . jobcareer_get_currency($memberhsip_pkg_price, true, '<small>', '</small>') . '</span>';
			if ($membership_pkg_desc != '') {
			    $membership_pkg_desc = nl2br($membership_pkg_desc);
			    $membership_pkg_desc = str_replace('<br />', '</li><li>', $membership_pkg_desc);
			    $cs_html .= '<ul>
                                                <li>' . $membership_pkg_desc . '</li>
                                        </ul>';
			}
			$cs_html .= '</div>';
			if (!is_user_logged_in()) {
			    $cs_html .= '<a class="cs-bgcolor" onclick="trigger_func(\'#btn-header-main-login\');">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') )) {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-bgcolor">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && $cs_user_apply_job != 'on') {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-bgcolor">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else {
			    $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_candidate_dashboard)) . '">
                                         <input class="cs-bgcolor" type="submit" value="' . esc_html__('Buy Now', 'jobhunt') . '">
                                         <input type="hidden" name="cs_package" value="' . absint($membership_pkg_id) . '" style="display:none; position:absolute;" />
                                         <input type="hidden" name="cs_job_pkg_trans" value="1">
					 <input type="hidden" name="cs_membership_pkg_false" value="false">
                                         </form>';
			}
			$cs_html .= '</div></div></div>';
		    } else if ($membership_package_style == 'fancy') {
			$pkg_feat_class = $package_feature == 'yes' ? ' active' : '';
			$pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
			$pkg_feat_bgcolor = $package_feature == 'yes' ? '' : ' class="cs-bgcolor"';
			$cs_html .= '<div class="col-lg-' . $grid_columns . ' col-md-' . $grid_columns . ' col-sm-6 col-xs-12">
							<div class="pricetable-holder classic cs-border-top-color' . $pkg_feat_class . '">
								<h2' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_title) . '</h2> 
								<div class="price-holder">
									<div class="cs-price">
										<span><small>' . esc_attr($currency_sign) . '</small>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_price) . '<em>' . sprintf(esc_html__('%s jobs / %s %s', 'jobhunt'), $memberhsip_pkg_connects, $membership_pkg_dur, $membership_pkg_dur_period) . '</em></span>';
			if ($membership_pkg_desc != '') {
			    $membership_pkg_desc = nl2br($membership_pkg_desc);
			    $membership_pkg_desc = str_replace('<br />', '</li><li>', $membership_pkg_desc);
			    $cs_html .= '<ul><li>' . $membership_pkg_desc . '</li></ul>';
			}
			$cs_html .= '</div>';
			if (!is_user_logged_in()) {
			    $cs_html .= '<a' . $pkg_feat_bgcolor . ' onclick="trigger_func(\'#btn-header-main-login\');">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') )) {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '"' . $pkg_feat_bgcolor . '>' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && $cs_user_apply_job != 'on') {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '"' . $pkg_feat_bgcolor . '>' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else {

			    $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_candidate_dashboard)) . '">

                                         <input' . $pkg_feat_bgcolor . ' type="submit" value="' . esc_html__('Buy Now', 'jobhunt') . '">
                                         <input type="hidden" name="cs_package" value="' . absint($membership_pkg_id) . '" style="display:none; position:absolute;" />
                                         <input type="hidden" name="cs_membership_pkg_trans" value="1">
					 <input type="hidden" name="cs_membership_pkg_false" value="false">
					 
                                         </form>';
			}
			$cs_html .= '</div></div></div>';
		    } else if ($membership_package_style == 'modern') {

			$pkg_feat_class = $package_feature == 'yes' ? ' active cs-border-top-color' : '';
			$pkg_feat_color = $package_feature == 'yes' ? ' class="cs-color"' : '';
			$cs_html .= '<li class="col-lg-' . $grid_columns . ' col-md-' . $grid_columns . ' col-sm-6 col-xs-12">
							<div class="pricetable-holder ' . $pkg_feat_class . '">
								<div class="price-holder">
									<div class="cs-price">
										<strong ' . $pkg_feat_color . '>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_title) . '</strong> 
										<span><small>' . esc_attr($currency_sign) . '</small>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_price) . '</span>';

			$cs_html .= '</div>';

			$cs_html .= "<p>";
			$cs_html .= sprintf(esc_html__('%s connects / %s %s', 'jobhunt'), $memberhsip_pkg_connects, $membership_pkg_dur, $membership_pkg_dur_period);
			$cs_html .= "</p>";



			if ($membership_pkg_desc != '') {
			    $membership_pkg_desc = nl2br($membership_pkg_desc);
			    $membership_pkg_desc = str_replace('<br />', '</li><li>', $membership_pkg_desc);
			    $cs_html .= '<ul><li>' . $membership_pkg_desc . '</li></ul>';
			}

			if (!is_user_logged_in()) {
			    $cs_html .= '<a onclick="trigger_func(\'#btn-header-main-login\');">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && isset($user_role) && $user_role == 'cs_candidate') {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '">' . esc_html__('Buy Now', 'jobhunt') . '<i class="icon-arrows-play"></i>' . '</a>';
			} else if (is_user_logged_in() && $cs_user_apply_job != 'on') {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '">' . esc_html__('Buy Now', 'jobhunt') . '<i class="icon-arrows-play"></i>' . '</a>';
			} else {

			    $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_candidate_dashboard)) . '">
                                            <input type="submit" value="' . esc_html__('Buy Now', 'jobhunt') . '">
                                            <input type="hidden" name="cs_package" value="' . absint($membership_pkg_id) . '" style="display:none; position:absolute;" />
                                            <input type="hidden" name="cs_membership_pkg_trans" value="1">
					    <input type="hidden" name="cs_membership_pkg_false" value="false">
                                            </form>';
			}
			$cs_html .= '</div></div></li>';
		    } else {
			$cs_html .= '<article class="col-md-' . $grid_columns . '">
                                        <span class="cs-bgcolor price"><sup>' . esc_attr($currency_sign) . '</sup>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_price) . '<em>' . sprintf(esc_html__('%s connects / %s %s', 'jobhunt'), $memberhsip_pkg_connects, $membership_pkg_dur, $membership_pkg_dur_period) . '</em></span>
                                        <h3>' . CS_FUNCTIONS()->cs_special_chars($memberhsip_pkg_title) . '</h3>
                                        <ul class="price-list">';
			if ($membership_package_style == 'subscription') {
			    $cs_html .= '<li><span><b>' . CS_FUNCTIONS()->cs_special_chars($package_listings) . '</b> ' . sprintf(esc_html__('%s connects / %s %s', 'jobhunt'), $memberhsip_pkg_connects, $membership_pkg_dur, $membership_pkg_dur_period) . '</span></li>';
			} else {
			    $cs_html .= '<li><span>' . esc_html__('Single Submission', 'jobhunt') . '</span></li>';
			}
			$cs_html .= '<li><span><b>' . CS_FUNCTIONS()->cs_special_chars($membership_pkg_dur) . '</b> ' . sprintf(esc_html__('%s duration', 'jobhunt'), $membership_pkg_dur_period) . '</span></li>
							<li>' . CS_FUNCTIONS()->cs_special_chars($membership_pkg_desc) . '</li>
							</ul>';
			if (!is_user_logged_in()) {
			    $cs_html .= '<a class="cs-color acc-submit" onclick="trigger_func(\'#btn-header-main-login\');">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && !((isset($user_role) && $user_role <> '' && $user_role == 'cs_employer') )) {
			    $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-color acc-submit">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			} else if (is_user_logged_in() && $cs_user_apply_job != 'on') {
			     $cs_html .= '<a id="cs_emp_check_' . absint($cs_rand_id) . '" class="cs-color acc-submit">' . esc_html__('Buy Now', 'jobhunt') . '</a>';
			}
			else {

			    $cs_html .= '<form method="post" action="' . add_query_arg(array('profile_tab' => 'packages'), get_permalink($cs_candidate_dashboard)) . '">
							<input class="slct-cv-pkg cs-color acc-submit" type="submit" value="' . esc_html__('Buy Now', 'jobhunt') . '">
							<input type="hidden" name="cs_package" value="' . absint($membership_pkg_id) . '" style="display:none; position:absolute;" />
							<input type="hidden" name="cs_job_pkg_trans" value="1">
							<input type="hidden" name="cs_membership_pkg_false" value="false">
							</form>';
			}
			$cs_html .= '</article>';
		    }
		}
		$cs_membership_pkg_counter ++;
	    }
	    if ($membership_package_style == 'fancy') {
		$cs_html .= '</ul>';
	    }
	    if ($membership_package_style == 'modern') {
		$cs_html .= '</ul>';
	    }
	}
	if ($membership_package_style == 'classic' || $membership_package_style == 'fancy' || $membership_package_style == 'modern' || $membership_package_style == 'advance') {
	    $cs_html .= '</div>';
	}
	if ($membership_package_style == 'basic') {
	    $cs_html .= '</div>';
	}
	$cs_html .= '</div>';
	if ($column_class != '') {
	    $cs_html .= '</div>';
	}
	return do_shortcode($cs_html);
    }

    add_shortcode('cs_membership_package', 'cs_memberhsip_package_shortcode');
}