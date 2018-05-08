<?php
/**
 * File Type: Listing Tab Helper Function
 */
if ( ! class_exists('Listing_Tab_Helper_Function') ) {

    class Listing_Tab_Helper_Function {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('jobhunt_listing_tabs_element_jobs_content', array( $this, 'jobhunt_listing_tabs_element_jobs_content_callback' ), 10, 1);
            add_action('jobhunt_listing_tabs_element_resumes_content', array( $this, 'jobhunt_listing_tabs_element_resumes_content_callback' ), 10, 1);
            add_action('jobhunt_listing_tabs_element_companies_content', array( $this, 'jobhunt_listing_tabs_element_companies_content_callback' ), 10, 1);
        }

        public function jobhunt_listing_tabs_element_companies_content_callback($post_per_tab = '') {

            global $wpdb, $cs_plugin_options;
            $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';

            $date_formate = 'd-m-Y H:i:s';
            $user_allow_in_search_query = array();
            if ( isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch == 'on' ) {
                $user_allow_in_search_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'cs_allow_search',
                        'value' => 'yes',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_allow_search',
                        'value' => '',
                        'compare' => '=',
                    ),
                );
            }
            $emp_args = array(
                'number' => $post_per_tab,
                'role' => 'cs_employer',
                'offset' => 0,
                'order' => 'ASC',
                'orderby' => 'display_name',
                'fields' => array( 'ID', 'display_name' ),
                'meta_query' => array(
                    array(
                        'key' => 'cs_user_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_user_last_activity_date',
                        'value' => strtotime(current_time($date_formate)),
                        'compare' => '<=',
                    ),
                    $user_allow_in_search_query,
                )
            );

//            echo '<pre>';
//            print_r($emp_args);
//            echo '</pre>';

            $emp_query = new WP_User_Query($emp_args);
            $count_post = $emp_query->total_users;

            // getting if record not found
            if ( $count_post > 0 ) {
                // getting job with page number
                $loop = new WP_User_Query($emp_args);
                $flag = 1;
                if ( ! empty($loop->results) ) {
                    foreach ( $loop->results as $cs_user ) {
                        $cs_employee_address = get_user_address_string_for_list($cs_user->ID, 'usermeta');
                        $cs_employee_emp_username = $cs_user->ID;
                        $cs_employee_comp_name = $cs_user->display_name;
                        $cs_employee_employer_img = get_user_meta($cs_user->ID, 'user_img', true);
                        $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
                        if ( ! cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "" ) {
                            $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                        }
                        $emp_jobpost = array( 'posts_per_page' => "1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                            'meta_query' => array(
                                array(
                                    'key' => 'cs_job_username',
                                    'value' => $cs_employee_emp_username,
                                    'compare' => '=',
                                ),
                            )
                        );
                        $loop_job_count = new WP_Query($emp_jobpost);
                        $count_job_post = $loop_job_count->found_posts;
                        global $post;
                        $all_specialisms = get_user_meta($cs_user->ID, 'cs_specialisms', true);
                        $specialisms_values = '';
                        $specialisms_class = '';
                        $specialism_html = '';
                        $specialism_flag = 1;
                        $current_page_url = get_permalink($post->ID);
                        if ( $all_specialisms != '' ) {
                            foreach ( $all_specialisms as $specialisms_item ) {
                                $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                                if ( is_object($specialismsitem) ) {
                                    if ( $specialism_flag == 1 ) {
                                        $specialism_html .= '<span class="cs-single-specialism" >' . $specialismsitem->name . '</span>';
                                    } else {
                                        $specialism_html .= '<span class="cs-single-specialism" >' . $specialismsitem->name . '</span>';
                                    }
                                    $specialisms_values .= $specialismsitem->name;
                                    $specialisms_class .= $specialismsitem->slug;
                                    if ( $specialism_flag != count($all_specialisms) ) {
                                        $specialisms_values .= ", ";
                                        $specialisms_class .= " ";
                                    }
                                    $specialism_flag ++;
                                }
                            }
                        }
                        ?>
                        <li>
                            <div class="cs-media">
                                <figure>
                                    <a href="<?php echo get_author_posts_url($cs_user->ID) ?>"><img src="<?php echo esc_url($cs_employee_employer_img); ?>" alt="image"></a>
                                </figure>
                            </div>
                            <div class="cs-text">
                                <div class="cs-post-title">
                                    <h3><a href="<?php echo get_author_posts_url($cs_user->ID) ?>"><?php echo esc_html($cs_employee_comp_name) ?></a></h3>
                                    <?php if ( $cs_employee_address <> '' ) { ?>
                                        <a href="#" class="cs-color">@ <?php echo esc_html($cs_employee_address) ?></a>
                                        <?php
                                    }
                                    $featured_employer = apply_filters('jobhunt_make_featured_tag', '', $cs_user->ID);
                                    echo force_balance_tags($featured_employer);
                                    do_action('jobhunt_employer_description', $cs_user->ID);
                                    ?>
                                </div>
                                <?php
                                if ( isset($specialism_html) ) {
                                    ?><div class="cs-specialism"><?php
                                    echo force_balance_tags($specialism_html);
                                    ?></div><?php
                                }
                                ?>
                            </div>
                            <?php
                            $vacancy_text = esc_html__('Vacancy', 'jobhunt');
                            if ( isset($count_job_post) && ! empty($count_job_post) && $count_job_post > 1 ) {
                                $vacancy_text = esc_html__('Vacancies', 'jobhunt');
                            }
                            ?>
                            <div class="cs-post-type">
                                <span class="cs-btn-holder">
                                    <a href="<?php echo get_author_posts_url($cs_user->ID) ?>"><?php esc_html_e('View Company', 'jobhunt'); ?></a>
                                    <a href="javascript:void(0)">
                                        <span class="cs-color"><?php echo esc_html($count_job_post); ?></span> <?php
                                        echo ($vacancy_text);
                                        ?>
                                    </a>
                                </span>
                            </div>
                        </li>
                        <?php
                        $flag ++;
                    }
                }
            } else {
                echo '<li class="ln-no-match">';
                echo '<div class="massage-notfound">
            <div class="massage-title">
             <h6><i class="icon-warning4"></i><strong> ' . esc_html__('Sorry !', 'jobhunt') . '</strong>&nbsp; ' . esc_html__("There are no listings matching your search.", 'jobhunt') . ' </h6>
            </div>
             <ul>
                <li>' . esc_html__("Please re-check the spelling of your keyword", 'jobhunt') . ' </li>
                <li>' . esc_html__("Try broadening your search by using general terms", 'jobhunt') . '</li>
                <li>' . esc_html__("Try adjusting the filters applied by you", 'jobhunt') . '</li>
             </ul>
          </div>';
                echo '</li>';
            }
        }

        public function jobhunt_listing_tabs_element_resumes_content_callback($post_per_tab = '') {

            global $cs_plugin_options;

            $login_user_is_employer_flag = 0;
            $login_user_is_candidate_flag = 0;
            $cs_emp_funs = new cs_employer_functions();
            if ( is_user_logged_in() ) {
                $user_role = cs_get_loginuser_role();
                if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_employer' ) {
                    $login_user_is_employer_flag = 1;
                } else if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate' ) {
                    $login_user_is_candidate_flag = 1;
                }
            }

            $default_currency_sign = '';
            if ( isset($cs_plugin_options['cs_currency_sign']) ) {
                $default_currency_sign = $cs_plugin_options['cs_currency_sign'];
            }




            $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
            $user_allow_in_search_query = array();
            if ( isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch == 'on' ) {
                $user_allow_in_search_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'cs_allow_search',
                        'value' => 'yes',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_allow_search',
                        'value' => '',
                        'compare' => '=',
                    ),
                );
            }
            $cand_args = array(
                'role' => 'cs_candidate',
                'order' => 'ASC',
                'orderby' => 'display_name',
                'user_status' => 1,
                'offset' => 0,
                'number' => $post_per_tab,
                'fields' => array( 'ID', 'display_name' ),
                'meta_query' => array(
                    array(
                        'key' => 'cs_user_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                    $user_allow_in_search_query,
                )
            );

            $cand_query = new WP_User_Query($cand_args);
            $count_cand = $cand_query->total_users;

            // getting if record not found
            if ( $count_cand > 0 ) {
                // getting job with page number
                $loop = new WP_User_Query($cand_args);
                global $cs_plugin_options, $current_user;
                $cs_emp_funs = new cs_employer_functions();
                $cs_candidate_switch = isset($cs_plugin_options['cs_candidate_switch']) ? $cs_plugin_options['cs_candidate_switch'] : '';
                $cs_fav_resumes = get_user_meta($current_user->ID, "cs_fav_resumes", true);

                $flag = 1;
                $counter = 1;
                if ( ! empty($loop->results) ) {
                    foreach ( $loop->results as $cs_user ) {
                        $cs_post_loc_city = urldecode(get_user_meta($cs_user->ID, 'cs_post_loc_city', true));
                        $candidate_img = get_user_meta($cs_user->ID, 'user_img', true);
                        $cs_minimum_salary = get_user_meta($cs_user->ID, 'cs_minimum_salary', true);
                        $cs_user_last_activity_date = get_user_meta($cs_user->ID, 'cs_user_last_activity_date', true);
                        // diffrent size image if exist
                        $candidate_img = cs_get_img_url($candidate_img, 'cs_media_4');
                        if ( ! cs_image_exist($candidate_img) || $candidate_img == "" ) {
                            $candidate_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                        }
                        $cs_candidate_user = $cs_user->ID;
                        $cs_get_exp_list = get_user_meta($cs_user->ID, 'cs_exp_list_array', true);
                        $cs_exp_titles = get_user_meta($cs_user->ID, 'cs_exp_title_array', true);
                        $cs_exp_from_dates = get_user_meta($cs_user->ID, 'cs_exp_from_date_array', true);
                        $cs_exp_to_dates = get_user_meta($cs_user->ID, 'cs_exp_to_date_array', true);
                        $cs_exp_companys = get_user_meta($cs_user->ID, 'cs_exp_company_array', true);
                        // get all job types
                        $all_specialisms = get_user_meta($cs_user->ID, 'cs_specialisms', true);
                        $specialisms_values = '';
                        $specialisms_class = '';
                        $specialism_html = '';
                        $specialism_flag = 1;
                        if ( $all_specialisms != '' ) {
                            foreach ( $all_specialisms as $specialisms_item ) {
                                $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                                if ( is_object($specialismsitem) ) {
                                    if ( $specialism_flag == 1 ) {
                                        $specialism_html .= '<span><a class="cs-color">' . $specialismsitem->name . '</a></span>';
                                    } else {
                                        $specialism_html .= '<span>, <a class="cs-color">' . $specialismsitem->name . '</a></span>';
                                    }
                                    $specialisms_values .= $specialismsitem->name;
                                    $specialisms_class .= $specialismsitem->slug;
                                    if ( $specialism_flag != count($all_specialisms) ) {
                                        $specialisms_values .= ", ";
                                        $specialisms_class .= " ";
                                    }
                                    $specialism_flag ++;
                                }
                            }
                        }
                        $recent_exp_company = '';
                        $recent_exp_titles = '';
                        $recent_exp_company = '';
                        $recent_exp_titles = '';
                        if ( isset($cs_get_exp_list) && is_array($cs_get_exp_list) && count($cs_get_exp_list) > 0 ) {
                            $required_index = find_heighest_date_index($cs_exp_to_dates, 'd-m-Y');
                            $recent_exp_company = isset($cs_exp_companys[$required_index]) ? $cs_exp_companys[$required_index] : '';
                            $recent_exp_title = isset($cs_exp_titles[$required_index]) ? $cs_exp_titles[$required_index] : '';
                        }
                        $last_login_timestring = get_user_last_login($cs_candidate_user);
                        $user_registered_timestamp = isset($cs_candidate_user) && $cs_candidate_user != '' ? get_user_registered_timestamp($cs_candidate_user) : '';
                        // Check is candidate available for view
                        $cs_candidate_view = false;
                        $cs_href = '';
                        if ( $cs_candidate_switch == 'on' ) {
                            if ( is_user_logged_in() && $login_user_is_employer_flag && $cs_emp_funs->cs_check_emp_resume($cs_candidate_user) ) {
                                $cs_candidate_view = true;
                            } else {
                                $cs_candidate_view = false;
                            }
                            // check if this candidate apply a job and owner of that job need to view the profile of this candidate
                            if ( check_candidate_applications($cs_candidate_user) > 0 ) {
                                $cs_candidate_view = true;
                            }
                            // check if you this is your own profile
                            if ( is_user_logged_in() ) {
                                $user_profile_id = get_current_user_id();
                                if ( (isset($user_profile_id)) && $cs_candidate_user == $user_profile_id ) {
                                    $cs_candidate_view = true;
                                }
                            }
                            $cs_candidate_view = apply_filters('jobhunt_view_candidate_all_employer', $cs_candidate_view);
                            if ( $cs_candidate_view == true ) {
                                $cs_href = ' href="' . esc_url(get_author_posts_url($cs_user->ID)) . '"';
                            }
                        } else {
                            $cs_href = ' href="' . esc_url(get_author_posts_url($cs_user->ID)) . '"';
                        }
                        ?>
                        <li>
                            <?php
                            $plugin_action = false;
                            $plugin_action = apply_filters('jobhunt_digitalmarketing_depedency', $plugin_action);
                            if ( ! $plugin_action ) {
                                ?>
                                <div class="cs-media">
                                    <figure>
                                        <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?>>
                                            <img src="<?php echo esc_url($candidate_img) ?>" alt="" /><?php do_action('jobhunt_append_with_title', $cs_user->ID); ?>
                                        </a>
                                    </figure>
                                </div>
                            <?php } ?>
                            <div class="cs-text">
                                <div class="cs-post-title">
                                    <h5><a href="<?php echo esc_url(get_author_posts_url($cs_user->ID)); ?>"><?php echo $cs_user->display_name ?></a> <?php if ( $cs_post_loc_city != '' ) { ?><span class="cs-location cs-color"><?php echo ($cs_post_loc_city) ?></span><?php } ?></h5>
                                </div>
                                <div class="post-option">
                                    <?php if ( $recent_exp_titles != '' && $recent_exp_company != '' ) { ?>
                                        <span class="cs-postion"><em><?php echo esc_html($recent_exp_titles) ?></em> @ <?php echo esc_html($recent_exp_company) ?></span>
                                        <?php
                                    }
                                    if ( $cs_minimum_salary != '' ) {
                                        echo '<span class="cs-min-salary">' . $default_currency_sign . $cs_minimum_salary . esc_html__(' p.a minimum', 'jobhunt') . '</span>';
                                    }
                                    if ( $cs_user_last_activity_date != '' ) {
                                        ?>
                                        <span class="cs-post-date"><?php echo esc_html__('Last Activity', 'jobhunt') . ' ' . cs_time_elapsed_string($cs_user_last_activity_date); ?></span>
                                    <?php } ?>
                                </div>
                                <?php
                                echo '<div class="specialism">' . force_balance_tags($specialism_html) . '</div>';
                                do_action('dairyjobs_cand_list_description', $cs_user->ID);
                                ?>

                            </div>

                            <?php
                            $display_shortlist_button = 'yes';
                            $display_shortlist_button = apply_filters('jobhunt_candidate_lists_shortlist_button', $display_shortlist_button);
                            if ( $display_shortlist_button == 'yes' || $display_shortlist_button == '' ) {
                                ?>
                                <div class="cs-uploaded jobseeker-detail">
                                    <span class="cs-btn-holder">
                                        <?php
                                        $cs_candidate = $cs_candidate_user;
                                        $plugin_active = false;
                                        $plugin_active = apply_filters('jobhunt_dairyjobs_depedency', $plugin_active);
                                        if ( $cs_candidate_switch == 'on' && ! $plugin_active ) {

                                            if ( ! is_user_logged_in() ) {
                                                ?>
                                                <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Add to List", "jobhunt"); ?>" class="ad_to_list add_list_icon cs-button" onclick="trigger_func('#btn-header-main-login');"><?php esc_html_e('Add to List', 'jobhunt') ?></a>
                                                <?php
                                            } else if ( is_user_logged_in() && ! $login_user_is_employer_flag ) {
                                                ?>
                                                <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Add to List", "jobhunt"); ?>" class="ad_to_list add_list_icon cs-button" id="cs_empl_check_<?php echo absint($cs_candidate_user) ?>" data-id="<?php echo absint($cs_candidate_user) ?>"><?php esc_html_e('Add to List', 'jobhunt') ?></a>
                                                <?php
                                            } else if ( is_user_logged_in() && $login_user_is_employer_flag ) {
                                                if ( is_array($cs_fav_resumes) && in_array($cs_candidate, $cs_fav_resumes) ) {
                                                    ?>
                                                    <a <?php echo CS_FUNCTIONS()->cs_special_chars($cs_href) ?> class="add_list_icon ad_to_list cs_resume_added cs-button"><?php esc_html_e('View Detail', 'jobhunt') ?></a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Add to List", "jobhunt"); ?>" class="add_list_icon ad_to_list cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_to_list('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>')"><?php esc_html_e('Add to List', 'jobhunt') ?></a>
                                                    <?php
                                                }
                                            }
                                        }
                                        //else {
                                        // not payment chargable list
                                        if ( ! is_user_logged_in() ) {
                                            ?>
                                            <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" onclick="trigger_func('#btn-header-main-login');"><i class="icon-plus-circle"></i><?php esc_html_e('Shortlist', 'jobhunt') ?></a>
                                            <?php
                                        } else if ( is_user_logged_in() && ! $login_user_is_employer_flag ) {
                                            ?>
                                            <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" id="cs_empl_check_<?php echo absint($cs_candidate_user) ?>" data-id="<?php echo absint($cs_candidate_user) ?>"><i class="icon-plus-circle"></i><?php esc_html_e('Shortlist', 'jobhunt') ?></a>
                                            <?php
                                        } else if ( is_user_logged_in() && $login_user_is_employer_flag ) {
                                            $finded_result_list = cs_find_index_user_meta_list($cs_candidate, 'cs-user-resumes-wishlist', 'post_id', cs_get_user_id());
                                            if ( is_array($finded_result_list) && ! empty($finded_result_list) ) {
                                                ?>
                                                <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Shortlisted", "jobhunt"); ?>" href="javascript:void(0);" class="ad_to_short_list add_list_icon cs_resume_added cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_favr('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>', 'remove')"><i class="icon-minus-circle"></i><?php esc_html_e('Shortlisted', 'jobhunt') ?></a>
                                                <?php
                                            } else {
                                                ?>
                                                <a data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__("Shortlist", "jobhunt"); ?>" class="ad_to_short_list add_list_icon cs-button" id="add-to-btn-<?php echo absint($cs_candidate_user) ?>" onclick="cs_add_favr('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_candidate_user) ?>', 'add')"><i class="icon-plus-circle"></i><?php esc_html_e('Shortlist', 'jobhunt') ?></a>
                                                <?php
                                            }
                                        }
                                        //}
                                        ?>
                                    </span>
                                </div>
                            <?php } ?>
                        </li>
                        <?php
                        $counter ++;
                        $flag ++;
                    }
                }
            } else {
                echo '<li class="ln-no-match">';
                echo '<div class="massage-notfound">
            <div class="massage-title">
             <h6><i class="icon-warning4"></i><strong> ' . esc_html__('Sorry !', 'jobhunt') . '</strong>&nbsp; ' . esc_html__("There are no listings matching your search.", 'jobhunt') . ' </h6>
            </div>
             <ul>
                <li>' . esc_html__("Please re-check the spelling of your keyword", 'jobhunt') . ' </li>
                <li>' . esc_html__("Try broadening your search by using general terms", 'jobhunt') . '</li>
                <li>' . esc_html__("Try adjusting the filters applied by you", 'jobhunt') . '</li>
             </ul>
          </div>';
                echo '</li>';
            }
        }

        public function jobhunt_listing_tabs_element_jobs_content_callback($post_per_tab = '') {
            global $cs_plugin_options;

            if ( empty($post_per_tab) ) {
                $post_per_tab = 10;
            }
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
            $current_timestamp = current_time('timestamp');
            $args_listing_tab_jobs = array(
                'posts_per_page' => $post_per_tab,
                'post_type' => 'Jobs',
                'post_status' => 'Publish',
                'fields' => 'ids',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_posted',
                        'value' => $current_timestamp,
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'cs_job_expired',
                        'value' => $current_timestamp,
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'cs_job_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                ),
            );

            $args_listing_tab_jobs_query = new WP_Query($args_listing_tab_jobs);
            if ( $args_listing_tab_jobs_query->have_posts() ) :
                while ( $args_listing_tab_jobs_query->have_posts() ) : $args_listing_tab_jobs_query->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_job_featured = get_post_meta($cs_job_id, 'cs_job_featured', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);

                    if ( $employer_img != '' ) {
                        $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                    }

                    $cs_jobs_thumb_url = apply_filters('digitalmarketing_job_image', $cs_jobs_thumb_url, $cs_job_id);

                    if ( ! cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "" ) {
                        $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    }
                    $cs_jobs_feature_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-feature.png');
                    $cs_job_featured = get_post_meta($cs_job_id, 'cs_job_featured', true);
                    // get all job types
                    $all_specialisms = get_the_terms($cs_job_id, 'specialisms');
                    $specialisms_values = '';
                    $specialisms_class = '';
                    $specialism_flag = 1;
                    if ( $all_specialisms != '' ) {
                        foreach ( $all_specialisms as $specialismsitem ) {
                            $specialisms_values .= $specialismsitem->name;
                            $specialisms_class .= $specialismsitem->slug;
                            if ( $specialism_flag != count($all_specialisms) ) {
                                $specialisms_values .= ", ";
                                $specialisms_class .= " ";
                            }
                            $specialism_flag ++;
                        }
                    }

                    $all_job_type = get_the_terms($cs_job_id, 'job_type');
                    $job_type_values = '';
                    $job_type_class = '';
                    $job_type_flag = 1;
                    if ( $all_job_type != '' ) {
                        foreach ( $all_job_type as $job_type ) {

                            $t_id_main = $job_type->term_id;
                            $job_type_color_arr = get_option("job_type_color_$t_id_main");
                            $job_type_color = '';
                            if ( isset($job_type_color_arr['text']) ) {
                                $job_type_color = $job_type_color_arr['text'];
                            }
                            $cs_link = ' href="javascript:void(0);"';
                            if ( $cs_search_result_page != '' ) {
                                $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                            }
                            $job_type_values .= '<span class="jobs-type"><a ' . force_balance_tags($cs_link) . ' style="color:' . $job_type_color . ';border-color:' . $job_type_color . ' !important;">' . $job_type->name . '</a></span>';
                            if ( $job_type_flag != count($all_specialisms) ) {
                                $job_type_values .= " ";
                                $job_type_class .= " ";
                            }
                            $job_type_flag ++;
                        }
                    }
                    ?>
                    <li>
                        <div class="jobs-content">
                            <?php if ( $cs_jobs_thumb_url <> '' ) { ?>
                                <div class="cs-media">
                                    <figure><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"><img alt="" src="<?php echo esc_url($cs_jobs_thumb_url); ?>"></a></figure>
                                </div>
                            <?php } ?>
                            <div class="cs-text">
                                <div class="cs-post-title">
                                    <strong><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"><?php echo esc_html(get_the_title($cs_job_id)); ?></a></strong>
                                </div>
                                <div class="post-options"> 
                                    <ul>
                                        <?php
                                        if ( isset($cs_jobs_address) && ! empty($cs_jobs_address) ) {
                                            ?>
                                            <li><span class="cs-location"><i class="icon-location6"></i><?php echo esc_html($cs_jobs_address); ?></span></li> 
                                            <?php
                                        }
                                        if ( $cs_job_posted <> '' ) {
                                            ?>
                                            <li><span class="cs-location"><i class="icon-calendar6"></i><?php echo esc_html__('Posted : ', 'jobhunt') . cs_time_elapsed_string($cs_job_posted); ?></span></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="job-option">
                                <?php echo $job_type_values; ?>
                                <a href="<?php get_the_permalink($cs_job_id); ?>" class="joblist-btn"><?php echo esc_html__('View Job', 'jobhunt'); ?></a>
                            </div>
                        </div>
                    </li>
                    <?php
                endwhile;
            endif;

            wp_reset_postdata();
            ?>
            <?php
        }

    }

    new Listing_Tab_Helper_Function();
}