<?php

/**
 * File Type: Job Post Type
 */
if ( ! class_exists('post_type_job') ) {

    class post_type_job {

        /**
         * Start Contructer Function
         */
        public function __construct() {
            add_action('init', array( &$this, 'cs_job_register' ), 0);
            add_filter('manage_jobs_posts_columns', array( &$this, 'cs_job_columns_add' ));
            add_action('manage_jobs_posts_custom_column', array( &$this, 'cs_job_columns' ), 10, 2);
        }

        /**
         * Start Wp's Initilize action hook Function
         */
        public function cs_job_init() {
            // Initialize Post Type
            $this->cs_job_register();
        }

        public function cs_trim_content() {

            global $post;
            $read_more = '....';
            $the_content = get_the_content($post->ID);
            if ( strlen(get_the_content($post->ID)) > 200 ) {
                $the_content = substr(get_the_content($post->ID), 0, 200) . $read_more;
            }

            return $the_content;
        }

        /**
         * Start Function How to Register post type
         */
        public function cs_job_register() {
            $labels = array(
                'name' => esc_html__('Jobs', 'jobhunt'),
                'menu_name' => esc_html__('Jobs', 'jobhunt'),
                'add_new_item' => esc_html__('Add New Job', 'jobhunt'),
                'edit_item' => esc_html__('Edit Job', 'jobhunt'),
                'new_item' => esc_html__('New Job Item', 'jobhunt'),
                'add_new' => esc_html__('Add New Job', 'jobhunt'),
                'view_item' => esc_html__('View Job Item', 'jobhunt'),
                'search_items' => esc_html__('Search', 'jobhunt'),
                'not_found' => esc_html__('Nothing found', 'jobhunt'),
                'not_found_in_trash' => esc_html__('Nothing found in Trash', 'jobhunt'),
                'parent_item_colon' => ''
            );
            $args = array(
                'labels' => $labels,
                'public' => true,
                'exclude_from_search' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'has_archive' => false,
                'query_var' => false,
                'menu_icon' => 'dashicons-admin-post',
                'rewrite' => true,
                'capability_type' => 'post',
                //'hierarchical' => true,
                'menu_position' => null,
                'supports' => array( 'title', 'editor' )
            );

            register_post_type('jobs', $args);
        }

        /**
         * Start Function How to Add Title Columns
         */
        public function cs_job_columns_add($columns) {
			
			$specialisms_label = esc_html__('Specialism', 'jobhunt');
			$specialisms_label = apply_filters( 'jobhunt_replace_specialisms_to_categories', $specialisms_label );

            unset($columns['date']);

            $columns['company'] = esc_html__('Company', 'jobhunt');
            $columns['job_type'] = esc_html__('Job Type', 'jobhunt');
            $columns['specialisms'] = $specialisms_label;
            $columns['posted'] = esc_html__('Posted', 'jobhunt');
            $columns['expired'] = esc_html__('Expired', 'jobhunt');
            $columns['views'] = '<i class="icon-eye7"></i> / <i class="icon-thumbsup"></i> / <i class="icon-users"></i>';
            $columns['status'] = esc_html__('Status', 'jobhunt');
            $columns = apply_filters( 'jobhunt_job_custom_columns', $columns );
            return $columns;
        }

        /**
         * Start Function How to Add  Columns
         */
        public function cs_job_columns($name) {
            global $post, $gateway;

            switch ( $name ) {
                default:
                    break;
                case 'company':
					$cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                    $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                    $employer_title = '';
                    if ( isset($cs_job_employer_data) ) {
                        foreach ( $cs_job_employer_data as $cs_job_employer_single ) {
                            $employer_title = get_the_title($cs_job_employer_single->ID);
                        }
                    }

                    $cs_user = get_userdata($cs_job_employer);
                    if ( isset($cs_user->display_name) ) {
                        echo $cs_user->display_name;
                    }

                    echo $employer_title;
                    break;
                case 'job_type':
                    $categories = get_the_terms($post->ID, 'job_type');
                    if ( $categories <> "" ) {
                        $couter_comma = 0;
                        foreach ( $categories as $category ) {
                            echo esc_attr($category->name);
                            $couter_comma ++;
                            if ( $couter_comma < count($categories) ) {
                                echo ", ";
                            }
                        }
                    }
                    break;
                case 'specialisms':

                    $categories = get_the_terms($post->ID, 'specialisms');
                    if ( $categories <> "" ) {
                        $couter_comma = 0;
                        foreach ( $categories as $category ) {
                            echo esc_attr($category->name);
                            $couter_comma ++;
                            if ( $couter_comma < count($categories) ) {
                                echo ", ";
                            }
                        }
                    }
                    break;
                case 'posted':

                    $cs_job_posted = get_post_meta($post->ID, 'cs_job_posted', true);
                    $cs_job_posted_date = isset($cs_job_posted) && $cs_job_posted != '' ? date_i18n('d/m/Y', ($cs_job_posted)) : '';
                    echo esc_html($cs_job_posted_date);
                    break;
                case 'expired':

                    $cs_job_expired = get_post_meta($post->ID, 'cs_job_expired', true);
                    $cs_job_expiry_date = isset($cs_job_expired) && $cs_job_expired != '' ? date_i18n('d/m/Y', ($cs_job_expired)) : '';
                    echo esc_html($cs_job_expiry_date);
                    break;
                case 'views':
                    $cs_views = get_post_meta($post->ID, "cs_count_views", true);
                    $output = absint($cs_views);
                    $output .= ' / ';
                    $cs_shortlisted = count_usermeta('cs-jobs-wishlist', serialize(strval($post->ID)), 'LIKE');
                    $output .= absint($cs_shortlisted);
                    $output .= ' / ';
                    $applications = count_usermeta('cs-jobs-applied', serialize(strval($post->ID)), 'LIKE');
                    $output .= absint($applications);
                    $output = apply_filters( 'jobhunt_job_custom_columns_values', $output );
                    echo $output;
                    break;
                case 'status':
                    echo get_post_meta($post->ID, 'cs_job_status', true);
                    break;
               
                
            }
        }

    }

    // Initialize Object
    $job_object = new post_type_job();
}