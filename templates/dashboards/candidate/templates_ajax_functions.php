<?php
/*
 * @candidate ajax profile page
 */

if ( ! function_exists('cs_ajax_candidate_profile') ) {

    /**
     * Start Function how to create and save candidate  metaboxes profile with the help of  Ajax
     */
    function cs_ajax_candidate_profile($uid = '') {
        global $post, $current_user, $cs_form_fields2, $cs_theme_fields, $cs_form_fields_frontend;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : $current_user->ID;
        if ( $uid <> '' ) {
            $cs_user_data = get_userdata($uid);
            $cs_description = $cs_user_data->description;
            $cs_first_name = $cs_user_data->first_name;
            $cs_last_name = $cs_user_data->last_name;
            $cs_display_name = $cs_user_data->display_name;
            $cs_job_title = get_user_meta($uid, 'cs_job_title', true);
            $cs_dob = get_user_meta($uid, 'cs_dob', true);
            $cs_user_status = get_user_meta($uid, 'cs_user_status', true);
            $cs_minimum_salary = get_user_meta($uid, 'cs_minimum_salary', true);
            $cs_allow_search = get_user_meta($uid, 'cs_allow_search', true);
            $cs_religion = get_user_meta($uid, 'cs_religion', true);
            $cs_id_num = get_user_meta($uid, 'cs_id_num', true);
            $cs_facebook = get_user_meta($uid, 'cs_facebook', true);
            $cs_twitter = get_user_meta($uid, 'cs_twitter', true);
            $cs_google_plus = get_user_meta($uid, 'cs_google_plus', true);
            $cs_linkedin = get_user_meta($uid, 'cs_linkedin', true);
            $cs_phone_number = get_user_meta($uid, 'cs_phone_number', true);
            $cs_video_url = get_user_meta($uid, 'cs_video_url', true);
            $cs_email = $cs_user_data->user_email;
            $cs_website = $cs_user_data->user_url;
            $cs_marital_status = get_user_meta($uid, 'cs_marital_status', true);
            $cs_value = get_user_meta($uid, 'user_img', true);
            $imagename_only = $cs_value;
            $cs_cover_candidate_img_value = get_user_meta($uid, 'cover_user_img', true);
            $cover_imagename_only = $cs_cover_candidate_img_value;
            $cs_jobhunt = new wp_jobhunt();

            $custom_addon_active = false;
            $custom_addon_active = apply_filters('jobhunt_custom_addon_depedency', $custom_addon_active);
            ?>
            <div class="cs-loader"></div>
            <?php if ( $cs_display_name != '' ) { ?>
                <h3 class="cs-candidate-title"><?php printf(esc_html__('Welcome %s', 'jobhunt'), esc_html($cs_display_name)) ?></h3>
            <?php } ?>

            <?php
            do_action('jobhunt_user_full_name_error_message', $uid);
            $cs_display_name = apply_filters('jobhunt_user_full_name_frontend', $cs_display_name, $uid);
            do_action('jobhunt_candidate_no_changes_btn', $uid);
            ?>
            <form id="cs_candidate" name="cs_candidate"  method="POST" enctype="multipart/form-data" >
                <div class="scetion-title">
                    <h4><?php esc_html_e('My Profile', 'jobhunt'); ?></h4>
                </div>
                <div class="dashboard-content-holder">
                    <section class="cs-profile-info">
                        <?php
                        $plugin_action = false;
                        $plugin_action = apply_filters('jobhunt_digitalmarketing_depedency', $plugin_action);
                        if ( ! $plugin_action ) {
                            ?>
                            <div class="cs-img-detail">
                                <div class="alert alert-dismissible user-img"> 
                                    <div class="page-wrap" id="cs_user_img_box">
                                        <figure>
                                            <?php
                                            if ( $cs_value <> '' ) {
                                                $cs_value = cs_get_image_url($cs_value, '');
                                                ?>
                                                <img src="<?php echo esc_url($cs_value); ?>" id="cs_user_img_img" width="100" alt="" />
                                                <div class="gal-edit-opts close"><a href="javascript:cs_del_media('cs_user_img')" class="delete">
                                                        <span aria-hidden="true">×</span></a>
                                                </div>
                                            <?php } else { ?>
                                                <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/upload-img.jpg" id="cs_user_img_img" width="100" alt="" />
                                                <?php
                                            }
                                            ?>
                                        </figure>
                                    </div>
                                </div>
                                <div class="upload-btn-div">
                                    <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                        <span class="cs-color"><?php esc_html_e('Browse', 'jobhunt'); ?></span>
                                        <?php
                                        $cs_opt_array = array(
                                            'std' => $imagename_only,
                                            'cust_id' => 'cs_user_img',
                                            'cust_name' => 'media_img',
                                            'cust_type' => 'hidden',
                                        );
                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                        <label class="browse-icon">
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => esc_html__('Browse', 'jobhunt'),
                                                'cust_id' => 'cs_media_upload',
                                                'cust_name' => 'media_upload',
                                                'cust_type' => 'file',
                                                'classes' => 'upload cs-uploadimgjobseek',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </label>				
                                    </div>
                                    <br />
                                    <span id="cs_candidate_profile_img_msg"><?php esc_html_e('Max file size is 1MB, Minimum dimension: 270x210 And Suitable files are .jpg & .png', 'jobhunt'); ?></span>
                                </div>
                            </div>
                            <?php if ( ! $custom_addon_active ) { ?>

                                <div class="cs-img-detail">
                                    <div class="alert alert-dismissible user-img"> 
                                        <div class="page-wrap" id="cs_cover_candidate_img_box">
                                            <figure>
                                                <?php
                                                if ( $cs_cover_candidate_img_value <> '' ) {

                                                    $cs_cover_candidate_img_value = cs_get_img_url($cs_cover_candidate_img_value, 'cs_media_4');
                                                    ?>
                                                    <img src="<?php echo esc_url($cs_cover_candidate_img_value); ?>" id="cs_cover_candidate_img_img" width="100" alt="" />
                                                    <div class="gal-edit-opts close">
                                                        <a href="javascript:cs_del_media('cs_cover_candidate_img')" class="delete">
                                                            <span aria-hidden="true">×</span>
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/upload-img.jpg" id="cs_cover_candidate_img_img" width="100" alt="" />
                                                    <?php
                                                }
                                                ?>
                                            </figure>
                                        </div>
                                    </div>
                                    <div class="upload-btn-div">
                                        <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                            <span class="cs-color"><?php esc_html_e('Browse Cover', 'jobhunt'); ?></span>
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => $cover_imagename_only,
                                                'id' => '',
                                                'return' => true,
                                                'cust_id' => 'cs_cover_candidate_img',
                                                'cust_name' => 'cs_cover_candidate_img',
                                                'prefix' => '',
                                            );
                                            echo force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                            $cs_opt_array = array(
                                                'std' => esc_html__('Browse Cover', 'jobhunt'),
                                                'id' => '',
                                                'return' => true,
                                                'force_std' => true,
                                                'cust_id' => '',
                                                'cust_name' => 'cand_cover_media_upload',
                                                'classes' => 'left cs-candi-cover-uploadimg upload',
                                                'cust_type' => 'file',
                                            );
                                            echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));
                                            ?>
                                        </div>
                                        <br /> 
                                        <span id="cs_candidate_profile_cover_msg"><?php esc_html_e('Max file size is 1MB, Minimum dimension: 1600x400 And Suitable files are .jpg & .png', 'jobhunt') ?></span>
                                    </div>
                                </div>

                            <?php } ?>
                        <?php } ?>
                        <div class="input-info">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label><?php esc_html_e('Full Name', 'jobhunt'); ?></label>
                                    <?php
                                    $cs_opt_array = array(
                                        'cust_id' => 'display_name',
                                        'cust_name' => 'display_name',
                                        'std' => $cs_display_name,
                                        'desc' => '',
                                        'extra_atr' => ' placeholder="' . esc_html__('Title', 'jobhunt') . '"',
                                        'required' => 'yes',
                                        'classes' => 'form-control',
                                        'hint_text' => '',
                                    );

                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </div>
                                <?php if ( ! $custom_addon_active ) { ?>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php esc_html_e('Job Title', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'job_title',
                                            'std' => $cs_job_title,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . esc_html__('Job Title', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php esc_html_e('Minimum Salary', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'minimum_salary',
                                            'std' => $cs_minimum_salary,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . esc_html__('Minimum Salary', 'jobhunt') . '"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>

                                    <?php
                                    global $cs_plugin_options;
                                    $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
                                    if ( isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch == 'on' ) {
                                        ?>

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label><?php esc_html_e('Allow in search & listing', 'jobhunt'); ?></label>
                                            <div class="select-holder">
                                                <?php
                                                $cs_opt_array = array(
                                                    'id' => 'allow_search',
                                                    'std' => $cs_allow_search,
                                                    'desc' => '',
                                                    'extra_atr' => 'data-placeholder="' . esc_html__("Please Select", "jobhunt") . '"',
                                                    'classes' => 'form-control chosen-default chosen-select-no-single',
                                                    'options' => array( '' => esc_html__('Please Select', 'jobhunt'), 'yes' => esc_html__('Yes', 'jobhunt'), 'no' => esc_html__('No', 'jobhunt') ),
                                                    'hint_text' => '',
                                                );

                                                $cs_form_fields2->cs_form_select_render($cs_opt_array);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    $specialisms_label = esc_html__('Specialisms', 'jobhunt');
                                    $specialisms_label = apply_filters('jobhunt_replace_specialisms_to_categories', $specialisms_label);
                                    ?>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php echo esc_html($specialisms_label); ?></label>
                                        <div>
                                            <?php echo cs_get_specialisms_dropdown('cs_specialisms', 'cs_specialisms', $uid, 'form-control chosen-select', true) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php do_action('jobhunt_candidate_profile_fields'); ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label><?php esc_html_e('Description', 'jobhunt'); ?></label>
                                    <?php
                                    $cs_description = (isset($cs_description)) ? ($cs_description) : '';
                                    echo $cs_form_fields2->cs_form_textarea_render(
                                            array( 'name' => esc_html__('Description', 'jobhunt'),
                                                'id' => 'candidate_content',
                                                'classes' => 'col-md-12',
                                                'cust_name' => 'candidate_content',
                                                'std' => $cs_description,
                                                'description' => '',
                                                'return' => true,
                                                'array' => true,
                                                'cs_editor' => true,
                                                'force_std' => true,
                                                'hint' => ''
                                            )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </section> 

                    <?php if ( ! $custom_addon_active ) { ?>
                        <section class="cs-social-network">
                            <div class="scetion-title">
                                <h4><?php esc_html_e('Social Network', 'jobhunt'); ?></h4>
                            </div>
                            <div class="input-info">
                                <div class="row">
                                    <div class="social-media-info">
                                        <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <?php
                                            $social_required_field = true;
                                            $social_required_field = apply_filters('jobhunt_profile_social_fields_required', $social_required_field);
                                            $social_required_field = ( $social_required_field == true ) ? ' required="required"' : '';

                                            $cs_opt_array = array(
                                                'id' => 'facebook',
                                                'std' => $cs_facebook,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Facebook', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );
                                            //$cs_opt_array['extra_atr'] .= $social_required_field;

                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <i class="icon-facebook2"></i> 
                                        </div>
                                        <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => 'twitter',
                                                'std' => $cs_twitter,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Twitter', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );
                                            //$cs_opt_array['extra_atr'] .= $social_required_field;

                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <i class="icon-twitter6"></i> 
                                        </div>
                                        <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => 'google_plus',
                                                'std' => $cs_google_plus,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Google Plus', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );
                                            //$cs_opt_array['extra_atr'] .= $social_required_field;
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <i class="icon-googleplus7"></i> 
                                        </div>
                                        <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => 'linkedin',
                                                'std' => $cs_linkedin,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Linkedin', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );
                                            //$cs_opt_array['extra_atr'] .= $social_required_field;
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <i class="icon-linkedin4"></i> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section class="cs-social-network">
                            <div class="scetion-title">
                                <h4><?php esc_html_e('Video URL', 'jobhunt'); ?></h4>
                            </div>
                            <div class="input-info">
                                <div class="row">
                                    <div class="social-media-info">
                                        <div class="social-input col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <?php
                                            $cs_opt_array = array(
                                                'id' => 'video_url',
                                                'std' => $cs_video_url,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Video URL', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );

                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>

                                        </div>



                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php } ?>

                    <?php do_action('jobhunt_candidate_alert_fields_section', $uid); ?>
                    <section class="cs-social-network">
                        <div class="scetion-title">
                            <h4><?php esc_html_e('Contact Information', 'jobhunt'); ?></h4>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="social-media-info">
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php esc_html_e('Phone Number', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'id' => 'phone_number',
                                            'std' => $cs_phone_number,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . esc_html__('Phone Number', 'jobhunt') . '" required="required"',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                            'return' => true,
                                        );

                                        $field = $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        $field = apply_filters('jobhunt_profile_phone_field', $field, $uid);
                                        echo $field;
                                        ?>
                                    </div>
                                    <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label><?php esc_html_e('Email', 'jobhunt'); ?></label>
                                        <?php
                                        $cs_opt_array = array(
                                            'cust_id' => 'user_email',
                                            'cust_name' => 'user_email',
                                            'std' => $cs_email,
                                            'desc' => '',
                                            'extra_atr' => ' placeholder="' . esc_html__('Email', 'jobhunt') . '" required="required"',
                                            'required' => 'yes',
                                            'classes' => 'form-control',
                                            'hint_text' => '',
                                        );

                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        ?>
                                    </div>
                                    <?php if ( ! $custom_addon_active ) { ?>
                                        <div class="social-input col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label><?php esc_html_e('Website', 'jobhunt'); ?></label>
                                            <?php
                                            $cs_opt_array = array(
                                                'cust_id' => 'user_url',
                                                'cust_name' => 'user_url',
                                                'std' => $cs_website,
                                                'desc' => '',
                                                'extra_atr' => ' placeholder="' . esc_html__('Website', 'jobhunt') . '"',
                                                'classes' => 'form-control',
                                                'hint_text' => '',
                                            );
                                            $required_field = true;
                                            $required_field = apply_filters('jobhunt_profile_website_field_required', $required_field);
                                            $required_field = ( $required_field == true ) ? ' required="required"' : '';
                                            $cs_opt_array['extra_atr'] .= $required_field;
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                    <?php } ?>

                                    <?php do_action('jobhunt_candidate_fields_frontend', $uid); ?>
                                    <?php do_action('jobhunt_frontend_location_fields', $uid, '', $current_user); ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="cs-extra-info">
                        <div class="scetion-title">
                            <h4><?php esc_html_e('Extra Information', 'jobhunt'); ?></h4>
                        </div>
                        <div class="input-info">
                            <div class="row">
                                <div class="social-media-info">
                                    <?php
                                    $cs_job_cus_fields = get_option("cs_candidate_cus_fields");
                                    if ( is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0 ) {
                                        echo cs_candidate_custom_fields_frontend($uid);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="cs-update-btn">
                        <?php
                        $cs_opt_array = array(
                            'std' => 'update_profile',
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'user_profile',
                            'cust_id' => 'user_profile',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'cs_user',
                            'cust_id' => 'cs_user',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        ?>
                        <a  href="javascript:void(0);" name="button_action" class="acc-submit cs-section-update cs-color csborder-color" onclick="javascript:ajax_profile_form_save('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>', 'cs_candidate')"><?php esc_html_e('Update', 'jobhunt'); ?></a>
                        <?php
                        $cs_opt_array = array(
                            'std' => 'ajax_form_save',
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'action',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                        $cs_opt_array = array(
                            'std' => $uid,
                            'id' => '',
                            'echo' => false,
                            'cust_name' => 'post_id',
                        );
                        $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        ?>
                    </section>  
                </div>
            </form>
            <?php
        } else {
            esc_html_e('Please create user profile.', 'jobhunt');
        }
        ?>
        <script type="text/javascript">
            /*
             * modern selection box function
             */

            if (jQuery('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length != '') {
                var config = {
                    '.chosen-select': {width: "100%"},
                    '.chosen-select-deselect': {allow_single_deselect: true},
                    '.chosen-select-no-single': {disable_search_threshold: 10, width: "100%"},
                    '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                    '.chosen-select-width': {width: "95%"}
                }
                for (var selector in config) {
                    jQuery(selector).chosen(config[selector]);
                }
            }


            jQuery(document).ready(function ($) {
                chosen_selectionbox();
            });
            /*
             * modern selection box function
             */
        </script>
        <?php
        die();
    }

    add_action('wp_ajax_cs_ajax_candidate_profile', 'cs_ajax_candidate_profile');
    add_action("wp_ajax_nopriv_cs_ajax_candidate_profile", "cs_ajax_candidate_profile");
}

if ( ! function_exists('cs_candidate_change_password') ) {

    function cs_candidate_change_password() {
        $html = '
        <div class="scetion-title">
            <h3>' . esc_html__('Change Password', 'jobhunt') . '</h3>
        </div>
        <div class="change-pass-content-holder">
            <div class="input-info">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . esc_html__('Old Password', 'jobhunt') . '</label>
                        <input type="password" name="old_password" class="form-control">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . esc_html__('New Password', 'jobhunt') . '</label>
                        <input type="password" name="new_password" class="form-control">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>' . esc_html__('Confirm Password', 'jobhunt') . '</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                    <div class="col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="button" value="' . esc_html__('Update', 'jobhunt') . '" id="candidate-change-pass-trigger" class="acc-submit cs-section-update cs-color csborder-color">   
                    </div>
                </div>
            </div>
        </div>';
        echo force_balance_tags($html);
        die;
    }

    add_action('wp_ajax_cs_candidate_change_password', 'cs_candidate_change_password');
    add_action("wp_ajax_nopriv_cs_candidate_change_password", "cs_candidate_change_password");
}

/**
 * End Function how to create and save candidate  metaboxes profile with the help of  Ajax
 *  * Start Function favorite jobs for jobseek in ajax base
 */
if ( ! function_exists('cs_ajax_candidate_favjobs') ) {

    function cs_ajax_candidate_favjobs($uid = '') {
        global $post, $cs_form_fields2, $cs_plugin_options;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        if ( $uid <> '' ) {
            ?>
            <section class="cs-favorite-jobs">
                <?php
                $user = cs_get_user_id();
                if ( isset($user) && $user <> '' ) {
                    $cs_shortlist_array = get_user_meta($user, 'cs-user-jobs-wishlist', true);
                    if ( ! empty($cs_shortlist_array) )
                        $cs_shortlist = array_column_by_two_dimensional($cs_shortlist_array, 'post_id');
                    else
                        $cs_shortlist = array();
                }
                ?>
                <div class="scetion-title">
                    <h3><?php esc_html_e('Shortlisted jobs', 'jobhunt'); ?></h3>
                </div>
                <ul class="top-heading-list">
                    <li><span><?php esc_html_e('Job Title', 'jobhunt'); ?></span></li>
                    <li><span><?php esc_html_e('Date Saved', 'jobhunt'); ?></span></li>
                </ul>
                <?php
                if ( ! empty($cs_shortlist) && count($cs_shortlist) > 0 ) {

                    $cs_blog_num_post = ( isset($cs_plugin_options['cs_job_dashboard_pagination']) && $cs_plugin_options['cs_job_dashboard_pagination'] != '' ) ? $cs_plugin_options['cs_job_dashboard_pagination'] : 10;
                    if ( empty($_REQUEST['page_id_all']) )
                        $_REQUEST['page_id_all'] = 1;

                    $args = array( 'posts_per_page' => $cs_blog_num_post, 'post_type' => 'jobs', 'paged' => $_REQUEST['page_id_all'], 'order' => 'DESC', 'orderby' => 'post_date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'post__in' => $cs_shortlist, );
                    $custom_query = new WP_Query($args);
                    $count_post = $custom_query->found_posts;
                    if ( $custom_query->have_posts() ):
                        ?>
                        <ul class="feature-jobs">
                            <?php
                            while ( $custom_query->have_posts() ): $custom_query->the_post();
                                $cs_jobs_thumb_url = '';
                                $employer_img = '';
                                // get employer images at run time
                                $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true);
                                $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                if ( $employer_img != '' ) {
                                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                                }
                                if ( ! cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "" ) {
                                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                }
                                ?>
                                <li class="holder-<?php echo intval($post->ID); ?>">
                                    <a class="hiring-img" href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                    <div class="company-detail-inner">
                                        <h6><a href="<?php echo esc_url(the_permalink()); ?>"><?php the_title(); ?></a></h6>
                                    </div>
                                    <div class="company-date-option">
                                        <span>
                                            <?php
                                            // getting added in wishlist date
                                            $finded = in_multiarray($post->ID, $cs_shortlist_array, 'post_id');
                                            if ( $finded != '' )
                                                if ( $cs_shortlist_array[$finded[0]]['date_time'] != '' ) {
                                                    echo date_i18n(get_option('date_format'), $cs_shortlist_array[$finded[0]]['date_time']);
                                                }
                                            ?>
                                        </span>
                                        <div class="control" >
                                            <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Remove", "jobhunt"); ?>" data-postid="<?php echo intval($post->ID); ?>" href="javascript:void(0);" onclick="javascript:cs_delete_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', this)"  class="close close-<?php echo intval($post->ID); ?>"><i class="icon-trash-o"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            endwhile;
                            ?>
                        </ul>
                        <?php
                        //==Pagination Start
                        if ( $count_post > $cs_blog_num_post && $cs_blog_num_post > 0 ) {
                            echo '<nav>';
                            echo cs_ajax_pagination($count_post, $cs_blog_num_post, 'shortlisted-jobs', 'candidate', $uid, '');
                            echo '</nav>';
                        }//==Pagination End 
                    endif;
                } else {
                    echo '<div class="cs-no-record">' . cs_info_messages_listing(esc_html__("There is no shortlist job.", 'jobhunt')) . '</div>';
                }
                ?>
            </section>  		
            <?php
        } else {
            echo '<div class="no-result"><h1>' . esc_html__('Please create user profile.', 'jobhunt') . '</h1></div>';
        }
        ?>
        <script>
            jQuery(document).ready(function () {
                jQuery('[data-toggle="tooltip"]').tooltip();
            });
        </script>
        <?php
        die();
    }

    add_action("wp_ajax_cs_ajax_candidate_favjobs", "cs_ajax_candidate_favjobs");
    add_action("wp_ajax_nopriv_cs_ajax_candidate_favjobs", "cs_ajax_candidate_favjobs");
}
/**
 * Start Function Candidate Membership Packages
 */
if ( ! function_exists('cs_ajax_candidate_membership_packages') ) {

    function cs_ajax_candidate_membership_packages() {
        global $cs_plugin_options, $current_user, $cs_form_fields2;

        $general_settings = new CS_PAYMENTS();
        $cs_cand_funs = new cs_candidate_fnctions();

        if ( isset($_POST['pkg_array']) ) {
            $post_array = stripslashes($_POST['pkg_array']);
            $post_array = json_decode($post_array, true);
            if ( is_array($post_array) && sizeof($post_array) > 0 ) {
                if ( isset($post_array['post_array']) ) {
                    $post_array = $post_array['post_array'];
                    $_POST = array_merge($_POST, $post_array);
                }
            }
        }

        $cs_vat_switch = isset($cs_plugin_options['cs_vat_switch']) ? $cs_plugin_options['cs_vat_switch'] : '';
        $cs_pay_vat = isset($cs_plugin_options['cs_payment_vat']) ? $cs_plugin_options['cs_payment_vat'] : '0';

        if ( isset($cs_plugin_options['cs_use_woocommerce_gateway']) && $cs_plugin_options['cs_use_woocommerce_gateway'] == 'on' ) {
            $cs_pay_vat = 0;
        }
        $currency_sign = jobcareer_get_currency_sign();
        $cs_memberhsip_packages_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';
        $cs_packages_ids = array();
        if ( is_array($cs_memberhsip_packages_options) && sizeof($cs_memberhsip_packages_options) > 0 ) {
            foreach ( $cs_memberhsip_packages_options as $cs_memberhsip_package ) {
                $cs_packages_ids[] = $cs_memberhsip_package['membership_pkg_id'];
            }
        }

        //print_r( $cs_packages_ids );
        // echo $cs_package_check   =   $cs_cand_funs->cs_is_membership_pkg_subscribed( $cs_packages_ids );
        // exit();
        if ( isset($_POST['cs_package']) && $_POST['cs_package'] != '' ) {

            $cs_package_check = $cs_cand_funs->cs_is_membership_pkg_subscribed($cs_packages_ids);
            $cs_html = '';
            if ( $cs_package_check['cs_trans_count'] == 0 ) {
                $cs_package = $_POST['cs_package'];
                $cs_package_data = $cs_memberhsip_packages_options[$cs_package];
                $cs_total_amount = 0;
                $cs_total_amount += CS_FUNCTIONS()->cs_num_format($cs_package_data['memberhsip_pkg_price']);
                $cs_smry_totl = $cs_total_amount;
                if ( $cs_vat_switch == 'on' && $cs_pay_vat > 0 ) {
                    $cs_vat_amount = $cs_total_amount * ( $cs_pay_vat / 100 );
                    $cs_total_amount = CS_FUNCTIONS()->cs_num_format($cs_vat_amount) + $cs_total_amount;
                }

                if ( $cs_total_amount > 0 ) {
                    global $gateways;
                    $cs_gateway_options = get_option('cs_plugin_options');
                    $cs_gw_counter = 1;
                    $cs_gatway_enable_flag = 0;
                    if ( isset($_POST['cs_membership_pkg_trans']) && $_POST['cs_membership_pkg_trans'] == 1 && $cs_total_amount > 0 ) {
                        $cs_trans_pkg = isset($_POST['cs_package']) ? $_POST['cs_package'] : '';
                        $cs_pkg_title = $cs_package_data['memberhsip_pkg_title'];
                        $cs_pkg_expiry_dur = $cs_package_data['membership_pkg_dur'];
                        $cs_pkg_dur_period = $cs_package_data['membership_pkg_dur_period'];
                        $cs_pkg_connects = $cs_package_data['memberhsip_pkg_connects'];
                        $cs_pkg_connects_rollover = $cs_package_data['cs_membership_pkg_connects_rollover'];
                        $cs_pkg_desc = $cs_package_data['membership_pkg_desc'];
                        $cs_pkg_expir_days = strtotime($cs_cand_funs->cs_date_conv($cs_pkg_expiry_dur, $cs_pkg_dur_period));
                        $trans_fields = array(
                            'cs_trans_id' => rand(149344111, 991435901),
                            'cs_trans_user' => $current_user->ID,
                            'cs_package_title' => $cs_pkg_title,
                            'cs_trans_package' => isset($_POST['cs_package']) ? $_POST['cs_package'] : '',
                            'cs_trans_amount' => $cs_total_amount,
                            'cs_trans_pkg_expiry' => $cs_pkg_expir_days,
                            'cs_trans_pkg_expiry_dur' => $cs_pkg_expiry_dur,
                            'cs_trans_pkg_expiry_dur_period' => $cs_pkg_dur_period,
                            'cs_trans_pkg_connects' => $cs_pkg_connects,
                            'cs_trans_pkg_connects_rollover' => $cs_pkg_connects_rollover,
                        );
                        if ( $_POST['cs_membership_pkg_false'] != 'false' ) {
                            $cs_trnas_html = $cs_cand_funs->cs_candidate_pay_process($trans_fields);
                        }
                    }
                    if ( isset($cs_trnas_html) && $cs_trnas_html != '' ) {
                        $cs_html .= $cs_trnas_html;
                    }
                    $cs_html .= '<form method="post" id="cs-membership-pkgs" data-ajaxurl="' . esc_url(admin_url('admin-ajax.php')) . '">';
                    $cs_html .= '   <div class="cs-order-summery">';
                    $cs_html .= '       <h4>' . esc_html__('Order summery', 'jobhunt') . '</h4>';
                    $cs_html .= '       <ul class="cs-sumry-clacs">';
                    $cs_html .= '           <li><span>' . esc_attr($cs_package_data['memberhsip_pkg_title']) . ' ' . esc_html__('Subscription', 'jobhunt') . '</span><em>' . jobcareer_get_currency($cs_package_data['memberhsip_pkg_price'], true) . '</em></li>';
                    if ( $cs_vat_switch == 'on' && isset($cs_vat_amount) ) {
                        $cs_html .= '           <li><span>' . sprintf(esc_html__('VAT (%s&#37;)', 'jobhunt'), $cs_pay_vat) . '</span><em>' . jobcareer_get_currency($cs_vat_amount, true) . '</em></li>';
                    }
                    $cs_html .= '           <li><span>' . esc_html__('Total', 'jobhunt') . '</span><em>' . jobcareer_get_currency($cs_total_amount, true) . '</em></li>';
                    $cs_html .= '       </ul>';
                    $cs_html .= '   </div>';
                    $cs_html .= '   <div class="contact-box cs-pay-box">';
                    $cs_html .= '       <ul class="select-card cs-all-gates">';
                    if ( isset($cs_gateway_options['cs_use_woocommerce_gateway']) && $cs_gateway_options['cs_use_woocommerce_gateway'] == 'on' ) {
                        $cs_opt_array = array(
                            'std' => 'cs_wooC_GATEWAY',
                            'id' => '',
                            'return' => true,
                            'cust_type' => 'hidden',
                            'extra_atr' => '',
                            'cust_name' => 'cs_payment_gateway',
                            'prefix' => '',
                        );
                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => $_POST['wooC_current_page'],
                            'id' => '',
                            'return' => true,
                            'cust_type' => 'hidden',
                            'extra_atr' => '',
                            'cust_name' => 'wooC_current_page',
                            'prefix' => '',
                        );
                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                        $cs_gatway_enable_flag ++;
                    } else {
                        if ( isset($gateways) && is_array($gateways) ) {
                            foreach ( $gateways as $key => $value ) {
                                $status = $cs_gateway_options[strtolower($key) . '_status'];
                                if ( isset($status) && $status == 'on' ) {
                                    $logo = '';
                                    if ( isset($cs_gateway_options[strtolower($key) . '_logo']) ) {
                                        $logo = $cs_gateway_options[strtolower($key) . '_logo'];
                                    }
                                    if ( isset($logo) && $logo != '' ) {
                                        $cs_checked = $cs_gw_counter == 1 ? ' checked="checked"' : '';
                                        $cs_active = $cs_gw_counter == 1 ? ' class="active"' : '';
                                        $cs_html .= '<li' . $cs_active . '><a><img alt="" src="' . esc_url($logo) . '"></a>';
                                        $cs_opt_array = array(
                                            'std' => $key,
                                            'id' => '',
                                            'cust_type' => 'radio',
                                            'extra_atr' => ' style="display:none; position:absolute;" ' . CS_FUNCTIONS()->cs_special_chars($cs_checked),
                                            'return' => true,
                                            'cust_name' => 'cs_payment_gateway',
                                        );
                                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                        $cs_html .= ' </li>';
                                        $cs_gatway_enable_flag ++;   // if any gatway enable then set flag
                                    }
                                    $cs_gw_counter ++;
                                }
                            }
                        }
                    }
                    $cs_html .= '       </ul>';
                    if ( $cs_gatway_enable_flag > 0 ) {
                        $cs_opt_array = array(
                            'std' => absint($cs_package),
                            'id' => '',
                            'cust_name' => 'cs_package',
                            'return' => true,
                        );
                        $cs_html .= $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => '1',
                            'id' => '',
                            'cust_name' => 'cs_membership_pkg_trans',
                            'return' => true,
                        );
                        $cs_html .= $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => esc_html__('Pay Now', 'jobhunt'),
                            'id' => '',
                            'cust_type' => 'submit',
                            'classes' => 'continue-btn',
                            'return' => true,
                        );
                        $cs_html .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
                    }
                    $cs_html .= '   </div>';
                    $cs_html .= '</form>';
                } else {
                    // Adding Free Package
                    $cs_trans_pkg = isset($_POST['cs_package']) ? $_POST['cs_package'] : '';
                    $cs_pkg_title = $cs_package_data['memberhsip_pkg_title'];
                    $cs_pkg_expiry_dur = $cs_package_data['membership_pkg_dur'];
                    $cs_pkg_dur_period = $cs_package_data['membership_pkg_dur_period'];
                    $cs_pkg_connects = $cs_package_data['memberhsip_pkg_connects'];
                    $cs_pkg_connects_rollover = $cs_package_data['cs_membership_pkg_connects_rollover'];
                    $cs_pkg_desc = $cs_package_data['membership_pkg_desc'];
                    $cs_pkg_expir_days = strtotime($cs_cand_funs->cs_date_conv($cs_pkg_expiry_dur, $cs_pkg_dur_period));

                    $trans_fields = array(
                        'cs_trans_id' => rand(149344111, 991435901),
                        'cs_trans_user' => $current_user->ID,
                        'cs_package_title' => $cs_pkg_title,
                        'cs_trans_package' => isset($_POST['cs_package']) ? $_POST['cs_package'] : '',
                        'cs_trans_amount' => 0,
                        'cs_trans_pkg_expiry' => $cs_pkg_expir_days,
                        'cs_trans_pkg_expiry_dur' => $cs_pkg_expiry_dur,
                        'cs_trans_pkg_expiry_dur_period' => $cs_pkg_dur_period,
                        'cs_trans_pkg_connects' => $cs_pkg_connects,
                        'cs_trans_pkg_connects_rollover' => $cs_pkg_connects_rollover,
                    );
                    $cs_html .= $cs_cand_funs->cs_candidate_pay_process($trans_fields);
                    $cs_html .= esc_html__('You have successfully subscribed free package.', 'jobhunt');
                }
            } else {
                echo '<div class="cs-record"> <i class="icon-warning2"> </i> ' . cs_info_messages_listing(esc_html__(" You already have credits to Apply job. You can't buy package until it is expired.", 'jobhunt')) . '</div>';
            }
        }
        do_action('jobhunt_subscribing_package');
        echo $cs_html;
        ?>
        <div class="cs-resumes">
            <div class="section-title">
                <h3><?php esc_html_e('Packages', 'jobhunt') ?></h3>
            </div>
        </div>
        <?php
        $cs_results = false;
        $cs_blog_num_post = ( isset($cs_plugin_options['cs_job_dashboard_pagination']) && $cs_plugin_options['cs_job_dashboard_pagination'] != '' ) ? $cs_plugin_options['cs_job_dashboard_pagination'] : 10;

        if ( empty($_REQUEST['page_id_all']) )
            $_REQUEST['page_id_all'] = 1;

        if ( is_array($cs_memberhsip_packages_options) && sizeof($cs_memberhsip_packages_options) > 0 ) {
            $args = array(
                'posts_per_page' => $cs_blog_num_post,
                'paged' => $_REQUEST['page_id_all'],
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
                        'key' => 'cs_transaction_package',
                        'value' => '',
                        'compare' => '!=',
                    ),
                ),
            );
            $custom_query = new WP_Query($args);
            $count_all_records = $custom_query->found_posts;
            if ( $count_all_records > 0 ) {
                ?>
                <div class="dashboard-content-holder">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td><?php esc_html_e('Transaction id', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Package', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Expiry', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Total jobs', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Used', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Remaining', 'jobhunt') ?></td>
                                    <td><?php esc_html_e('Status', 'jobhunt') ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cs_trans_num = 1;
                                while ( $custom_query->have_posts() ) : $custom_query->the_post();
                                    $cs_trans_id = get_post_meta(get_the_id(), "cs_transaction_id", true);
                                    $cs_trans_package = get_post_meta(get_the_id(), "cs_transaction_package", true);
                                    $cs_trans_connects_remaining = get_post_meta(get_the_id(), "cs_transaction_connects_remaining", true);
                                    $cs_trans_connects_used = get_post_meta(get_the_id(), "cs_transaction_connects_used", true);
                                    $cs_trans_connects = get_post_meta(get_the_id(), "cs_transaction_connects", true);
                                    $cs_trans_status = get_post_meta(get_the_id(), "cs_transaction_status", true);
                                    $cs_trans_status = $cs_trans_status != '' ? $cs_trans_status : 'pending';
                                    if ( $cs_trans_status == 'pending' ) {
                                        $cs_trans_status = esc_html__('Pending', 'jobhunt');
                                    } else if ( $cs_trans_status == 'active' ) {
                                        $cs_trans_status = esc_html__('Active', 'jobhunt');
                                    } else if ( $cs_trans_status == 'approved' ) {
                                        $cs_trans_status = esc_html__('Approved', 'jobhunt');
                                    }
                                    $cs_trans_status = $cs_trans_status == 'approved' ? 'active' : $cs_trans_status;

                                    $cs_trans_expiry = get_post_meta(get_the_id(), "cs_transaction_expiry_date", true);
                                    if ( $cs_trans_expiry != '' ) {
                                        $cs_trans_expiry = date_i18n(get_option('date_format'), $cs_trans_expiry);
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo absint($cs_trans_num) ?></td>
                                        <td>#<?php echo absint($cs_trans_id) ?></td>
                                        <td><?php echo $cs_memberhsip_packages_options[$cs_trans_package]['memberhsip_pkg_title']; ?></td>
                                        <td><?php echo CS_FUNCTIONS()->cs_special_chars($cs_trans_expiry) ?></td>
                                        <td><?php echo absint($cs_trans_connects); ?></td>
                                        <td><?php echo absint($cs_trans_connects_used) ?></td>
                                        <td><?php echo absint($cs_trans_connects_remaining) ?></td>
                                        <td><?php echo ucfirst($cs_trans_status) ?></td>
                                    </tr>
                                    <?php
                                    $cs_trans_num ++;
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                        </di>
                    </div>

                    <?php
                } else {
                    echo '<div class="cs-no-record">' . cs_info_messages_listing(esc_html__("There is no package in your list.", 'jobhunt')) . '</div>';
                }
            } else {
                echo '<div class="cs-no-record">' . cs_info_messages_listing(esc_html__("There is no package in your list.", 'jobhunt')) . '</div>';
            }
            die();
        }

        add_action("wp_ajax_cs_ajax_candidate_membership_packages", "cs_ajax_candidate_membership_packages");
        add_action("wp_ajax_nopriv_cs_ajax_candidate_membership_packages", "cs_ajax_candidate_membership_packages");
    }

    /**
     * Start Function Applied  jobs for jobseek in ajax base
     */
    if ( ! function_exists('cs_ajax_candidate_appliedjobs') ) {

        function cs_ajax_candidate_appliedjobs($uid = '') {
            global $post, $cs_form_fields2, $cs_plugin_options;
            $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
            if ( $uid <> '' ) {
                $user = cs_get_user_id();
                if ( isset($user) && $user <> '' ) {
                    $cs_jobapplied_array = get_user_meta($user, 'cs-user-jobs-applied-list', true);
                    if ( ! empty($cs_jobapplied_array) )
                        $cs_jobapplied = array_column_by_two_dimensional($cs_jobapplied_array, 'post_id');
                    else
                        $cs_jobapplied = array();
                }
                $cs_jobapplied = apply_filters('jobhunt_candidate_multi_jobs_applied_list_order', $cs_jobapplied);
                $cs_jobapplied_array = apply_filters('jobhunt_candidate_multi_jobs_applied_list', $cs_jobapplied_array, $user);
                ?>
                <div class="cs-loader"></div>
                <section class="cs-favorite-jobs">
                    <div class="scetion-title">
                        <h3><?php esc_html_e('Applied Jobs', 'jobhunt'); ?></h3>
                        <?php
                        $args = array(
                            'posts_per_page' => "-1", 'post__in' => $cs_jobapplied, 'post_type' => 'jobs',
                            'meta_query' => array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'cs_job_expired',
                                    'value' => strtotime(current_time('d-m-Y')),
                                    'compare' => '<',
                                )
                            ),
                            'order' => "ASC"
                        );
                        $args = apply_filters('jobhunt_candidate_multi_jobs_applied_list_order_by', $args);
                        $custom_query = new WP_Query($args);
                        if ( $custom_query->found_posts > 0 ) {
                            ?>
                            <span>
                                <a href="javascript:void(0);" onclick="javascript:cs_ajax_remove_appliedjobs('<?php echo esc_js(admin_url('admin-ajax.php')) ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>',<?php echo absint($uid); ?>);">
                                    <?php esc_html_e('Remove Ended Jobs', 'jobhunt'); ?>
                                </a>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                    <ul class="top-heading-list">
                        <li><span><?php esc_html_e('Job Title', 'jobhunt'); ?></span></li>
                        <li><span><?php esc_html_e('Date Applied', 'jobhunt'); ?></span></li>
                    </ul>
                    <?php if ( ! empty($cs_jobapplied) && count($cs_jobapplied) > 0 ) { ?>
                        <ul class="feature-jobs">
                            <?php
                            $cs_blog_num_post = ( isset($cs_plugin_options['cs_job_dashboard_pagination']) && $cs_plugin_options['cs_job_dashboard_pagination'] != '' ) ? $cs_plugin_options['cs_job_dashboard_pagination'] : 10;
                            if ( empty($_REQUEST['page_id_all']) )
                                $_REQUEST['page_id_all'] = 1;

                            $args = array( 'posts_per_page' => $cs_blog_num_post, 'post__in' => $cs_jobapplied, 'post_type' => 'jobs', 'paged' => $_REQUEST['page_id_all'], 'order' => "ASC" );
                            $args = apply_filters('jobhunt_candidate_multi_jobs_applied_list_order_by', $args);
                            $custom_query = new WP_Query($args);
                            $count_post = $custom_query->found_posts;

                            if ( $custom_query->have_posts() ) :
                                while ( $custom_query->have_posts() ) : $custom_query->the_post();
                                    $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true) . '<br>';
                                    $cs_org_name = get_post_meta($post->ID, 'cs_org_name', true);

                                    $cs_jobs_thumb_url = '';
                                    $employer_img = '';
                                    // get employer images at run time
                                    $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true);
                                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                    if ( $employer_img != '' ) {
                                        $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                                    }
                                    if ( ! cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "" ) {
                                        $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                                    }
                                    ?>
                                    <li class="holder-<?php
                        echo intval($post->ID);
                        if ( $cs_job_expired < strtotime(current_time('d-m-Y')) ) {
                            echo ' cs-expired';
                        }
                                    ?>">
                                        <a class="hiring-img" href="<?php echo esc_url(get_permalink($post->ID)); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                        <div class="company-detail-inner">
                                            <?php
                                            echo '<h6>
                                                <a href="' . esc_url(get_the_permalink()) . '">' . get_the_title() . '</a>';
                                            if ( $cs_org_name <> '' ) {
                                                echo '<a href="' . esc_url(get_the_permalink()) . '">@ ' . $cs_org_name . '</a>';
                                            }
                                            echo '</h6>';
                                            do_action('jobhunt_candidate_multi_jobs_applied_count', $post->ID, $user);
                                            if ( $cs_job_expired < strtotime(current_time('d-m-Y')) ) {
                                                echo '<span>';
                                                esc_html_e('Ended', 'jobhunt');
                                                echo '</span>';
                                            }
                                            ?>
                                        </div>
                                        </div>
                                        <div class="company-date-option">
                                            <span><?php
                        $finded = in_multiarray($post->ID, $cs_jobapplied_array, 'post_id');
                        if ( $finded != '' )
                            if ( $cs_jobapplied_array[$finded[0]]['date_time'] != '' ) {
                                echo date_i18n(get_option('date_format'), $cs_jobapplied_array[$finded[0]]['date_time']);
                            }
                                            ?></span>
                                                <?php
                                            if ( $cs_job_expired < strtotime(current_time('d-m-Y')) ) {
                                                ?>
                                                <div class="control">
                                                    <a data-toggle="tooltip" data-placement="top" title="<?php esc_html_e("Remove", "jobhunt"); ?>" id="remove_resume_link<?php echo absint($post->ID); ?>" href="javascript:void(0);"  class="delete" 
                                                       onclick="javascript:cv_removejobs('<?php echo esc_js(admin_url('admin-ajax.php')) ?>', '<?php echo absint($post->ID); ?>',<?php echo absint($uid); ?>);" > 
                                                        <i class="icon-trash-o"></i>
                                                    </a>  
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </li>
                                    <?php
                                endwhile;
                            endif;
                            ?>
                        </ul>
                        <?php
                        //==Pagination Start
                        if ( $count_post > $cs_blog_num_post && $cs_blog_num_post > 0 ) {
                            echo '<nav>';
                            echo cs_ajax_pagination($count_post, $cs_blog_num_post, 'applied-jobs', 'candidate', $uid, '');
                            echo '</nav>';
                        }//==Pagination End 
                        ?>
                        <?php
                    } else {
                        echo '<div class="cs-no-record">' . cs_info_messages_listing(esc_html__("You did not applied for any job.", 'jobhunt')) . '</div>';
                    }
                    ?>
                </section>
                <?php
            } else {
                echo '<div class="no-result"><h1>' . esc_html__('Please create user profile.', 'jobhunt') . '</h1></div>';
            }
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('[data-toggle="tooltip"]').tooltip();
                });
            </script>
            <?php
            die();
        }

        add_action("wp_ajax_cs_ajax_candidate_appliedjobs", "cs_ajax_candidate_appliedjobs");
        add_action("wp_ajax_nopriv_cs_ajax_candidate_appliedjobs", "cs_ajax_candidate_appliedjobs");
    }

    /**
     * Start Function for Candidate Resume in Ajax base
     */
    if ( ! function_exists('cs_ajax_candidate_resume') ) {

        function cs_ajax_candidate_resume($uid = '') {
            global $post, $cs_plugin_options, $cs_form_fields2;
            $cs_award_switch = isset($cs_plugin_options['cs_award_switch']) ? $cs_plugin_options['cs_award_switch'] : '';
            $cs_portfolio_switch = isset($cs_plugin_options['cs_portfolio_switch']) ? $cs_plugin_options['cs_portfolio_switch'] : '';
            $cs_skills_switch = isset($cs_plugin_options['cs_skills_switch']) ? $cs_plugin_options['cs_skills_switch'] : '';
            $cs_education_switch = isset($cs_plugin_options['cs_education_switch']) ? $cs_plugin_options['cs_education_switch'] : '';
            $cs_experience_switch = isset($cs_plugin_options['cs_experience_switch']) ? $cs_plugin_options['cs_experience_switch'] : '';
            $cs_document_switch = isset($cs_plugin_options['cs_document_switch']) ? $cs_plugin_options['cs_document_switch'] : '';
            $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
            $cs_post_id = $uid;

            $custom_addon_active = false;
            $custom_addon_active = apply_filters('jobhunt_custom_addon_depedency', $custom_addon_active);

            if ( $cs_post_id <> '' ) {
                ?>
                <div id="main_resume_content">
                    <section class="tabs-list">
                        <h3><?php esc_html_e('My Resume', 'jobhunt'); ?></h3>
                    </section>
                    <?php if ( $cs_education_switch == 'on' ) { ?>        
                        <section class="cs-tabs cs-education" id="education">
                            <h4><i class="icon-graduation"></i><?php esc_html_e('Education', 'jobhunt'); ?></h4>
                            <ul class="accordion-list">
                                <form id="edu_list" name="cs_edu_list" enctype="multipart/form-data" method="POST">
                                    <?php
                                    cs_education_list_fe();
                                    $cs_opt_array = array(
                                        'std' => 'ajax_form_save',
                                        'id' => '',
                                        'echo' => true,
                                        'cust_name' => 'action',
                                    );
                                    $cs_form_fields2->cs_form_hidden_render($cs_opt_array);

                                    $cs_opt_array = array(
                                        'std' => $cs_post_id,
                                        'id' => '',
                                        'echo' => true,
                                        'cust_name' => 'cs_user',
                                    );
                                    $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                    ?>
                                </form>
                            </ul>
                        </section>
                        <?php
                    }
                    if ( $cs_experience_switch == 'on' ) {
                        ?>        
                        <section class="cs-tabs cs-experience" id="experience">
                            <h4><i class="icon-briefcase4"></i><?php esc_html_e('Experience', 'jobhunt'); ?></h4>
                            <ul class="accordion-list">
                                <form id="experience_list" enctype="multipart/form-data" method="POST">
                                    <?php
                                    cs_experience_list_fe();

                                    $cs_opt_array = array(
                                        'std' => 'ajax_form_save',
                                        'cust_id' => 'action',
                                        'cust_name' => 'action',
                                        'cust_type' => 'hidden',
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);

                                    $cs_opt_array = array(
                                        'std' => $cs_post_id,
                                        'cust_id' => 'cs_user',
                                        'cust_name' => 'cs_user',
                                        'cust_type' => 'hidden',
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>

                                </form>
                            </ul>
                        </section>
                        <?php
                    }
                    if ( $cs_portfolio_switch == 'on' && ! $custom_addon_active ) {
                        ?>        
                        <section class="cs-tabs cs-portfolio" id="portfolio">
                            <h4><i class="icon-pictures5"></i><?php esc_html_e('Portfolio', 'jobhunt'); ?></h4>
                            <ul class="accordion-list">
                                <?php cs_portfolio_list_fe(); ?>
                            </ul>
                        </section>
                        <?php
                    }
                    if ( $cs_skills_switch == 'on' ) {
                        ?>        
                        <section class="cs-tabs cs-skills" id="skills">
                            <h4><i class="icon-pie2"></i><?php esc_html_e('Skills', 'jobhunt'); ?></h4>
                            <form id="skill_list" enctype="multipart/form-data" method="POST">
                                <?php
                                cs_skills_list_fe();
                                $cs_opt_array = array(
                                    'std' => 'ajax_form_save',
                                    'cust_id' => 'action',
                                    'cust_name' => 'action',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);

                                $cs_opt_array = array(
                                    'std' => $cs_post_id,
                                    'cust_id' => 'cs_user',
                                    'cust_name' => 'cs_user',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </form>
                        </section>
                        <?php
                    }
                    if ( $cs_award_switch == 'on' ) {
                        ?>        
                        <section class="cs-tabs cs-awards" id="awards">
                            <h4><i class="icon-trophy5"></i><?php esc_html_e('Honors & Awards', 'jobhunt'); ?></h4>

                            <form id="award_list"   enctype="multipart/form-data" method="POST">
                                <?php cs_award_list_fe(); ?>
                                <?php
                                $cs_opt_array = array(
                                    'std' => 'ajax_form_save',
                                    'cust_id' => 'action',
                                    'cust_name' => 'action',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);

                                $cs_opt_array = array(
                                    'std' => $cs_post_id,
                                    'cust_id' => 'cs_user',
                                    'cust_name' => 'cs_user',
                                    'cust_type' => 'hidden',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </form>
                        </section>
                        <?php
                    }

                    do_action('jobhunt_user_fields_frontend_dashboard', $cs_post_id);
                    ?>
                </div><?php
        } else {
            esc_html_e('Please create user profile.', 'jobhunt');
        }
                ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('[data-toggle="tooltip"]').tooltip();
                });
            </script>
            <?php
            die();
        }

        add_action("wp_ajax_cs_ajax_candidate_resume", "cs_ajax_candidate_resume");
        add_action("wp_ajax_nopriv_cs_ajax_candidate_resume", "cs_ajax_candidate_resume");
    }

    /**
     * Start Function for Candidate CV's & Cover in Ajax Base
     */
    if ( ! function_exists('cs_ajax_candidate_cvcover') ) {

        function cs_ajax_candidate_cvcover($uid = '') {
            global $post, $cs_form_fields_frontend, $cs_form_fields2;
            if ( $uid == '' )
                $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
            $cs_cover_letter = get_user_meta($uid, 'cs_cover_letter', true);
            $cs_candidate_cv = get_user_meta($uid, 'cs_candidate_cv', true);
            ?>
            <div class="cs-loader"></div>
            <section class="cs-cover-letter">
                <div class="scetion-title">
                    <h3><?php esc_html_e('CV & Cover Letter', 'jobhunt'); ?> </h3>
                </div>
                <div class="dashboard-content-holder">
                    <form id="candidate_cv" name="cs_candidate"  enctype="multipart/form-data" method="POST">
                        <div class="cs-img-detail resume-upload">
                            <div class="inner-title">
                                <h5><?php esc_html_e('Your CV', 'jobhunt'); ?></h5>
                            </div>
                            <div class="upload-btn-div">
                                <div class="dragareamain" style="padding-bottom:0px;">
                                    <script type="text/ecmascript">
                                        jQuery(document).ready(function(){
                                        jQuery('.cs-uploadimg').change( function(e) {
                                        var img = URL.createObjectURL(e.target.files[0]);
                                        //var img = URL.createObjectURL(e.target.files[0]['type']);
                                        jQuery('#cs_candidate_cv').attr('value', img);
                                        });
                                        });
                                    </script>
                                    <div class="fileUpload uplaod-btn btn csborder-color cs-color">
                                        <span class="cs-color"><?php esc_html_e('Browse', 'jobhunt'); ?></span>
                                        <label class="browse-icon">
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => esc_html__('Browse', 'jobhunt'),
                                                'cust_id' => 'media_upload',
                                                'cust_name' => 'media_upload',
                                                'cust_type' => 'file',
                                                'force_std' => true,
                                                'extra_atr' => ' onchange="checkName(this, \'cs_candidate_cv\', \'button_action\')"',
                                                'classes' => 'upload cs-uploadimg cs-color csborder-color',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </label>
                                    </div>
                                    <div id="selecteduser-cv">
                                        <?php
                                        if ( isset($cs_candidate_cv) and $cs_candidate_cv <> '' && ( ! isset($cs_candidate_cv['error'])) ) {
                                            $cs_opt_array = array(
                                                'std' => $cs_candidate_cv,
                                                'cust_id' => 'cs_candidate_cv',
                                                'cust_name' => 'cs_candidate_cv',
                                                'cust_type' => 'hidden',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                            <div class="alert alert-dismissible user-resume" id="cs_candidate_cv_box">
                                                <div>
                                                    <?php
                                                    if ( isset($cs_candidate_cv) && $cs_candidate_cv != '' ) {
                                                        if ( cs_check_coverletter_exist($cs_candidate_cv) ) {
                                                            $uploads = wp_upload_dir();
                                                            echo '<a target="_blank" href="' . esc_url($cs_candidate_cv) . '">';
                                                            // uploaded file
                                                            $parts = preg_split('~_(?=[^_]*$)~', basename($cs_candidate_cv));
                                                            echo esc_html($parts[0]); // outputs "one_two_three"
                                                            echo '</a>';
                                                            ?>
                                                            <div class="gal-edit-opts close"><a href="javascript:cs_del_cover_letter('cs_candidate_cv')" class="delete">
                                                                    <span aria-hidden="true">×</span></a>
                                                            </div>
                                                            <?php
                                                        } else {
                                                            esc_html_e("File not Available", "jobhunt");
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php } ?>				
                                    </div>
                                </div>
                                <span class="cs-status-msg-cv-upload"><?php esc_html_e('Suitable files are .doc,docx,rft,pdf & .pdf', 'jobhunt'); ?></span>              
                            </div>
                        </div>
                        <?php do_action('jobhunt_candiadte_cv_fields'); ?>
                        <div class="inner-title">
                            <h5><?php esc_html_e('Your Cover Letter', 'jobhunt'); ?></h5>
                        </div><?php
                        $cs_cover_letter = (isset($cs_cover_letter)) ? ($cs_cover_letter) : '';
                        echo $cs_form_fields2->cs_form_textarea_render(
                                array( 'name' => esc_html__('Your Cover Letter', 'jobhunt'),
                                    'id' => 'cs_cover_letter',
                                    'classes' => 'col-md-12',
                                    'cust_name' => 'cs_cover_letter',
                                    'std' => $cs_cover_letter,
                                    'description' => '',
                                    'return' => true,
                                    'array' => true,
                                    'cs_editor' => true,
                                    'force_std' => true,
                                    'hint' => ''
                                )
                        );
                        ?>
                        <section class="cs-update-btn">
                            <?php
                            $cs_opt_array = array(
                                'std' => 'update_cv_profile',
                                'cust_id' => 'user_profile',
                                'cust_name' => 'user_profile',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);

                            $cs_opt_array = array(
                                'std' => $uid,
                                'cust_id' => 'cs_user',
                                'cust_name' => 'cs_user',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            ?>
                            <a  href="javascript:void(0);" name="button_action" class="acc-submit cs-section-update cs-color csborder-color" onclick="javascript:ajax_candidate_cv_form_save('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo esc_js(wp_jobhunt::plugin_url()); ?>', 'candidate_cv', '<?php echo absint($uid); ?>')"><?php esc_html_e('Update', 'jobhunt'); ?></a>
                            <?php
                            $cs_opt_array = array(
                                'std' => 'ajax_candidate_cv_form_save',
                                'cust_id' => 'action',
                                'cust_name' => 'action',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);

                            $cs_opt_array = array(
                                'std' => $uid,
                                'cust_id' => 'cs_user',
                                'cust_name' => 'cs_user',
                                'cust_type' => 'hidden',
                            );
                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                            ?>
                        </section>
                    </form>
                </div>
            </section>
            <?php
            die();
        }

        $is_cv_documents = apply_filters('jobhunt_is_candidate_cv_documnets', false);
        if ( $is_cv_documents == true ) {
            add_action("wp_ajax_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cv_documents_callback");
            add_action("wp_ajax_nopriv_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cv_documents_callback");
        } else {
            add_action("wp_ajax_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cvcover");
            add_action("wp_ajax_nopriv_cs_ajax_candidate_cvcover", "cs_ajax_candidate_cvcover");
        }
    }

    /**
     * Start Function for Candidate post type session in Ajax
     */
    if ( ! function_exists('cs_ajax_set_session') ) {

        function cs_ajax_set_session() {
            if ( session_id() == '' ) {
                session_start();
            }
            $_SESSION["cs_post_type"] = $_POST['post_type'];
            die();
        }

        add_action("wp_ajax_cs_ajax_set_session", "cs_ajax_set_session");
        add_action("wp_ajax_nopriv_cs_ajax_set_session", "cs_ajax_set_session");
    }