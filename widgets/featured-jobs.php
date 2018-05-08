<?php
/**
 * Featured jobs widget Class
 *
 *
 */
if ( ! class_exists('featured_jobs') ) {

    class featured_jobs extends WP_Widget {

        /**
         * @init Recent posts Module
         *
         *
         */
        public function __construct() {
            parent::__construct(
                    'featured_jobs', // Base ID
                    esc_html__('CS : Featured Jobs', 'jobhunt'), // Name
                    array( 'classname' => 'widget-featured-jobs', 'description' => esc_html__('Featured Jobs.', 'jobhunt'), ) // Args
            );
        }

        /**
         * @Recent posts html form
         *
         *
         */
        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields;
            $instance = wp_parse_args((array) $instance, array( 'title' => '' ));
            $title = $instance['title'];
            $description = isset($instance['description']) ? esc_attr($instance['description']) : '';
            $featured_jobs = isset($instance['featured_jobs']) ? $instance['featured_jobs'] : '';
            $featured_job_view = isset($instance['featured_job_view']) ? $instance['featured_job_view'] : '';
            $count_jobs = isset($instance['count_jobs']) ? $instance['count_jobs'] : '';



            $cs_opt_array = array(
                'name' => esc_html__('Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($title),
                    'id' => cs_allow_special_char($this->get_field_id('title')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('title')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);


            $cs_opt_array = array(
                'name' => esc_html__('Description', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => cs_allow_special_char($description),
                    'id' => cs_allow_special_char($this->get_field_id('description')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('description')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('description')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_textarea_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('View ', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $featured_job_view,
                    'cust_name' => $this->get_field_name('featured_job_view'),
                    'cust_id' => $this->get_field_id('featured_job_view'),
                    'classes' => 'chosen-select',
                    'options' => array(
                        'fancy' => esc_html__('Fancy ', 'jobhunt'),
                        'modern' => esc_html__('Modern ', 'jobhunt'),
                    ),
                    'return' => true,
                ),
            );

            $cs_html_fields->cs_select_field($cs_opt_array);

            $mypost = array( 'posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
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
            $loop_count = new WP_Query($mypost);
            if ( $loop_count->have_posts() ) {
                while ( $loop_count->have_posts() ): $loop_count->the_post();
                    $jobs[get_the_ID()] = get_the_title(get_the_ID());
                endwhile;
            }else {
                $jobs = array();
            }
            wp_reset_postdata();
            $jobs_total = count($jobs);
            $cs_opt_array = array(
                'name' => esc_html__('Featured Jobs:', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'multi' => true,
                'field_params' => array(
                    'std' => $featured_jobs,
                    'cust_name' => $this->get_field_name('featured_jobs') . '[]',
                    'cust_id' => $this->get_field_id('featured_jobs'),
                    'id' => '',
                    'classes' => 'featured-jobs',
                    'options' => $jobs,
                    'return' => true,
                ),
            );
            $cs_html_fields->cs_select_field($cs_opt_array);
            $cs_opt_array = array(
                'name' => '',
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => esc_attr($jobs_total),
                    'id' => cs_allow_special_char($this->get_field_id('count_jobs')),
                    'classes' => '',
                    'cust_id' => cs_allow_special_char($this->get_field_name('count_jobs')),
                    'cust_name' => cs_allow_special_char($this->get_field_name('count_jobs')),
                    'cust_type' => 'hidden',
                ),
            );
            $cs_html_fields->cs_text_field($cs_opt_array);
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    chosen_selectionbox();
                });
            </script>
            <?php
        }

        /**
         * @Recent posts update form data
         *
         *
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['description'] = $new_instance['description'];
            $instance['featured_jobs'] = $new_instance['featured_jobs'];
            $instance['featured_job_view'] = $new_instance['featured_job_view'];
            $instance['count_jobs'] = $new_instance['count_jobs'];
            return $instance;
        }

        /**
         * @Display Recent posts widget
         *
         *
         */
        function widget($args, $instance) {
            global $cs_node;
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $description = empty($instance['description']) ? '' : esc_attr($instance['description']);
            $featured_jobs = empty($instance['featured_jobs']) ? '' : $instance['featured_jobs'];
            $featured_job_view = empty($instance['featured_job_view']) ? '' : $instance['featured_job_view'];
            $count_jobs = empty($instance['count_jobs']) ? '' : $instance['count_jobs'];

            if ( ! is_array($featured_jobs) ) {
                $featured_jobs = array( $featured_jobs );
            }
            global $wpdb, $post;
            if ( ( ! empty($title) && $title <> ' ') || (isset($description) && $description <> "") || ! empty($featured_jobs) ) {
                echo '<div class="widget featured-jobs">';
                if ( ( ! empty($title) && $title <> ' ') || (isset($description) && $description <> "") ) {
                    echo '<div class="cs-element-title">';
                    if ( ! empty($title) && $title <> ' ' ) {
                        echo '<h2>' . cs_allow_special_char($title) . '</h2>';
                    }
                    if ( isset($description) && $description <> "" ) {
                        echo '<p>' . htmlspecialchars_decode($description) . '</p>';
                    }
                    echo '</div>';
                }
                ?>    

                <?php
                if ( ! empty($featured_jobs) ) {
                    $cs_job_username = "";

                    $args = array( 'post__in' => $featured_jobs, 'post_type' => 'jobs' );
                    $title_limit = 3;
                    $custom_query = new WP_Query($args);

                    if ( $custom_query->have_posts() <> "" ) {
                        ?>
                        <div class="row">
                            <?php if ( $featured_job_view == 'modern' ) { ?>
                                <?php
                                while ( $custom_query->have_posts() ) : $custom_query->the_post();
                                    $cs_post_id = get_the_ID();
                                    global $cs_plugin_options;
                                    $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                                    $cs_post_loc_address = get_post_meta($post->ID, "cs_post_loc_address", true);
                                    $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                                    $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);
                                    $cs_post_loc_city = get_post_meta($post->ID, 'cs_post_loc_city', true);
                                    $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true);
                                    $cs_jobs_address = get_user_address_string_for_list($cs_post_id);
                                    $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                                    $employer_name = '';
                                    if ( isset($cs_job_employer_data) ) {
                                        foreach ( $cs_job_employer_data as $cs_job_employer_single ) {
                                            //$cs_jobs_address = get_user_address_string_for_list($cs_job_employer_single->ID);
                                            $employer_name = $cs_job_employer_single->post_title;
                                            $employer_name = ', ' . esc_html__('by', 'jobhunt') . ' <a class="cs-color" href="' . esc_url(get_permalink($cs_job_employer_single->ID)) . '">' . $employer_name . '</a>';
                                        }
                                    }
                                    // get all job types
                                    $specialisms_values = '';
                                    $all_specialisms = get_the_terms($post->ID, 'specialisms');
                                    if ( ! empty($all_specialisms) && is_array($all_specialisms) ) {
                                        $specialisms_values .= '<div class="cs-catgories">' . "\n";
                                        $specialisms_values .= '<ul>' . "\n";
                                        foreach ( $all_specialisms as $specialismsitem ) {
                                            $cs_term_link = ' href="javascript:void(0);"';
                                            if ( $cs_search_result_page != '' ) {
                                                $cs_term_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?specialisms=' . $specialismsitem->slug) . '"';
                                            }
                                            $specialisms_values .= '<li><a class="cs-color" ' . $cs_term_link . '>' . esc_html($specialismsitem->name) . '</a></li>' . "\n";
                                        }
                                        $specialisms_values .= '</ul>' . "\n";
                                        $specialisms_values .= '</div>';
                                    }

                                    // job emplyer image
                                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                    if ( $employer_img != '' ) {
                                        $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_2');
                                    }

                                    // job types
                                    $all_job_type = get_the_terms($post->ID, 'job_type');
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
                                            //$job_type_class .= get_term_link($t_id_main);	
                                            $cs_link = ' href="javascript:void(0);"';
                                            if ( $cs_search_result_page != '' ) {
                                                $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                                            }
                                            $job_type_values .= '<a ' . force_balance_tags($cs_link) . '  style="border-color:' . $job_type_color . ';color:' . $job_type_color . ';">' . $job_type->name . '</a>';

                                            if ( $job_type_flag != count($all_specialisms) ) {
                                                $job_type_values .= " ";
                                                $job_type_class .= " ";
                                            }
                                            $job_type_flag ++;
                                        }
                                    }


                                    $first_date = strtotime(date('Y-m-d'));
                                    $second_date = strtotime(date('Y-m-d', $cs_job_expired));
                                    $days_diff = $second_date - $first_date;
                                    //echo date('d',$days_diff);


                                    $date1 = date_create(date('Y-m-d'));
                                    $date2 = date_create(date('Y-m-d', $cs_job_expired));
                                    $diff = date_diff($date1, $date2);
                                    $left_days = $diff->format("%a");
                                    $left_days = $left_days . ' ' . esc_html__('Days left', 'jobhunt');
                                    if ( $left_days == 0 || $left_days == 1 ) {
                                        $left_days = $left_days . ' ' . esc_html__('Day left', 'jobhunt');
                                    }
                                    ?>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="cs-job-featured">
                                            <?php if ( $cs_jobs_thumb_url != '' ) { ?>
                                                <div class="cs-media">
                                                    <figure>
                                                        <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                                    </figure>
                                                </div>
                                            <?php } ?>
                                            <div class="cs-text">
                                                <strong><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php the_title(); ?></a></strong>
                                                <p><?php echo $description = wp_trim_words(get_the_content($cs_post_id), 15); ?></p>
                                                <?php if ( isset($cs_post_loc_city) && $cs_post_loc_city <> '' ) { ?>
                                                    <address><i class="icon-location2"></i><?php echo esc_html($cs_post_loc_city); ?></address>
                                                <?php } ?>
                                                <div class="cs-time">
                                                    <i class="icon-clock3"></i>
                                                    <span><a href="<?php echo esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))); ?>"><?php echo get_the_date('j M Y'); ?></a></span>
                                                </div>
                                            </div>
                                            <div class="cs-job-accounts">
                                                <span><?php
                                                    echo esc_html__('Vacancies + ', 'jobhunt');
                                                    echo intval($count_jobs);
                                                    ?></span>

                                                <?php
                                                /*
                                                 * Apply now functionality
                                                 */
                                                $user = cs_get_user_id();
                                                $class_apply = '';
                                                if ( isset($_SESSION['apply_job_id']) ) {
                                                    $class_apply = 'applyauto';
                                                    unset($_SESSION['apply_job_id']);
                                                }
                                                if ( is_user_logged_in() ) {

                                                    $user = cs_get_user_id();
                                                    $user_role = cs_get_loginuser_role();
                                                    if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate' ) {
                                                        $cs_applied_list = array();
                                                        if ( isset($user) and $user <> '' and is_user_logged_in() ) {
                                                            $finded_result_list = cs_find_index_user_meta_list($cs_post_id, 'cs-user-jobs-applied-list', 'post_id', cs_get_user_id());
                                                            if ( is_array($finded_result_list) && ! empty($finded_result_list) ) {
                                                                ?>
                                                                <a href="javascript:void(0);" class="apply-btn" >
                                                                    <?php esc_html_e('Applied', 'jobhunt') ?>
                                                                </a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a class="apply-btn <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_post_id); ?>', this)" >
                                                                    <?php esc_html_e('Apply Now', 'jobhunt') ?>
                                                                </a>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <a class="apply-btn <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_post_id); ?>', this)" > 
                                                                <?php esc_html_e('Apply Now', 'jobhunt') ?>
                                                            </a>	
                                                            <?php
                                                        }
                                                    }
                                                } else {

                                                    $cs_rand_id = rand(34563, 34323990);
                                                    ?>
                                                    <a href="javascript:void(0);" class="apply-btn" onclick="trigger_func('#btn-header-main-login');"> 
                                                        <?php esc_html_e('Apply Now', 'jobhunt') ?></a>
                                                <?php } ?>

                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            <?php } else {
                                ?>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php
                                    while ( $custom_query->have_posts() ) : $custom_query->the_post();
                                        $cs_post_id = get_the_ID();
                                        global $cs_plugin_options;
                                        $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
                                        $cs_post_loc_address = get_post_meta($post->ID, "cs_post_loc_address", true);
                                        $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                                        $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);
                                        $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true);

                                        $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                                        $employer_name = '';
                                        if ( isset($cs_job_employer_data) ) {
                                            foreach ( $cs_job_employer_data as $cs_job_employer_single ) {
                                                $cs_jobs_address = get_user_address_string_for_list($cs_job_employer_single->ID);
                                                $employer_name = $cs_job_employer_single->post_title;
                                                $employer_name = ', by <a class="cs-color" href="' . esc_url(get_permalink($cs_job_employer_single->ID)) . '">' . $employer_name . '</a>';
                                            }
                                        }
                                        // get all job types
                                        $specialisms_values = '';
                                        $all_specialisms = get_the_terms($post->ID, 'specialisms');
                                        if ( ! empty($all_specialisms) && is_array($all_specialisms) ) {
                                            $specialisms_values .= '<div class="cs-catgories">' . "\n";
                                            $specialisms_values .= '<ul>' . "\n";
                                            foreach ( $all_specialisms as $specialismsitem ) {
                                                $cs_term_link = ' href="javascript:void(0);"';
                                                if ( $cs_search_result_page != '' ) {
                                                    $cs_term_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?specialisms=' . $specialismsitem->slug) . '"';
                                                }
                                                $specialisms_values .= '<li><a class="cs-color" ' . $cs_term_link . '>' . esc_html($specialismsitem->name) . '</a></li>' . "\n";
                                            }
                                            $specialisms_values .= '</ul>' . "\n";
                                            $specialisms_values .= '</div>';
                                        }

                                        // job emplyer image
                                        $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                                        if ( $employer_img != '' ) {
                                            $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_2');
                                        }

                                        // job types
                                        $all_job_type = get_the_terms($post->ID, 'job_type');
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
                                                //$job_type_class .= get_term_link($t_id_main);	
                                                $cs_link = ' href="javascript:void(0);"';
                                                if ( $cs_search_result_page != '' ) {
                                                    $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                                                }
                                                $job_type_values .= '<a ' . force_balance_tags($cs_link) . '  style="border-color:' . $job_type_color . ';color:' . $job_type_color . ';">' . $job_type->name . '</a>';

                                                if ( $job_type_flag != count($all_specialisms) ) {
                                                    $job_type_values .= " ";
                                                    $job_type_class .= " ";
                                                }
                                                $job_type_flag ++;
                                            }
                                        }
                                        $first_date = strtotime(date('Y-m-d'));
                                        $second_date = strtotime(date('Y-m-d', $cs_job_expired));
                                        $days_diff = $second_date - $first_date;
                                        //echo date('d',$days_diff);
                                        $date1 = date_create(date('Y-m-d'));
                                        $date2 = date_create(date('Y-m-d', $cs_job_expired));
                                        $diff = date_diff($date1, $date2);
                                        $left_days = $diff->format("%a");
                                        $left_days = $left_days . ' ' . esc_html__('Days left', 'jobhunt');
                                        if ( $left_days == 0 || $left_days == 1 ) {
                                            $left_days = $left_days . ' ' . esc_html__('Day left', 'jobhunt');
                                        }
                                        ?>
                                        <div class="cs-top-featured">
                                            <?php if ( $cs_jobs_thumb_url != '' ) { ?>
                                                <div class="cs-media">
                                                    <figure>
                                                        <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($cs_jobs_thumb_url); ?>" alt=""></a>
                                                    </figure>
                                                </div>
                                            <?php } ?>
                                            <div class="cs-text">
                                                <strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                                                <?php echo $specialisms_values; ?>
                                                <?php if ( $cs_post_loc_address != '' ) { ?>
                                                    <address><i class="icon-map-pin"></i><?php echo esc_html($cs_post_loc_address); ?></address>
                                                <?php } ?>
                                                <?php if ( $job_type_values != '' || $left_days != '' ) { ?>
                                                    <div class="cs-time">
                                                        <?php if ( $job_type_values != '' ) { ?>
                                                            <strong><?php echo $job_type_values; ?></strong>
                                                        <?php } ?>
                                                        <?php if ( $left_days != '' ) { ?>
                                                            <span><?php echo esc_html($left_days); ?></span>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                <p>
                                                    <?php echo wp_trim_words(get_the_content(), 15, '.'); ?>
                                                    <a class="read-btn cs-color" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more'); ?></a>
                                                </p>
                                                <?php
                                                /*
                                                 * Apply now functionality
                                                 */
                                                $user = cs_get_user_id();
												$user_can_apply = 'on';
												$user_can_apply = apply_filters( 'jobhunt_candidate_can_apply', $user_can_apply );
												if( $user_can_apply == 'on' ){
                                                $class_apply = '';
                                                if ( isset($_SESSION['apply_job_id']) ) {
                                                    $class_apply = 'applyauto';
                                                    unset($_SESSION['apply_job_id']);
                                                }
                                                if ( is_user_logged_in() ) {
                                                    $user = cs_get_user_id();
                                                    $user_role = cs_get_loginuser_role();
                                                    if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate' ) {
                                                        $cs_applied_list = array();
                                                        if ( isset($user) and $user <> '' and is_user_logged_in() ) {
                                                            $finded_result_list = cs_find_index_user_meta_list($list_job_id, 'cs-user-jobs-applied-list', 'post_id', cs_get_user_id());
                                                            if ( is_array($finded_result_list) && ! empty($finded_result_list) ) {
                                                                ?>
                                                                <a href="javascript:void(0);" class="apply-btn" >
                                                                    <?php esc_html_e('Applied', 'jobhunt') ?>
                                                                </a>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <a class="apply-btn <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($list_job_id); ?>', this)" >
                                                                    <?php esc_html_e('Apply for this job', 'jobhunt') ?>
                                                                </a>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <a class="apply-btn <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($list_job_id); ?>', this)" > 
                                                                <?php esc_html_e('Apply for this job', 'jobhunt') ?>
                                                            </a>	
                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    $cs_rand_id = rand(34563, 34323990);
                                                    ?>
                                                    <a href="javascript:void(0);" class="apply-btn" onclick="trigger_func('#btn-header-main-login');"> 
                                                        <?php esc_html_e('Apply Now', 'jobhunt') ?></a>
                                                <?php } 
												}?>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                    wp_reset_postdata();
                                    ?>
                                </div>   
                            <?php }
                            ?>
                        </div>
                        <?php
                    }
                }
                echo '<div>';
            }
        }

    }

}
add_action('widgets_init', create_function('', 'return register_widget("featured_jobs");'));
