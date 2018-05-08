<?php
/*
  Plugin Name: WP JobHunt
  Plugin URI: http://themeforest.net/user/Chimpstudio/
  Description: JobHunt
  Version: 2.1
  Author: ChimpStudio
  Text Domain: jobhunt
  Author URI: http://themeforest.net/user/Chimpstudio/
  License: GPL2
  Copyright 2015  chimpgroup  (email : info@chimpstudio.co.uk)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, United Kingdom
 */
if ( ! class_exists('wp_jobhunt') ) {

    class wp_jobhunt {

        public $plugin_url;
        public $plugin_dir;
        public static $cs_version;
        public static $cs_data_update_flag;

        /**
         * Start Function of Construct
         */
        public function __construct() {

            self::$cs_version = '2.1';
            self::$cs_data_update_flag = 'cs_data_update_flag_' . str_replace(".", "_", self::$cs_version);

            add_action('init', array( $this, 'load_plugin_textdomain' ), 0);

            add_action('wp_footer', array( $this, 'cs_loader' ));

            remove_filter('pre_user_description', 'wp_filter_kses');
            add_filter('pre_user_description', 'wp_filter_post_kses');

            // Add optinos in Email Template Settings
            add_filter('jobhunt_email_template_settings', array( $this, 'email_template_settings_callback' ), 0, 1);

            $this->define_constants();
            $this->includes();

            add_action('admin_notices', array( $this, 'check_db_update_plugin' ), 5);
            add_action('wp_ajax_cs_db_data_update', array( $this, 'cs_db_data_update_callback' ));
            add_action('wp_ajax_nopriv_cs_db_data_update', array( $this, 'cs_db_data_update_callback' ));
        }

        /**
         * Start Function how to Create WC Contants
         */
        private function define_constants() {

            global $post, $wp_query, $cs_plugin_options, $current_user, $cs_jh_scodes, $plugin_user_images_directory;
            $cs_plugin_options = get_option('cs_plugin_options');
            $this->plugin_url = plugin_dir_url(__FILE__);
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $plugin_user_images_directory = 'wp-jobhunt-users';
        }

        /**
         * End Function how to Create WC Contants
         */

        /**
         * Start Function how to add core files used in admin and theme
         */
        public function includes() {

            $active_plugin = false;
            $active_plugin = apply_filters('jobhunt_email_template_depedency', $active_plugin);

            // Email Templates.
            require_once 'classes/email-templates/class-register-template.php';
            require_once 'classes/email-templates/class-reset-password-template.php';
            require_once 'classes/email-templates/class-confirm-reset-password-template.php';
            require_once 'classes/email-templates/class-job-add-template.php';
            require_once 'classes/email-templates/class-employer-contact-candidate-email-template.php';
            require_once 'classes/email-templates/class-candidate-contact-employer-email-template.php';
            require_once 'classes/email-templates/class-new-user-notification-site-owner-template.php';
            require_once 'classes/email-templates/class-job-apply-successfully.php';
            if ( ! $active_plugin ) {
                require_once 'classes/email-templates/class-job-applied-employer-notification.php';
            }
            require_once 'classes/email-templates/class-job-update-email-template.php';
            require_once 'classes/email-templates/class-job-approved-email-template.php';
            require_once 'classes/email-templates/class-job-not-approved-email-template.php';
            require_once 'classes/email-templates/class-job-waiting-email-template.php';
            require_once 'classes/email-templates/class-job-delete-template.php';
            require_once 'classes/email-templates/class-approved-employer-profile-template.php';
            require_once 'classes/email-templates/class-not-approved-candidate-profile-template.php';
            require_once 'classes/email-templates/class-approved-candidate-profile-template.php';
            require_once 'classes/email-templates/class-not-approved-employer-profile-template.php';
            require_once 'classes/email-templates/class-employer-register-template.php';
            require_once 'classes/email-templates/class-candidate-register-template.php';

            require_once 'admin/classes/class-email.php';
            require_once 'admin/classes/class-save-post-options.php';
            require_once 'templates/elements/shortcode_functions.php';
            require_once 'admin/include/form-fields/cs_form_fields.php';
            require_once 'admin/include/form-fields/cs_html_fields.php';
            require_once 'classes/class_transactions.php';
            require_once 'include/form-fields/form-fields-frontend.php';
            require_once 'include/form-fields/cs_html_fields_frontend.php';
            require_once 'helpers/notification-helper.php';
            require_once 'admin/settings/plugin_settings.php';
            require_once 'admin/settings/includes/plugin_options.php';
            require_once 'admin/settings/includes/plugin_options_fields.php';
            require_once 'admin/settings/includes/plugin_options_functions.php';
            require_once 'admin/settings/includes/plugin_options_array.php';
            require_once 'admin/settings/user-import/cs_import.php';
            require_once 'admin/include/post-types/jobs/job_custom_fields.php';
            require_once 'admin/include/post-types/candidate/candidate_custom_fields.php';
            require_once 'admin/include/post-types/employer/employer_custom_fields.php';
            // importer hooks
            require_once 'admin/include/importer_hooks.php';

            require_once 'classes/class_dashboards_templates.php';
            require_once 'templates/dashboards/candidate/templates_functions.php';
            require_once 'templates/dashboards/candidate/templates_ajax_functions.php';
            require_once 'templates/dashboards/employer/employer_functions.php';
            require_once 'templates/dashboards/employer/employer_templates.php';
            require_once 'templates/dashboards/employer/employer_ajax_templates.php';
            require_once 'payments/class-payments.php';
            require_once 'payments/custom-wooc-hooks.php';
            require_once 'payments/config.php';
            require_once 'admin/include/post-types/jobs/jobs.php';
            // move at user meta
            require_once 'admin/include/post-types/transaction/transaction.php';
            require_once 'admin/include/post-types/transaction/transactions_meta.php';
            require_once 'admin/include/post-types/jobs/jobs_meta.php';
            // user meta
            require_once 'admin/include/user-meta/cs_meta.php';
            require_once 'widgets/widgets.php';
            // Cv Package Files
            require_once 'templates/packages/cv/cv_package_elements.php';
            require_once 'templates/packages/cv/cv_package_functions.php';
            // Job Package Files
            require_once 'templates/packages/jobs/job_package_elements.php';
            require_once 'templates/packages/jobs/job_package_functions.php';
            // Apply Job Package Files
            require_once 'templates/packages/membership/membership_package_elements.php';
            require_once 'templates/packages/membership/membership_package_functions.php';
            // Job Post Files
            require_once 'templates/elements/job-post/job-post-elements.php';
            require_once 'templates/elements/job-post/job-post-functions.php';
            // Listing Tab Files
            require_once 'templates/elements/listing-tab/listing-tab-elements.php';
            require_once 'templates/elements/listing-tab/listing-tab-functions.php';
            require_once 'templates/elements/listing-tab/listing-tab-helper.php';
            // Job specialisms Files
            require_once 'templates/elements/specialisms/elements.php';
            require_once 'templates/elements/specialisms/functions.php';
            require_once 'templates/functions.php';
            // employer element   
            require_once 'templates/listings/employer/employer_element.php';
            // Job element   
            require_once 'templates/listings/jobs/jobs_element.php';
            // Job search
            require_once 'templates/elements/jobs-search/jobs-search-element.php';
            // Candidate  
            require_once 'templates/listings/candidate/candidate_element.php';
            // Candidate  membership packages
            require_once 'admin/settings/includes/plugin_options_membersihp_pckgs.php';
            // for employer listing
            require_once 'templates/listings/employer/employer_template.php';
            // for job sesker listing
            require_once 'templates/listings/candidate/candidate_template.php';
            require_once 'templates/dashboards/candidate/candidate_functions.php';
            // for jobs listing
            require_once 'templates/listings/jobs/jobs_template.php';
            require_once 'templates/elements/jobs-search/jobs-search-template.php';
            // for login
            require_once 'templates/elements/login/login_functions.php';
            require_once 'templates/elements/login/login_forms.php';
            require_once 'templates/elements/login/shortcodes.php';
            require_once 'templates/elements/login/class-signup-features.php';
            require_once 'templates/elements/login/cs-social-login/cs_social_login.php';
            require_once 'templates/elements/login/cs-social-login/google/cs_google_connect.php';
            // linkedin login
            // recaptchas
            require_once 'templates/elements/login/recaptcha/autoload.php';
            // Location Checker
            require_once 'classes/class_location_check.php';
            add_filter('template_include', array( &$this, 'cs_single_template' ));
            add_action('admin_enqueue_scripts', array( &$this, 'cs_defaultfiles_plugin_enqueue' ), 2);
            add_action('wp_enqueue_scripts', array( &$this, 'cs_defaultfiles_plugin_enqueue' ), 2);
            add_action('wp_enqueue_scripts', array( &$this, 'cs_enqueue_responsive_front_scripts' ), 3);


            add_action('admin_init', array( $this, 'cs_all_scodes' ));
            add_filter('body_class', array( $this, 'cs_boby_class_names' ));
        }

        /**
         * End Function how to add core files used in admin and theme
         */
        public function cs_loader() {//return;
            ?>
            <?php if ( ! wp_jobhunt::is_demo_user_modification_allowed() ) : ?>
                <script type="text/javascript">
                    (function ($) {
                        $(document).ready(function () {
                            bind_rest_auth_event();
                            show_error_msg();
                            setInterval(bind_rest_auth_event, 1000);
                        });

                        function bind_rest_auth_event() {
                            var selectors = "input[type='submit'], .acc-submit, .cs-check-tabs, #cs_add_edus, #cs_add_epsx, #add_pt_to_js_list, #cs_add_skill [data-original-title='Remove'], #id_truebtn";
                            $(selectors).prop('onclick', null).removeAttr('onclick');
                            $(selectors).off("click");
                            $(document).off("click", selectors);
                            $("#postjobs, #editjob").off('click', '.cs-check-tabs');
                            $("#resumes").off('click', '[id^="jb-cont-send-"]');
                            $("body").off("click", selectors);
                            $("body").on("click", selectors, function (e) {
                                e.stopPropagation();
                                show_error_msg();

                                return false;
                            });
                        }

                        function show_error_msg() {
                            jQuery('#cs_alerts').html('<div class="cs-remove-msg error"><i class="icon-warning2"></i><?php echo esc_html__('Demo users are not allowed to modify information.', 'jobhunt'); ?></div>');
                            classes = jQuery('#cs_alerts').attr('class');
                            classes = classes + " active";
                            jQuery('#cs_alerts').addClass(classes);
                            setTimeout(function () {
                                jQuery('#cs_alerts').removeClass("active");
                            }, 4000);
                        }
                    })(jQuery);
                </script>
            <?php endif; ?>
            <?php
        }

        /**
         * Start Function how to add Specific CSS Classes by filter
         */
        function cs_boby_class_names($classes) {
            $classes[] = 'wp-jobhunt';
            return $classes;
        }

        /**
         * End Function how to add Specific CSS Classes by filter
         */

        /**
         * Start Function how to access admin panel
         */
        public function prevent_admin_access() {
            if ( is_user_logged_in() ) {

                if ( strpos(strtolower($_SERVER['REQUEST_URI']), '/wp-admin') !== false && (current_user_can('cs_employer') || current_user_can('cs_candidate')) ) {
                    wp_redirect(get_option('siteurl'));
                    add_filter('show_admin_bar', '__return_false');
                }
            }
        }

        /**
         * Start Function how to Add textdomain for translation
         */
        public function load_plugin_textdomain() {
            global $cs_plugin_options;

            if ( function_exists('icl_object_id') ) {

                global $sitepress, $wp_filesystem;

                require_once ABSPATH . '/wp-admin/includes/file.php';

                $backup_url = '';

                if ( false === ($creds = request_filesystem_credentials($backup_url, '', false, false, array()) ) ) {

                    return true;
                }

                if ( ! WP_Filesystem($creds) ) {
                    request_filesystem_credentials($backup_url, '', true, false, array());
                    return true;
                }

                $cs_languages_dir = plugin_dir_path(__FILE__) . 'languages/';

                $cs_all_langs = $wp_filesystem->dirlist($cs_languages_dir);

                $cs_mo_files = array();
                if ( is_array($cs_all_langs) && sizeof($cs_all_langs) > 0 ) {

                    foreach ( $cs_all_langs as $file_key => $file_val ) {

                        if ( isset($file_val['name']) ) {

                            $cs_file_name = $file_val['name'];

                            $cs_ext = pathinfo($cs_file_name, PATHINFO_EXTENSION);

                            if ( $cs_ext == 'mo' ) {
                                $cs_mo_files[] = $cs_file_name;
                            }
                        }
                    }
                }

                $cs_active_langs = $sitepress->get_current_language();

                foreach ( $cs_mo_files as $mo_file ) {
                    if ( strpos($mo_file, $cs_active_langs) !== false ) {
                        $cs_lang_mo_file = $mo_file;
                    }
                }
            }

            $locale = apply_filters('plugin_locale', get_locale(), 'jobhunt');
            $dir = trailingslashit(WP_LANG_DIR);
            if ( isset($cs_lang_mo_file) && $cs_lang_mo_file != '' ) {
                load_textdomain('jobhunt', plugin_dir_path(__FILE__) . "languages/" . $cs_lang_mo_file);
            } else {
                load_textdomain('jobhunt', plugin_dir_path(__FILE__) . "languages/jobhunt-" . $locale . '.mo');
            }
        }

        /**
         * Fetch and return version of the current plugin
         *
         * @return	string	version of this plugin
         */
        public static function get_plugin_version() {
            $plugin_data = get_plugin_data(__FILE__);
            return $plugin_data['Version'];
        }

        /**
         * Start Function how to Add User and custom Roles
         */
        public function cs_add_custom_role() {
            add_role('guest', 'Guest', array(
                'read' => true, // True allows that capability
                'edit_posts' => true,
                'delete_posts' => false, // Use false to explicitly deny
            ));
        }

        /**
         * End Function how to Add User and custom Roles
         */

        /**
         * Start Function how to Add plugin urls
         */
        public static function plugin_url() {
            return plugin_dir_url(__FILE__);
        }

        /**
         * End Function how to Add plugin urls
         */

        /**
         * Start Function how to Add image url for plugin directory
         */
        public static function plugin_img_url() {
            return plugin_dir_url(__FILE__);
        }

        /**
         * End Function how to Add image url for plugin directory
         */

        /**
         * Start Function how to Create plugin Directory
         */
        public static function plugin_dir() {
            return plugin_dir_path(__FILE__);
        }

        /**
         * End Function how to Create plugin Directory
         */

        /**
         * Start Function how to Activate the plugin
         */
        public static function activate() {
            global $plugin_user_images_directory;
            add_option('cs_jobhunt_plugin_activation', 'installed');
            add_option('cs_jobhunt', '1');
            // create user role for candidate and employer
            $result = add_role(
                    'cs_employer', esc_html__('Employer', 'jobhunt'), array(
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                    )
            );
            $result = add_role(
                    'cs_candidate', esc_html__('Candidate', 'jobhunt'), array(
                'read' => false,
                'edit_posts' => false,
                'delete_posts' => false,
                    )
            );
            // create users images directory 
            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_dir = $upload_dir . '/' . $plugin_user_images_directory;
            if ( ! is_dir($upload_dir) ) {
                mkdir($upload_dir, 0777);
            }
        }

        /**
         * Start Function how to DeActivate the plugin
         */
        static function deactivate() {
            delete_option('cs_jobhunt_plugin_activation');
            delete_option('cs_jobhunt', false);
        }

        /**
         * Start Function how to Add Theme Templates
         */
        public function cs_single_template($single_template) {
            global $post;

            if ( get_post_type() == 'candidate' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path(__FILE__) . 'templates/single_pages/single-candidate.php';
                }
            }
            if ( get_post_type() == 'employer' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path(__FILE__) . 'templates/single_pages/single-employer.php';
                }
            }
            if ( get_post_type() == 'jobs' ) {
                if ( is_single() ) {
                    $single_template = plugin_dir_path(__FILE__) . 'templates/single_pages/single-jobs.php';
                }
            }
            return $single_template;
        }

        /*
         * Update db hook
         */

        public static function check_db_update_plugin() {
            $purchase_code_data = get_option('item_purchase_code_verification');
            $envato_email = isset($purchase_code_data['envato_email_address']) ? $purchase_code_data['envato_email_address'] : '';
            $selected_demo = isset($purchase_code_data['selected_demo']) ? $purchase_code_data['selected_demo'] : '';
            $demos_array = array();
            $options = "<option value=''>Pleae select a demo you are using right now</option>";
            if ( function_exists('get_demo_data_structure') ) {
                $demos = get_demo_data_structure();
            }

            if ( ! empty($demos) ) {
                foreach ( $demos as $demo_key => $demo_value ) {
                    $demos_array[$demo_key] = $demo_key;
                    $demo_slug = isset($demo_value['slug']) ? $demo_value['slug'] : '';
                    $demo_name = isset($demo_value['name']) ? $demo_value['name'] : '';
                    $selected = ( $demo_slug == $selected_demo ) ? ' selected' : '';
                    $options .= "<option value='" . $demo_slug . "'" . $selected . ">" . $demo_name . "</option>";
                }
            }
            //$item_purchase_code    = isset( $purchase_code_data['item_puchase_code'] )? $purchase_code_data['item_puchase_code'] : '';
            if ( get_option(self::$cs_data_update_flag) !== 'yes' ) {

                $class = 'notice notice-warning is-dismissible';
                $popup_fields = '';
                //$affected_packages = '<ul><li>' . implode('</li><li>', $affected_plugins) . '</li></ul>';
                $popup_message = '<h1 style=\'color: #ff2e2e; margin-top: 0; float: none;\'>Warning!!!</h1> By upgrading it will take some time. So please wait after move next:<br>';

                if ( class_exists('cs_framework') ) {

                    $popup_fields = "<div id=\'confirmText\' style=\'padding-left: 20px; padding-right: 20px;\'><div class='row'>\
                        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\
                            <div class='field-holder'>\
                                    <input type='text' placeholder='Envato Provided Email *' id='envato_email' name='envato_email' value='" . $envato_email . "'>\
                            </div>\
                        </div>\
                        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>\
                            <div class='field-holder'>\
                                    <select name='theme_demo' class='chosen-select' id='theme_demo'>" . $options . "</select>\
                            </div>\
                        </div>\
                    </div></div>";
                }

                $popup = '
				<script type="text/javascript">
                                                        
					var html_popup1 = "<div id=\'confirmOverlay\' style=\'display:block\'><div id=\'confirmBox\' class=\'update-popup-box\'>";
					html_popup1 += "<div id=\'confirmText\' style=\'padding-left: 20px; padding-right: 20px;\'>' . $popup_message . '</div>";
					html_popup1 += "' . $popup_fields . '";
					html_popup1 += "<div id=\'confirmButtons\'><div class=\'button confirm-yes confirm-db-update-btn\'>Upgrade</div><div class=\'button confirm-no\'>Cancel</div><br class=\'clear\'></div><div id=\'property-visibility-update-msg\'></div></div></div>";

					(function($){
						$(function() {
							$(".btnConfirmVisiblePropertyUpgrade").click(function() {
								$(this).parent().append(html_popup1);
								$(".confirm-db-update-btn").click(function() {

									//start ajax request
									var old_html =  $(".confirm-db-update-btn").html();
									var theme_demo = $("#theme_demo").val();

									var envato_email = $("#envato_email").val();

									var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,50}\b$/i;
									var result = pattern.test(envato_email);
									if( envato_email != "" && result == false){
										alert("Please provide valid email address.");
										return false;
									}
									$(".confirm-db-update-btn").html("<i class=\'icon-spinner\' style=\'margin:13px 0 0 -5px;\'></i>");
									$.ajax({
										type: "POST",
										dataType: "json",
										url: ajaxurl,
										data: "envato_email="+envato_email+"&theme_demo="+theme_demo+"&action=cs_db_data_update",
										success: function (response) {
											$(".confirm-db-update-btn").html(old_html);
											$("#property-visibility-update-msg").html("<p style=\'color: #008000;padding-left: 20px; padding-right: 20px;\'>" + response.msg + "</p>");
										}
									});

									// end ajax request

								});
								$(".confirm-no").click(function() {
									$("#confirmOverlay").remove();
									window.location = window.location;
								});
								return false;
							});
						});
					})(jQuery);
				</script>';
                $message = '<h2>Job Career Alert!</h2>';
                $message .= 'DB Structure Need to update for latest plugin compatibility. <br/><br/> <a href="#" class="btnConfirmVisiblePropertyUpgrade button button-primary button-hero hide-if-no-customize">Click here to run update</a> ' . $popup;
                printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
            }
        }

        /*
         * update all data
         */

        public function cs_db_data_update_callback() {
            do_action('cs_plugin_db_structure_updater');
            add_option(self::$cs_data_update_flag, 'yes');
            $json['type'] = 'success';
            $json['msg'] = __('your DB structure updated successfully.', 'jobhunt');
            echo json_encode($json);
            die();
        }

        /**
         * Custom Css 
         */
        public function cs_custom_inline_styles_method() {

            $cs_plugin_options = get_option('cs_plugin_options');
            wp_enqueue_style('custom-style-inline', plugins_url('/assets/css/custom_script.css', __FILE__));
            $cs_custom_css = isset($cs_plugin_options['cs_style-custom-css']) ? $cs_plugin_options['cs_style-custom-css'] : 'sdfdsa';
            $custom_css = $cs_custom_css;
            wp_add_inline_style('custom-style-inline', $custom_css);
        }

        /**
         * Start Function how to Includes Default Scripts and Styles
         */
        public function cs_defaultfiles_plugin_enqueue() {
            global $cs_plugin_options;
            if ( is_admin() ) {
                wp_enqueue_media();
            }
            if ( ! is_admin() ) {
                wp_enqueue_style('cs_iconmoon_css', plugins_url('/assets/icomoon/css/iconmoon.css', __FILE__));
                wp_enqueue_style('cs_bootstrap_css', plugins_url('/assets/css/bootstrap.css', __FILE__));
                wp_enqueue_style('jobcareer_widgets_css', plugins_url('/assets/css/widget.css', __FILE__));
                $cs_plugin_options = get_option('cs_plugin_options');
                $cs_default_css_option = isset($cs_plugin_options['cs_common-elements-style']) ? $cs_plugin_options['cs_common-elements-style'] : '';
                //Common css Elements
                if ( $cs_default_css_option == 'on' ) {
                    wp_enqueue_style('cs_jobhunt_plugin_defalut_elements_css', plugins_url('/assets/css/default-elements.css', __FILE__));
                }
                wp_enqueue_style('cs_jobhunt_plugin_css', plugins_url('/assets/css/cs-jobhunt-plugin.css', __FILE__));
                wp_enqueue_style('cs_jobhunt_dashboard', plugins_url('/assets/css/cs-jobhunt-dashboard.css', __FILE__));


                wp_enqueue_script('cs_waypoints_min_js', plugins_url('/assets/scripts/waypoints.min.js', __FILE__), '', '', true); //?
            }
            wp_enqueue_script('job-editor-script', plugins_url('/assets/scripts/jquery-te-1.4.0.min.js', __FILE__));
            wp_enqueue_style('job-editor-style', plugins_url('/assets/css/jquery-te-1.4.0.css', __FILE__));
            wp_enqueue_style('cs_slicknav_css', plugins_url('/assets/css/slicknav.css', __FILE__));
            wp_enqueue_style('cs_datetimepicker_css', plugins_url('/assets/css/jquery_datetimepicker.css', __FILE__));
            wp_enqueue_style('cs_bootstrap_slider_css', plugins_url('/assets/css/bootstrap-slider.css', __FILE__));
            wp_enqueue_script('cs_bootstrap_slider_js', plugins_url('/assets/scripts/bootstrap-slider.js', __FILE__), '', '', true);
            wp_enqueue_style('cs_chosen_css', plugins_url('/assets/css/chosen.css', __FILE__));


            // All JS files
            // wp_enqueue_script(array('jquery'));
            // temporary off
            wp_enqueue_script('cs_bootstrap_min_js', plugins_url('/assets/scripts/bootstrap.min.js', __FILE__), array( 'jquery' ), '', true);
            $google_api_key = '?libraries=places';
            if ( isset($cs_plugin_options['cs_google_api_key']) && $cs_plugin_options['cs_google_api_key'] != '' ) {
                $google_api_key = '?key=' . $cs_plugin_options['cs_google_api_key'] . '&libraries=places';
            }
            wp_enqueue_script('cs_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key);
            wp_enqueue_script('cs_map_info_js', plugins_url('/assets/scripts/map_infobox.js', __FILE__), '', '', true);

            wp_enqueue_script('cs_my_upload_js', '', array( 'jquery', 'media-upload', 'thickbox', 'jquery-ui-droppable', 'jquery-ui-datepicker', 'jquery-ui-slider', 'wp-color-picker' ));
            wp_enqueue_script('cs_chosen_jquery_js', plugins_url('/assets/scripts/chosen.jquery.js', __FILE__), '', '', true);
            wp_enqueue_script('cs_scripts_js', plugins_url('/assets/scripts/scripts.js', __FILE__), '', '', true);
            wp_enqueue_script('cs_isotope_min_js', plugins_url('/assets/scripts/isotope.min.js', __FILE__), '', '', true); //?
            wp_enqueue_script('cs_modernizr_min_js', plugins_url('/assets/scripts/modernizr.min.js', __FILE__), '', '', '');
            wp_enqueue_script('cs_browser_detect_js', plugins_url('/assets/scripts/browser-detect.js', __FILE__), '', '', '');
            wp_enqueue_script('cs_slick_js', plugins_url('/assets/scripts/slick.js', __FILE__), '', '', true);
            wp_enqueue_script('cs_jquery_sticky_js', plugins_url('/assets/scripts/jquery.sticky.js', __FILE__), '', '', true); //?
            wp_enqueue_script('cs_jobhunt_functions_js', plugins_url('/assets/scripts/jobhunt_functions.js', __FILE__), '', '', true);


            $cs_pt_array = array(
                'select_file' => esc_html__('Select File', 'jobhunt'),
                'add_file' => esc_html__('Add File', 'jobhunt'),
                'geolocation_error_msg' => esc_html__('Geolocation is not supported by this browser.', 'jobhunt'),
                'title' => esc_html__('Title', 'jobhunt'),
                'plugin_options_replace' => esc_html__('Current Plugin options will be replaced with the default options.', 'jobhunt'),
                'delete_backup_file' => esc_html__('This action will delete your selected Backup File. Are you want to continue?', 'jobhunt'),
                'valid_email_error' => esc_html__('Please Enter valid Email address.', 'jobhunt'),
                'shortlist' => esc_html__('Shortlist', 'jobhunt'),
                'shortlisted' => esc_html__('Shortlisted', 'jobhunt'),
                'are_you_sure' => esc_html__('Are you sure to do this?', 'jobhunt'),
                'cancel' => esc_html__('Cancel', 'jobhunt'),
                'delete' => esc_html__('Delete', 'jobhunt'),
                'drag_marker' => esc_html__('Drag this Marker', 'jobhunt'),
                'couldnt_find_coords' => esc_html__('Couldn\'t find coordinates for this place', 'jobhunt'),
                'active' => esc_html__('Active', 'jobhunt'),
                'applied' => esc_html__('Applied', 'jobhunt'),
                'inactive' => esc_html__('Inactive', 'jobhunt'),
                'apply_without_login' => esc_html__('Apply Without Login', 'jobhunt'),
                'apply_now' => esc_html__('Apply Now', 'jobhunt'),
                'fill_all_fields' => esc_html__('Please fill all required fields.', 'jobhunt'),
                'cover_length' => sprintf(__('Cover letter length must be %s to %s long.', 'jobhunt'), 10, 500),
                'min_length' => 10,
                'max_length' => 500,
                'character_remaining' => esc_html__('characters remaining', 'jobhunt'),
                'number_field_invalid' => esc_html__('You can only enter numbers in this field.', 'jobhunt'),
                'select_job_empty_error' => esc_html__('Please select a job first then you can send invitation to freelancer.', 'jobhunt'),
                'reply_field_empty' => esc_html__('Please fill reply field.', 'jobhunt'),
            );



            wp_localize_script('cs_jobhunt_functions_js', 'jobhunt_functions_vars', $cs_pt_array);
            wp_enqueue_script('cs_exra_functions_js', plugins_url('/assets/scripts/extra_functions.js', __FILE__), '', '', true);
            $cs_pt_array = array(
                'currency_sign' => jobcareer_get_currency_sign(),
                'currency_position' => jobcareer_get_currency_position(),
                'there_is_prob' => esc_html__('There is some Problem.', 'jobhunt'),
                'oops_nothing_found' => esc_html__('Oops, nothing found!', 'jobhunt'),
                'title' => esc_html__('Title', 'jobhunt'),
            );
            wp_localize_script('cs_exra_functions_js', 'cs_vars', $cs_pt_array);


            if ( ! is_admin() ) {
                wp_enqueue_script('cs_functions_js', plugins_url('/assets/scripts/functions.js', __FILE__), '', '', true);
                $cs_func_array = array(
                    'more' => esc_html__('More', 'jobhunt'),
                    'name_error' => esc_html__('Please Fill in Name.', 'jobhunt'),
                    'email_error' => esc_html__('Please Enter Email.', 'jobhunt'),
                    'valid_email_error' => esc_html__('Please Enter valid Email address.', 'jobhunt'),
                    'subject_error' => esc_html__('Please Fill in Subject.', 'jobhunt'),
                    'msg_error' => esc_html__('Please Fill in Message.', 'jobhunt'),
                );
                wp_localize_script('cs_functions_js', 'cs_func_vars', $cs_func_array);
            }
            wp_enqueue_script('cs_datetimepicker_js', plugins_url('/assets/scripts/jquery_datetimepicker.js', __FILE__), '', '', true);

            if ( ! wp_is_mobile() ) {
                /* include only when not a mobile device */
                wp_enqueue_script('cs_custom_resolution_js', plugins_url('/assets/scripts/custom-resolution.js', __FILE__), '', '', true);
            }

            /**
             *
             * @login popup script files
             */
            if ( ! function_exists('cs_range_slider_scripts') ) {

                function cs_range_slider_scripts() {
                    
                }

            }
            /**
             *
             * @login popup script files
             */
            if ( ! function_exists('cs_google_recaptcha_scripts') ) {

                function cs_google_recaptcha_scripts() {
                    wp_enqueue_script('cs_google_recaptcha_scripts', cs_server_protocol() . 'www.google.com/recaptcha/api.js?onload=cs_multicap_all_functions&amp;render=explicit', '', '');
                }

            }
            /**
             *
             * @login popup script files
             */
            if ( ! function_exists('cs_login_box_popup_scripts') ) {

                function cs_login_box_popup_scripts() {
                    wp_enqueue_script('cs_uiMorphingButton_fixed_js', plugins_url('/assets/scripts/uiMorphingButton_fixed.js', __FILE__), '', '', true);
                }

            }
            /**
             *
             * @datetime calender script files
             */
            if ( ! function_exists('cs_datetime_picker_scripts') ) {

                function cs_datetime_picker_scripts() {
                    
                }

            }
            /**
             *
             * @sidemenue effect script files
             */
            if ( ! function_exists('cs_visualnav_sidemenu') ) {

                function cs_visualnav_sidemenu() {
                    wp_enqueue_script('cs_jquery_easing_js', plugins_url('/assets/scripts/jquery.easing.1.2.js', __FILE__), '', '', true);
                    wp_enqueue_script('cs_jquery_visualNav_js', plugins_url('/assets/scripts/jquery.visualNav.js', __FILE__), '', '', true);
                    wp_enqueue_script('cs_jquery_smint_js', plugins_url('/assets/scripts/jquery.smint.js', __FILE__), '', '', true); //?
                }

            }

            if ( ! function_exists('cs_enqueue_count_nos') ) {

                function cs_enqueue_count_nos() {
                    wp_enqueue_script('cs_countTo_js', plugins_url('/assets/scripts/jquery.countTo.js'), '', '', true);
                    wp_enqueue_script('cs_inview_js', plugins_url('/assets/scripts/jquery.inview.min.js'), '', '', true);
                }

            }
            /**
             *
             * @Add this enqueue Script
             */
            if ( ! function_exists('cs_addthis_script_init_method') ) {

                function cs_addthis_script_init_method() {
                    wp_enqueue_script('cs_addthis_js', cs_server_protocol() . 's7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4412d954dccc64', '', '', true);
                }

            }
            /**
             *
             * @social login script
             */
            if ( ! function_exists('cs_socialconnect_scripts') ) {

                function cs_socialconnect_scripts() {
                    wp_enqueue_script('cs_socialconnect_js', plugins_url('/templates/elements/login/cs-social-login/media/js/cs-connect.js', __FILE__), '', '', true);
                }

            }

            /**
             *
             * @google auto complete script
             */
            if ( ! function_exists('cs_google_autocomplete_scripts') ) {

                function cs_google_autocomplete_scripts() {
                    wp_enqueue_script('cs_location_autocomplete_js', plugins_url('/assets/scripts/jquery.location-autocomplete.js', __FILE__), '', '');
                }

            }
            if ( is_admin() ) {
                // admin css files
                wp_enqueue_style('cs_admin_styles_css', plugins_url('/admin/assets/css/admin_style.css', __FILE__));
                wp_enqueue_style('cs_datatable_css', plugins_url('/admin/assets/css/datatable.css', __FILE__));
                wp_enqueue_style('cs_fonticonpicker_css', plugins_url('/assets/icomoon/css/jquery.fonticonpicker.min.css', __FILE__));
                wp_enqueue_style('cs_iconmoon_css', plugins_url('/assets/icomoon/css/iconmoon.css', __FILE__));
                wp_enqueue_style('cs_fonticonpicker_bootstrap_css', plugins_url('/assets/icomoon/theme/bootstrap-theme/jquery.fonticonpicker.bootstrap.css', __FILE__));
                wp_enqueue_style('cs_bootstrap_css', plugins_url('/admin/assets/css/bootstrap.css', __FILE__));
                // admin js files
                wp_enqueue_script('cs_datatable_js', plugins_url('/admin/assets/scripts/datatable.js', __FILE__), '', '', true);
                wp_enqueue_script('cs_chosen_jquery_js', plugins_url('/assets/scripts/chosen.jquery.js', __FILE__));
                wp_enqueue_script('cs_custom_wp_admin_script_js', plugins_url('/admin/assets/scripts/cs_functions.js', __FILE__), '', '', true);
                $cs_funcs_array = array(
                    'select_file' => esc_html__('Select File', 'jobhunt'),
                    'add_file' => esc_html__('Add File', 'jobhunt'),
                    'btn_bg_color' => esc_html__('Button bg Color', 'jobhunt'),
                    'txt_color' => esc_html__('Text Color', 'jobhunt'),
                    'icon_bg_color' => esc_html__('Icon Background Color', 'jobhunt'),
                    'border_color' => esc_html__('Border Color', 'jobhunt'),
                    'are_you_sure' => esc_html__('Are you sure to do this?', 'jobhunt'),
                    'cancel' => esc_html__('Cancel', 'jobhunt'),
                    'delete' => esc_html__('Delete', 'jobhunt'),
                    'pricing_feature' => esc_html__('Pricing Feature', 'jobhunt'),
                    'gmail_user_and_password' => esc_html__('You must provide your Gmail Email address as SMTP username and Gmail Password as SMTP Password.', 'jobhunt'),
                );
                wp_localize_script('cs_custom_wp_admin_script_js', 'cs_funcs_vars', $cs_funcs_array);
                wp_enqueue_script('cs_jobhunt_shortcodes_js', plugins_url('/admin/assets/scripts/shortcode_functions.js', __FILE__), '', '', true);
                wp_enqueue_script('fonticonpicker_js', plugins_url('/assets/icomoon/js/jquery.fonticonpicker.min.js', __FILE__));
                cs_datetime_picker_scripts();
            }
            // get user inline style
            $this->cs_custom_inline_styles_method();
        }

        public static function cs_enqueue_tabs_script() {
            
        }

        /**
         *
         * @Responsive Tabs Styles and Scripts
         */
        public static function cs_enqueue_responsive_front_scripts() {


            $my_theme = wp_get_theme('JobCareer');
            if ( ! $my_theme->exists() ) {
                if ( is_rtl() ) {
                    wp_enqueue_style('jobcareer_rtl_css', plugins_url('/assets/css/rtl.css', __FILE__));
                }
                wp_enqueue_style('jobcareer_responsive_css', plugins_url('/assets/css/responsive.css', __FILE__));
            }
        }

        /**
         *
         * @Data Table Style Scripts
         */

        /**
         * Start Function how to Add table Style Script
         */
        public static function cs_data_table_style_script() {
            wp_enqueue_script('cs_jquery_data_table_js', plugins_url('/assets/scripts/jquery.data_tables.js', __FILE__), '', '', true);
            wp_enqueue_style('cs_data_table_css', plugins_url('/assets/css/jquery.data_tables.css', __FILE__));
        }

        /**
         * End Function how to Add Tablit Style Script
         */
        public static function cs_jquery_ui_scripts() {
            
        }

        /**
         * Start Function how to Add Location Picker Scripts
         */
        public function cs_location_gmap_script() {
            wp_enqueue_script('cs_jquery_latlon_picker_js', plugins_url('/assets/scripts/jquery_latlon_picker.js', __FILE__), '', '', true);
        }

        /**
         * End Function how to Add Location Picker Scripts
         */

        /**
         * Start Function how to Add Google Place Scripts
         */
        public function cs_google_place_scripts() {
            global $cs_plugin_options;
            $google_api_key = '?libraries=places';
            if ( isset($cs_plugin_options['cs_google_api_key']) && $cs_plugin_options['cs_google_api_key'] != '' ) {
                $google_api_key = '?key=' . $cs_plugin_options['cs_google_api_key'] . '&libraries=places';
            }
            wp_enqueue_script('cs_google_autocomplete_script', 'https://maps.googleapis.com/maps/api/js' . $google_api_key);
        }

        // start function for google map files 
        public static function cs_googlemapcluster_scripts() {
            wp_enqueue_script('jquery-googlemapcluster', plugins_url('/assets/scripts/markerclusterer.js', __FILE__), '', '', true);
            wp_enqueue_script('cs_map_info_js', plugins_url('/assets/scripts/map_infobox.js', __FILE__), '', '', true);
        }

        /**
         * End Function how to Add Google Place Scripts
         */

        /**
         * Start Function how to Add Google Autocomplete Scripts
         */
        public function cs_autocomplete_scripts() {
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('jquery-ui-slider');
        }

        /**
         * End Function how to Add Google Autocomplete Scripts
         */
        // Start function for global code
        public function cs_all_scodes() {
            global $cs_jh_scodes;
        }

        // Start function for auto login user
        public function cs_auto_login_user() {
            
        }

        public static $email_template_type = 'general';
        public static $email_default_template = 'Hello! I am general email template by [COMPANY_NAME].';
        public static $email_template_variables = array(
            array(
                'tag' => 'SITE_NAME',
                'display_text' => 'Site Name',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_site_name' ),
            ),
            array(
                'tag' => 'ADMIN_EMAIL',
                'display_text' => 'Admin Email',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_admin_email' ),
            ),
            array(
                'tag' => 'SITE_URL',
                'display_text' => 'SITE URL',
                'value_callback' => array( 'wp_jobhunt', 'cs_get_site_url' ),
            ),
        );

        public function email_template_settings_callback($email_template_options) {
            $email_template_options['types'][] = self::$email_template_type;
            $email_template_options['templates']['general'] = self::$email_default_template;
            $email_template_options['variables']['General'] = self::$email_template_variables;

            return $email_template_options;
        }

        public static function cs_get_site_name() {
            return get_bloginfo('name');
        }

        public static function cs_get_admin_email() {
            return get_bloginfo('admin_email');
        }

        public static function cs_get_site_url() {
            return get_bloginfo('url');
        }

        public static function cs_replace_tags($template, $variables) {
            // Add general variables to the list
            $variables = array_merge(self::$email_template_variables, $variables);
            foreach ( $variables as $key => $variable ) {
                $callback_exists = false;

                // Check if function/method exists.
                if ( is_array($variable['value_callback']) ) { // If it is a method of a class.
                    $callback_exists = method_exists($variable['value_callback'][0], $variable['value_callback'][1]);
                } else { // If it is a function.
                    $callback_exists = function_exists($variable['value_callback']);
                }

                // Substitute values in place of tags if callback exists.
                if ( true == $callback_exists ) {
                    // Make a call to callback to get value.
                    $value = call_user_func($variable['value_callback']);

                    // If we have some value to substitute then use that.
                    if ( false != $value ) {
                        $template = str_replace('[' . $variable['tag'] . ']', $value, $template);
                    }
                }
            }
            return $template;
        }

        public static function get_template($email_template_index, $email_template_variables, $email_default_template) {
            global $cs_plugin_options;
            $email_template = '';
            $jh_from = isset($cs_plugin_options['cs_smtp_sender_email']) ? $cs_plugin_options['cs_smtp_sender_email'] : '';
            $template_data = array( 'subject' => '', 'from' => '', 'recipients' => '', 'email_notification' => '', 'email_type' => '', 'email_template' => '' );
            // Check if there is a template select else go with default template.
            $selected_template_id = jobhunt_check_if_template_exists($email_template_index, 'jh-templates');
            if ( false != $selected_template_id ) {

                // Check if a temlate selected else default template is used.
                if ( $selected_template_id != 0 ) {
                    $templateObj = get_post($selected_template_id);
                    if ( $templateObj != null ) {
                        $email_template = $templateObj->post_content;
                        $template_id = $templateObj->ID;
                        //$jh_from = get_post_meta( $template_id, 'jh_from', true );
                        $template_data['subject'] = wp_jobhunt::cs_replace_tags(get_post_meta($template_id, 'jh_subject', true), $email_template_variables);
                        $template_data['from'] = wp_jobhunt::cs_replace_tags($jh_from, $email_template_variables);
                        $template_data['recipients'] = wp_jobhunt::cs_replace_tags(get_post_meta($template_id, 'jh_recipients', true), $email_template_variables);
                        $template_data['email_notification'] = get_post_meta($template_id, 'jh_email_notification', true);
                        $template_data['email_type'] = get_post_meta($template_id, 'jh_email_type', true);
                    }
                } else {
                    // Get default template.
                    $email_template = $email_default_template;
                    $template_data['email_notification'] = 1;
                }
            } else {
                $email_template = $email_default_template;
                $template_data['email_notification'] = 1;
            }
            $email_template = wp_jobhunt::cs_replace_tags($email_template, $email_template_variables);
            $template_data['email_template'] = $email_template;
            return $template_data;
        }

        public static function is_demo_user_modification_allowed() {
            global $cs_plugin_options, $post;

            if ( 'page_candidate.php' === cs_get_current_template() || 'page_employer.php' === cs_get_current_template() ) {
                $cs_demo_user_login_switch = isset($cs_plugin_options['cs_demo_user_login_switch']) ? $cs_plugin_options['cs_demo_user_login_switch'] : '';
                if ( $cs_demo_user_login_switch == 'on' ) {
                    $cs_job_demo_user_employer = isset($cs_plugin_options['cs_job_demo_user_employer']) ? $cs_plugin_options['cs_job_demo_user_employer'] : '';
                    $cs_job_demo_user_candidate = isset($cs_plugin_options['cs_job_demo_user_candidate']) ? $cs_plugin_options['cs_job_demo_user_candidate'] : '';
                    $current_user_id = get_current_user_id();
                    if ( $cs_job_demo_user_employer == $current_user_id || $cs_job_demo_user_candidate == $current_user_id ) {
                        if ( isset($cs_plugin_options['cs_demo_user_modification_allowed_switch']) && $cs_plugin_options['cs_demo_user_modification_allowed_switch'] != 'on' ) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

    }

}
/*
  Default Sidebars On/OFF Check
 */
if ( ! function_exists('callback_function') ) {
    add_action('wp_loaded', 'callback_function');

    function callback_function() {
        if ( ! is_admin() ) {
            $cs_plugin_options = get_option('cs_plugin_options');
            $cs_default_sidebar_option = isset($cs_plugin_options['cs_default-sidebars']) ? $cs_plugin_options['cs_default-sidebars'] : '';
            if ( $cs_default_sidebar_option == 'on' ) {
                global $wp_registered_sidebars;
                foreach ( $wp_registered_sidebars as $sidebar_id ) {
                    $cs_unregister_id = isset($sidebar_id['id']) ? $sidebar_id['id'] : '';
                    if ( $cs_unregister_id != '' ) {
                        unregister_sidebar($sidebar_id['id']);
                    }
                }
            }
        }
    }

}

if ( ! function_exists('callback_confirm_div') ) {
    add_action('wp_footer', 'callback_confirm_div');

    function callback_confirm_div() {
        if ( is_user_logged_in() ) {
            ?>
            <div id="id_confrmdiv" style="display:none;">
                <div class="cs-confirm-container">
                    <i class="icon-exclamation2"></i>
                    <div class="message"><?php esc_html_e("Do you really want to delete?", "jobhunt"); ?></div>
                    <a href="javascript:void(0);" id="id_truebtn"><?php esc_html_e("Yes Delete It", "jobhunt"); ?></a>
                    <a href="javascript:void(0);" id="id_falsebtn"><?php esc_html_e("Cancel", "jobhunt"); ?></a>
                </div>
            </div>
            <?php
        }
    }

}

function cs_get_current_template($echo = false) {
    if ( ! isset($GLOBALS['current_theme_template']) )
        return false;
    if ( $echo )
        echo $GLOBALS['current_theme_template'];
    else
        return $GLOBALS['current_theme_template'];
}

add_filter('template_include', 'cs_template_include', 1000);

function cs_template_include($t) {
    $GLOBALS['current_theme_template'] = basename($t);
    return $t;
}

/*
 * Check if an email template exists
 */
if ( ! function_exists('jobhunt_check_if_template_exists') ) {

    function jobhunt_check_if_template_exists($slug, $type) {
        global $wpdb;
        $post = $wpdb->get_row("SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' && post_type = '" . $type . "'", 'ARRAY_A');
        if ( isset($post) && isset($post['ID']) ) {
            return $post['ID'];
        } else {
            return false;
        }
    }

}

/**
 *
 * @Create Object of class To Activate Plugin
 */
if ( class_exists('wp_jobhunt') ) {
    $cs_jobhunt = new wp_jobhunt();
    register_activation_hook(__FILE__, array( 'wp_jobhunt', 'activate' ));
    register_deactivation_hook(__FILE__, array( 'wp_jobhunt', 'deactivate' ));
}

//Remove Sub Menu add new job
function modify_menu() {
    global $submenu;
    unset($submenu['edit.php?post_type=jobs'][10]);
}

add_action('admin_menu', 'modify_menu');



if ( ! function_exists('jobcareer_get_currency') ) {

    /**
     * Return an input variable from $_SERVER if exists else default.
     *
     * @param	string $name name of the variable.
     * @param string $default default value.
     * @return string
     */
    function jobcareer_get_currency($price = '', $currency_symbol = false, $before_currency = '', $after_currency = '', $force_currency_sign = '', $force_currency_position = '') {
        global $cs_plugin_options;
        $price_str = '';
        $default_currency = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';
        $currency_pos = isset($cs_plugin_options['cs_currency_position']) ? $cs_plugin_options['cs_currency_position'] : 'left';


        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($cs_plugin_options['cs_use_woocommerce_gateway']) ? $cs_plugin_options['cs_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $default_currency = get_woocommerce_currency_symbol();
                $currency_pos = get_option('woocommerce_currency_pos');
            }
        }

        if ( $force_currency_sign != '' ) {
            $default_currency = $force_currency_sign;
        }
        if ( $force_currency_position != '' ) {
            $currency_pos = $force_currency_position;
        }

        $price = CS_FUNCTIONS()->cs_num_format($price);
        if ( $currency_symbol == true && is_numeric($price) ) {
            $currency_sign = $before_currency . $default_currency . $after_currency;
            $price_str = $currency_sign . $price;
            switch ( $currency_pos ) {
                case 'left' :
                    $price_str = $currency_sign . $price;
                    break;
                case 'right' :
                    $price_str = $price . $currency_sign;
                    break;
                case 'left_space' :
                    $price_str = $currency_sign . ' ' . $price;
                    break;
                case 'right_space' :
                    $price_str = $price . ' ' . $currency_sign;
                    break;
            }
        } else {
            $price_str = $price;
        }
        return $price_str;
    }

}

if ( ! function_exists('jobcareer_get_currency_sign') ) {

    /**
     *
     * @return string for currency sign
     */
    function jobcareer_get_currency_sign() {
        global $cs_plugin_options;
        $default_currency = isset($cs_plugin_options['cs_currency_sign']) ? $cs_plugin_options['cs_currency_sign'] : '$';

        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($cs_plugin_options['cs_use_woocommerce_gateway']) ? $cs_plugin_options['cs_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $default_currency = get_woocommerce_currency_symbol();
            }
        }
        return $default_currency;
    }

}

if ( ! function_exists('jobcareer_get_currency_position') ) {

    /**
     *
     * @return position for currency sign
     */
    function jobcareer_get_currency_position() {
        global $cs_plugin_options;
        $currency_position = isset($cs_plugin_options['cs_currency_position']) ? $cs_plugin_options['cs_currency_position'] : 'left';

        if ( class_exists('WooCommerce') ) {
            $woocommerce_enabled = isset($cs_plugin_options['cs_use_woocommerce_gateway']) ? $cs_plugin_options['cs_use_woocommerce_gateway'] : '';
            if ( $woocommerce_enabled == 'on' ) {
                $currency_position = get_option('woocommerce_currency_pos');
            }
        }
        return $currency_position;
    }

}