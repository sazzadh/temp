<?php
/**
 * The template for Job Detail 
 */
// set view in db if user comes first time cookie
global $post, $current_user, $cs_form_fields2, $cs_plugin_options;

$cs_job_posted_date_formate = 'd-m-Y H:i:s';
$cs_job_expired_date_formate = 'd-m-Y H:i:s';

$current_post_id = get_the_ID();
?>
<div id="main">
    <?php
    /*
     *  login user detail
     *      
     */
    $login_user_name = '';
    $login_user_email = '';
    $login_user_phone = '';
    $cs_emp_funs = new cs_employer_functions();
    if (is_user_logged_in()) {
	$user_info = get_userdata($current_user->ID);
	if (isset($user_info->display_name))
	    $login_user_name = $user_info->display_name;
	if (isset($user_info->user_email))
	    $login_user_email = $user_info->user_email;

	$login_user_phone = get_user_meta($user_info->ID, 'cs_phone_number', true);
    }
    $cs_job_status = get_post_meta($post->ID, 'cs_job_status', true);
    $cs_job_emplyr = get_post_meta($post->ID, 'cs_job_username', true);
    $cs_post_view = true;
    if ($cs_job_status != 'active') {
	$cs_post_view = false;
	if (is_user_logged_in() && $cs_job_emplyr == $current_user->ID) {
	    $cs_post_view = true;
	    $cs_owner_view = true;
	}
	if (is_user_logged_in() && current_user_can('administrator')) {
	    $cs_post_view = true;
	    $cs_owner_view = true;
	}
    }
    if ($cs_post_view == true) {
	if (have_posts()):
	    while (have_posts()) : the_post();
		$job_post = $post;
		// get all job types
		$all_specialisms = get_the_terms($job_post->ID, 'specialisms');
		$specialisms_values = '';
		$specialism_flag = 1;
		if ($all_specialisms != '') {
		    foreach ($all_specialisms as $specialismsitem) {
			$specialisms_values .= $specialismsitem->slug;
			if ($specialism_flag != count($all_specialisms)) {
			    $specialisms_values .= ", ";
			}
			$specialism_flag ++;
		    }
		}
		// get posted user
		$cs_job_username = get_post_meta(get_the_ID(), 'cs_job_username', true);
		// getting employer information
		$employer_post = get_userdata($cs_job_username);

		$job_address = get_user_address_string_for_detail($job_post->ID);
		?> 
	        <div class="main-section jobs-detail-2">
		    <?php
		    // getting from plugin options
		    $cs_title_f_size = isset($cs_plugin_options['cs_job_default_header_title_f_size']) ? $cs_plugin_options['cs_job_default_header_title_f_size'] : '';
		    $cs_title_color = isset($cs_plugin_options['cs_job_default_header_title_color']) ? $cs_plugin_options['cs_job_default_header_title_color'] : '';
		    $cs_title_style_str = '';
		    if (($cs_title_f_size != '' && $cs_title_f_size > 0) || $cs_title_color != '') {
			$cs_title_style_str .= ' style="';
			if ($cs_title_f_size != '' && $cs_title_f_size > 0) {
			    $cs_title_style_str .= ' font-size: ' . $cs_title_f_size . 'px !important;';
			}
			if ($cs_title_color != '') {
			    $cs_title_style_str .= ' color: ' . $cs_title_color . ' !important;';
			}
			$cs_title_style_str .= '"';
		    }
		    $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
		    ?>
	    	<div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
	    	    <div class="row">
	    		<div class="section-fullwidtht col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    		    <div class="row">
	    			<div class="section-content col-lg-8 col-md-8 col-sm-12 col-xs-12">
	    			    <div class="jobs-info">
                                         <?php echo cs_check_expire_job(get_the_ID(),'html');?>
					    <?php ob_start(); ?>
	    				<div class="cs-text">
						<?php
						$aspecialisms = get_the_terms($post->ID, 'specialisms');
						$all_job_type = get_the_terms($post->ID, 'job_type');

						$job_type_values = '';
						$job_type_class = '';
						$job_type_flag = 1;
						if ($all_job_type != '') {
						    foreach ($all_job_type as $job_type) {
							$t_id_main = $job_type->term_id;
							$job_type_color_arr = get_option("job_type_color_$t_id_main");
							$job_type_color = '';
							if (isset($job_type_color_arr['text'])) {
							    $job_type_color = $job_type_color_arr['text'];
							}

							$cs_link = ' href="javascript:void(0);"';
							if ($cs_search_result_page != '') {
							    $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
							}

							$job_type_values .= '<a ' . force_balance_tags($cs_link) . ' class="freelance" style="border-color:' . $job_type_color . '">' . $job_type->name . '</a>';

							if ($job_type_flag != count($all_specialisms)) {
							    $job_type_values .= " ";
							    $job_type_class .= " ";
							}
							$job_type_flag ++;
						    }
						}

						$job_address = apply_filters('jobhunt_job_address_frontend', $job_address, $post->ID);

						if ($job_type_values <> '') {
						    ?>
						    <?php echo force_balance_tags($job_type_values); ?>

						<?php } ?>
						<?php
						$jobhunt_featured = apply_filters('jobhunt_featured_job', '', $job_post->ID);
						echo force_balance_tags($jobhunt_featured);
						?>
	    				    <ul class="post-options">
						    <?php if ($job_address <> '') { ?>
							<li><i class="icon-location6"></i><a href="#"><?php echo esc_html($job_address); ?></a></li>
							<?php
						    }
						    $cs_job_posted_date = get_post_meta($job_post->ID, 'cs_job_posted', true);
						    if (isset($cs_job_posted_date) && $cs_job_posted_date != '') {
							?>
							<li><i class="icon-calendar5"></i><?php esc_html_e("Post Date:", "jobhunt"); ?> <span><?php echo date_i18n(get_option('date_format'), (get_post_meta($job_post->ID, 'cs_job_posted', true))); ?></span></li>
						    <?php } ?>
						    <?php
						    // Application closing date frontend filter in application deadline add on
						    echo apply_filters('job_hunt_application_deadline_date_frontend', $current_post_id);
						    ?>
	    				    </ul>
	    				</div>
					    <?php
					    $job_info_output = ob_get_clean();
					    echo apply_filters('fancy_job_info', $job_info_output, $current_post_id);
					    ?>
	    			    </div>
	    			    <div class="jobs-detail-listing">

	    				<ul class="row">
						<?php
						$cs_job_cus_fields = get_option("cs_job_cus_fields");
						if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {

						    $custom_field_box = 1;

						    foreach ($cs_job_cus_fields as $cus_field) {
							if ($cus_field['meta_key'] != '') {
							    $data = get_post_meta($job_post->ID, $cus_field['meta_key'], true);
							    // empty check of value
							    if ($cus_field['label'] != '')
								if ($data != "") {
								    ?> <li class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><div class="listing-inner">
									    <i class="<?php echo sanitize_html_class($cus_field['fontawsome_icon']) ?>"></i>
									    <div class="cs-text">
										<span>    <?php echo esc_html($cus_field['label']); ?></span>
										<strong>	<?php
										    if (is_array($data)) {
											$data_flage = 1;
											$comma = '';
											foreach ($data as $datavalue) {
											    if ($cus_field['type'] == 'dropdown') {
												$options = $cus_field['options']['value'];
												if (isset($options)) {
												    $finded_array = array_search($datavalue, $options);
												    $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
												}
												echo $comma . esc_html($datavalue);
												$comma = ', ';
											    } else {
												echo esc_html($datavalue);
											    }
											    if ($data_flage != count($data)) {
												echo "";
											    }
											    $data_flage ++;
											}
										    } else {
											if ($cus_field['type'] == 'dropdown') {
											    $options = $cus_field['options']['value'];
											    if (isset($options)) {
												$finded_array = array_search($data, $options);
												$data = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
											    }
											    echo esc_html($data);
											} else {
											    echo esc_html($data);
											}
										    }
										    ?></strong></div></div></li><?php
								    if (($custom_field_box % 3 == 0 && $custom_field_box > 0) && count($cs_job_cus_fields) != $custom_field_box)
									$custom_field_box ++;
								}
							}
						    }
						    if ($custom_field_box % 3 != 0 && $custom_field_box > 0)
							echo "";
						}
						?>
	    				</ul>

	    				</ul>
	    			    </div>
	    			    <div class="rich-editor-text">
	    				<h6><?php esc_html_e('Job Description', 'jobhunt'); ?></h6>
					    <?php
					    the_content();
					    wp_link_pages(array(
						'before' => '<div class="page-content-links"><strong class="page-links-title">' . esc_html__('Pages:', 'jobcareer') . '</strong>',
						'after' => '</div>',
						'link_before' => '<span>',
						'link_after' => '</span>',
						'pagelink' => '%',
						'separator' => '',
					    ));
					    $empty_string = '';
					    echo apply_filters('view_more', $empty_string, $current_post_id);
					    $list_job_id = rand(0, 4656465);
					    ?>
					    <?php do_action('jobhunt_job_attachment_frontend_ui', $current_post_id); ?>
					    <?php ob_start(); ?>
					    <?php
					    $user_can_apply = 'on';
					    $user_can_apply = apply_filters('jobhunt_candidate_can_apply', $user_can_apply);

					    $display_apply_buttons = 'yes';
					    $display_apply_buttons = apply_filters('job_detail_apply_now_buttons', $display_apply_buttons);


					    $display_shortlist_button = 'yes';
					    $display_shortlist_button = apply_filters('jobhunt_candidate_lists_shortlist_button', $display_shortlist_button);
					    ?>

					    <?php if ($user_can_apply == 'on') { ?>
						<?php if ($display_apply_buttons == 'yes' || $display_apply_buttons == '') { ?>
		    				<div class="apply-buttons">
							<?php
							$user = cs_get_user_id();
							if ($display_shortlist_button == 'yes') {
							    if (!is_user_logged_in() || !$cs_emp_funs->is_employer()) {
								?>
								<?php cs_add_dirpost_favourite($job_post->ID); ?>
							    <?php } else {
								?>
			    				    <a class="cs-add-wishlist btn small" href="javascript:void(0)"  id="<?php echo 'addjobs_to_wishlist' . intval($list_job_id); ?>" 
			    				       onclick="show_alert_msg('<?php echo esc_html__("Become a candidate then try again.", "jobhunt"); ?>')" >
			    					<i class="icon-heart-o"></i></a>
								<?php
							    }
							}
							$class_apply = '';
							if (isset($_SESSION['apply_job_id'])) {
							    $class_apply = 'applyauto';
							    unset($_SESSION['apply_job_id']);
							}

							ob_start();
							if (is_user_logged_in()) {
							    $user = cs_get_user_id();
							    $user_role = cs_get_loginuser_role();
							    if (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
								$cs_applied_list = array();
								if (isset($user) and $user <> '' and is_user_logged_in()) {
								    $finded_result_list = cs_find_index_user_meta_list($job_post->ID, 'cs-user-jobs-applied-list', 'post_id', cs_get_user_id());
								    if (is_array($finded_result_list) && !empty($finded_result_list)) {
									?>
				    				    <a href="javascript:void(0);" class="btn large like applied_icon" >
				    					<i class="icon-briefcase4"></i><?php esc_html_e('Applied', 'jobhunt') ?>
				    				    </a>
									<?php
								    } else {
									do_action('jobhunt_apply_job_btn_fancy_view', $job_post->ID, $class_apply);
								    }
								} else {
								    ?>
								    <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post->ID); ?>', this)" > 
									<i class="icon-briefcase4"></i><?php esc_html_e('Apply for this job', 'jobhunt') ?>
								    </a>	
								    <?php
								}
							    }
							} else {
							    
                                                            //do_action('jobhunt_applyjob_without_login',$job_post->ID);
							    ?>
							    <a href="javascript:void(0);" class="btn large like applied_icon" onclick="trigger_func('#btn-header-main-login',<?php echo $job_post->ID; ?>);"> 
								<i class="icon-briefcase4"></i><?php esc_html_e('Apply Now', 'jobhunt') ?></a>

							    <?php
							}

							$job_apply_btn_output = ob_get_clean();
							echo apply_filters('jobhunt_vaavio_apply_btn', $job_apply_btn_output, $job_post->ID, esc_html__('Apply Now', 'jobhunt'), esc_html__('Apply for this job', 'jobhunt'), 'btn large like applied_icon', '<i class="icon-briefcase4"></i>');

							if (!is_user_logged_in()) {
							    ?>

							    <a class="btn large linkedin social_login_login_linkedin" href="#" data-applyjobid="<?php echo intval($job_post->ID); ?>">
								<div data-applyjobid="<?php echo intval($job_post->ID); ?>" class="linkedin_jobid_apply"></div><i class="icon-linkedin4"></i><?php esc_html_e('Apply with Linkedin', 'jobhunt'); ?>
							    </a>           
							    <?php do_action('apply_with_facebook_button', $job_post->ID); ?>
							<?php }
							?>

		    				</div>
						    <?php
						    $apply_buttons_output = ob_get_clean();
						    echo apply_filters('apply_buttons', $apply_buttons_output, $current_post_id);
						} else {
						    ?>
		    				<div class="apply-buttons">
							<?php
							$user = cs_get_user_id();
							if ($display_shortlist_button == 'yes') {
							    if (!is_user_logged_in() || !$cs_emp_funs->is_employer()) {
								?>
								<?php cs_add_dirpost_favourite($job_post->ID); ?>
							    <?php } else {
								?>
			    				    <a class="cs-add-wishlist btn small" href="javascript:void(0)"  id="<?php echo 'addjobs_to_wishlist' . intval($list_job_id); ?>" 
			    				       onclick="show_alert_msg('<?php echo esc_html__("Become a candidate then try again.", "jobhunt"); ?>')" >
			    					<i class="icon-heart-o"></i></a>
							    <?php } ?>
							<?php } ?>
		    				</div>
						    <?php
						}
						do_action('jobhunt_asifbadat_apply_buttons', $post->ID, 'fancy');
					    }
					    ?>
					    <?php ob_start(); ?>
					    <?php if (cs_social_share(0)) { ?>
						<div class="social-media">
						    <span><?php esc_html_e('Share', 'jobhunt'); ?></span>
						    <ul>
							<?php echo cs_social_share(0); ?>
						    </ul>
						</div>
					    <?php } ?>
					    <?php
					    $social_links = ob_get_clean();
					    echo apply_filters('social_links', $social_links, $current_post_id);
					    ?>
	    			    </div>

	    			</div>
	    			<div class="section-sidebar col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<?php
					ob_start();
					$flag = 1;
					if (isset($employer_post) && $employer_post != '') {
					    // getting employer data
					    $cs_employee_address = get_user_meta($employer_post->ID, 'cs_post_comp_address', true);
					    $cs_employee_web_http = $employer_post->user_url;
					    $cs_email = $employer_post->user_email;
					    $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
					    $cs_employee_facebook = get_user_meta($employer_post->ID, 'cs_facebook', true);
					    $cs_employee_twitter = get_user_meta($employer_post->ID, 'cs_twitter', true);
					    $cs_employee_linkedin = get_user_meta($employer_post->ID, 'cs_linkedin', true);
					    $cs_employee_google_plus = get_user_meta($employer_post->ID, 'cs_google_plus', true);
					    $cs_phone_number = get_user_meta($employer_post->ID, 'cs_phone_number', true);
					    $cs_employee_employer_img = get_user_meta($employer_post->ID, 'user_img', true);
					    $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
					    if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
						$cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found4x3.jpg');
					    }
					    $cs_employee_emp_username = $employer_post->display_name;

					    $display_email_address = 'yes';
					    $display_email_address = apply_filters('jobhunt_employer_email_address', $display_email_address);
					    ?>
					    <div class="company-info">
						<?php if ($cs_employee_employer_img <> '') { ?>
		    				<div class="cs-media">
		    				    <figure><a href="<?php echo esc_url(get_author_posts_url($employer_post->ID)); ?>">
		    					    <img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="<?php echo esc_html($employer_post->post_title); ?>" /></a>
		    				    </figure>
		    				</div><?php } ?>
						<div class="cs-text">
						    <strong> <a href="<?php echo esc_url(get_permalink($employer_post->ID)); ?>"><?php echo esc_html($employer_post->post_title); ?></h4> </a></strong>
						    <?php if ($cs_employee_address != '') echo '<span> ' . $cs_employee_address . '</span>'; ?>

						    <ul class="admin-contect">
							<?php if ($cs_phone_number <> '') { ?>
		    					<li>
		    					    <i class="icon-phone8"></i>
		    					    <p>
								    <?php echo esc_html($cs_phone_number); ?> 
		    					    </p>
		    					</li>
							<?php } if ($cs_email <> '' && ( $display_email_address == 'yes' || $display_email_address == '' )) { ?>
		    					<li>
		    					    <i class="icon-mail6"></i>
		    					    <p>
		    						<small><?php esc_html_e('Email:', 'jobhunt'); ?></small>
		    						<a href="mailto:<?php echo sanitize_email($cs_email); ?>"> <?php echo ($cs_email); ?></a>
		    					    </p>
		    					</li>
							    <?php
							}
							if ($cs_employee_web != '') {
							    ?>
		    					<li>
		    					    <i class="icon-link4"></i>
		    					    <p>
		    						<a href="<?php echo esc_url($cs_employee_web_http); ?>"><?php echo esc_html($cs_employee_web); ?></a>
		    					    </p>
		    					</li>
							<?php }
							?>
						    </ul>

						    <?php
						    $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
						    $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
						    cs_google_recaptcha_scripts();
						    ?>
						    <script type="text/javascript">
		                                        var recaptcha8;
		                                        var cs_multicap = function () {

		                                            recaptcha8 = grecaptcha.render('recaptcha8', {
		                                                'sitekey': '<?php echo ($cs_sitekey); ?>', //Replace this with your Site key
		                                                'theme': 'light'
		                                            });
		                                        };
						    </script>
						    <div class="btn-area">
							<a href="<?php echo esc_url(get_author_posts_url($employer_post->ID)); ?>"><?php echo esc_html__('View all Jobs', 'jobhunt'); ?></a>
							<?php
							$display_employer_contact_form = 'yes';
							$display_employer_contact_form = apply_filters('jobhunt_employer_contact_form_display', $display_employer_contact_form);

							$display_employer_contact_from_job_detail = 'yes';
							$display_employer_contact_from_job_detail = apply_filters('jobhunt_employer_contact_from_job_detail', $display_employer_contact_from_job_detail, $current_post_id);

							if ($display_employer_contact_form == 'yes' && $display_employer_contact_from_job_detail == 'yes') {
							    $employer_contact_link_target = '#cs-msgbox-' . absint($job_post->ID);
							    $employer_contact_link_target = apply_filters('jobhunt_employer_contact_form_link', $employer_contact_link_target);
							    ?>
		    					<a data-target="<?php echo $employer_contact_link_target; ?>" data-toggle="modal" href="#"><?php esc_html_e('Contact US', 'jobhunt'); ?></a>
							<?php } ?>
						    </div>

						    <div class="modal fade" id="cs-msgbox-<?php echo absint($job_post->ID) ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							<div class="modal-dialog" role="document">
							    <div class="modal-content">
								<div class="modal-body">
								    <div class="cs-profile-contact-detail cs-contact-modal" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-cap="recaptcha7">
									<a class="close" data-dismiss="modal">&times;</a>
									<form id="ajaxcontactemployer" action="#" method="post" enctype="multipart/form-data">
									    <div id="ajaxcontact-response" class=""></div>

									    <div class="input-filed">
										<i class="icon-user9"></i>
										<?php
										$cs_opt_array = array(
										    'id' => '',
										    'std' => isset($login_user_name) ? esc_html($login_user_name) : '',
										    'cust_id' => "ajaxcontactname",
										    'cust_name' => "ajaxcontactname",
										    'classes' => 'form-control',
										    'extra_atr' => 'placeholder="' . esc_html__('Enter your Name', 'jobhunt') . '*"',
										    'required' => 'yes',
										);
										$cs_form_fields2->cs_form_text_render($cs_opt_array);
										?>

									    </div>
									    <div class="input-filed"> <i class="icon-envelope4"></i>
										<?php
										$cs_opt_array = array(
										    'id' => '',
										    'std' => isset($login_user_email) ? esc_html($login_user_email) : '',
										    'cust_id' => "ajaxcontactemail",
										    'cust_name' => "ajaxcontactemail",
										    'classes' => 'form-control',
										    'extra_atr' => 'placeholder="' . esc_html__('Email Address', 'jobhunt') . '*"',
										    'required' => 'yes',
										);
										$cs_form_fields2->cs_form_text_render($cs_opt_array);
										?>
									    </div>
									    <div class="input-filed"> <i class="icon-mobile4"></i>
										<?php
										$cs_opt_array = array(
										    'id' => '',
										    'std' => isset($login_user_phone) ? esc_html($login_user_phone) : '',
										    'cust_id' => "ajaxcontactphone",
										    'cust_name' => "ajaxcontactphone",
										    'classes' => 'form-control',
										    'extra_atr' => 'placeholder="' . esc_html__('Phone Number', 'jobhunt') . '"',
										);
										$cs_form_fields2->cs_form_text_render($cs_opt_array);
										?>
									    </div>
									    <div class="input-filed">
										<?php
										$cs_opt_array = array(
										    'id' => '',
										    'std' => '',
										    'cust_id' => "ajaxcontactcontents",
										    'cust_name' => "ajaxcontactcontents",
										    'classes' => 'form-control',
										    'extra_atr' => 'placeholder="' . esc_html__('Message should have more than 50 characters', 'jobhunt') . '"',
										);
										$cs_form_fields2->cs_form_textarea_render($cs_opt_array);
										?>
									    </div>

									    <?php
									    global $cs_plugin_options;
									    $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

									    if ($cs_captcha_switch == 'on') {
										echo '<div class="input-holder recaptcha-reload" id="recaptcha8_div">';
										echo cs_captcha('recaptcha8');
										echo '</div>';
									    }
									    ?>

									    <div class="submit-btn profile-contact-btn" data-employerid="<?php echo esc_html($employer_post->ID); ?>">
										<?php
										$cs_opt_array = array(
										    'id' => '',
										    'std' => esc_html__('Send Email', 'jobhunt'),
										    'cust_id' => "employerid_contactus",
										    'cust_name' => "employerid_contactus",
										    'cust_type' => 'button',
										    'classes' => 'cs-bgcolor',
										);
										$cs_form_fields2->cs_form_text_render($cs_opt_array);
										$cs_opt_array = array(
										    'std' => 'cs_registration_validation',
										    'cust_id' => 'action',
										    'cust_name' => 'action',
										    'cust_type' => 'hidden',
										    'return' => true,
										);
										$cs_form_fields2->cs_form_text_render($cs_opt_array);
										?>
										<div id="main-cs-loader" class="loader_class"></div>
									    </div>

									    <?php
									    $cs_terms_condition = isset($cs_plugin_options['cs_terms_condition']) ? $cs_plugin_options['cs_terms_condition'] : '';
									    if ($cs_terms_condition != '') {
										?>
		    							    <span class="cs-terms"><?php esc_html_e('You accepts our', 'jobhunt') ?><a target="_blank" href="<?php echo esc_url(get_permalink($cs_terms_condition)) ?>"> <?php esc_html_e('Terms and Conditions', 'jobhunt') ?></a></span> 
										<?php
									    }
									    ?>
									</form>
								    </div>
								</div>
							    </div>
							</div>
						    </div>

						</div>
					    </div>
					    <?php
					}
					$company_info_output = ob_get_clean();
					echo apply_filters('company_info', $company_info_output, $current_post_id);

					$cs_plugin_options = get_option('cs_plugin_options');
					$cs_safetysafe_switch = $cs_plugin_options['cs_safetysafe_switch'];
					if ($cs_safetysafe_switch != '' && $cs_safetysafe_switch == 'on') {
					    ?>
					    <div class="safety-save">
						<div class="warning-title ">
						    <h4 class="cs-color"><i class="icon-warning4"></i><?php esc_html_e('Safety Information', 'jobhunt') ?></h4>
						</div>
						<div class="cs-text">
						    <ul class="save-info">
							<?php
							$cs_safety_title_array = isset($cs_plugin_options['cs_safety_title_array']) ? $cs_plugin_options['cs_safety_title_array'] : '';
							$cs_safety_desc_array = isset($cs_plugin_options['cs_safety_desc_array']) ? $cs_plugin_options['cs_safety_desc_array'] : '';
							if (is_array($cs_safety_desc_array) && sizeof($cs_safety_desc_array) > 0) {
							    $cs_safety_count = 0;
							    foreach ($cs_safety_desc_array as $cs_safety_desc) {
								?>
								<li>
								    <h3><?php echo esc_html($cs_safety_title_array[$cs_safety_count]); ?></h3>
								    <p><?php echo esc_html($cs_safety_desc); ?></p>
								</li>
								<?php
								$cs_safety_count ++;
							    }
							} else {
							    ?>
		    					<li>
		    					    <p><?php esc_html_e('There is no record found', 'jobhunt') ?></p>
		    					</li>
							    <?php
							}
							?>
						    </ul>
						</div>
					    </div>
					    <?php
					}
					?>
	    			    <div class="widget widget-jobs">
					    <?php
					    ob_start();
					    $showcount = 5;
					    $filter_arr2[] = '';
					    // specilisim filter for all jobs
					    $specialisms = '';

					    if ($specialisms_values != '')
						$specialisms = explode(",", $specialisms_values);
					    if ($specialisms != '' && $specialisms != 'All specialisms') {
						$filter_multi_spec_arr = ['relation' => 'OR',];
						foreach ($specialisms as $specialisms_key) {
						    if ($specialisms_key != '') {
							$filter_multi_spec_arr[] = array(
							    'taxonomy' => 'specialisms',
							    'field' => 'slug',
							    'terms' => array($specialisms_key)
							);
						    }
						}
						$filter_arr2[] = array(
						    $filter_multi_spec_arr
						);
					    }
					    $featured_job_mypost = array('posts_per_page' => "10", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
						'post_status' => 'publish', 'ignore_sticky_posts' => 1,
						'post__not_in' => array($job_post->ID),
						'tax_query' => array(
						    'relation' => 'AND',
						    $filter_arr2
						),
						'meta_query' => array(
						    array(
							'key' => 'cs_job_posted',
							'value' => current_time('timestamp'),
							'compare' => '<=',
						    ),
						    array(
							'key' => 'cs_job_expired',
							'value' => current_time('timestamp'),
							'compare' => '>=',
						    ),
						    array(
							'key' => 'cs_job_status',
							'value' => 'active',
							'compare' => '=',
						    ),
						)
					    );

					    $title_limit = 3;

					    // Exclude expired jobs from listing
					    $featured_job_mypost = apply_filters('job_hunt_jobs_listing_parameters', $featured_job_mypost);

					    $custom_query = new WP_Query($featured_job_mypost);
					    if ($custom_query->have_posts() <> "") {
						?>
						<div class="widget-title"><h5><?php esc_html_e("Related Jobs", "jobhunt"); ?></h5></div>
						<ul>
						    <?php
						    global $cs_plugin_options;
						    $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
						    while ($custom_query->have_posts()) : $custom_query->the_post();
							$cs_post_id = get_the_ID();

							$cs_post_loc_address = get_user_address_string_for_list($post->ID);
							$cs_job_posted = get_post_meta($post->ID, "cs_job_posted", true);
							$cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); // 
							$user = get_user_by('id', $cs_job_employer);
							$cs_job_posted = cs_time_elapsed_string($cs_job_posted);
							?>
		    				    <li>
		    					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		    					<div class="post-option">
								<?php if ($cs_post_loc_address <> '') { ?> <span><?php echo esc_html($cs_post_loc_address); ?></span> <?php } ?>
								<?php if (is_object($user) && $user->display_name <> '') {
								    ?>   
								    <span>
									<?php
									if (isset($cs_job_posted) && $cs_job_posted != '') {
									    esc_html_e('Posted on', 'jobhunt');
									    ?>
									    <?php
									    echo esc_attr($cs_job_posted) . ', ' . esc_html__('by', 'jobhunt');
									}
									echo ' <u>' . esc_html($user->display_name) . '</u>';
									?>
								    </span><?php } ?>
		    					</div>
		    				    </li>
							<?php
						    endwhile;
						    ?>
						</ul>
					    <?php } ?>
					    <?php wp_reset_postdata(); ?>
					    <?php
					    $related_jobs_output = ob_get_clean();
					    echo apply_filters('sidebar_related_jobs', $related_jobs_output, $current_post_id);
					    ?>
	    			    </div>
	    			</div>
	    		    </div>
	    		</div>
	    	    </div>

	    	</div>
	        </div>
		<?php
	    endwhile;
	endif;
    } else {
	?>
        <div class="main-section">
    	<div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
    	    <div class="row">
    		<div class="col-md-12">
    		    <div class="unauthorized">
    			<h1>
				<?php _e('You are not <span>authorized</span>', 'jobhunt') ?>
    			</h1>
    		    </div>
    		</div>
    	    </div>
    	</div>
        </div>
	<?php
    }
    ?>

</div>