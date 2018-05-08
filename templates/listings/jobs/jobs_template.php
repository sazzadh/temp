<?php
/**
 * File Type: Job listing Shortcode
 * 
 * * Start Function how to create listing of Job
 *
 */
if ( ! function_exists('cs_jobs_listing') ) {

    function cs_jobs_listing($atts, $content = "") {
        global $wp_query;
        $current_timestamp = current_time('timestamp');
        do_action('jobhunt_vaavio_post_data_check', $_GET);
        foreach ( $wp_query->query_vars as $key => $val ) {
            $_GET[$key] = $val;
        }
        ob_start();
        ?>
        <div class="cs_alerts" ></div>	
        <div class="main-cs-loader" ></div>
        <?php
        global $wpdb, $cs_plugin_options, $cs_html_fields, $cs_form_fields2;
        $cs_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
        $cs_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
        $cs_job_map_style = isset($cs_plugin_options['def_map_style']) ? $cs_plugin_options['def_map_style'] : 'style-2';

        $defaults = array(
            'column_size' => '',
            'cs_job_show_pagination' => 'pagination', // yes or no
            'cs_job_pagination' => '10', // as per your requirement only numbers(0-9)
            'cs_job_searchbox' => 'yes', // yes or no
            'cs_job_filterable' => 'yes', // yes or no
            'cs_job_top_search' => '',
            'cs_job_map' => '',
            'cs_job_map_lat' => $cs_loc_latitude,
            'cs_job_map_long' => $cs_loc_longitude,
            'cs_job_map_zoom' => '11',
            'cs_job_map_height' => '300',
            'cs_job_map_style' => $cs_job_map_style,
            'cs_job_view' => 'simple', // simplelist, featured or detaillist
            'cs_job_result_type' => 'all', // all, featured
            'cs_job_title' => '', // simplelist or detaillist
            'cs_job_sub_title' => '', // sub title
            'cs_job_counter' => '',
        );
        $defaults = apply_filters('jobhunt_jobs_shortcode_frontend_default_attributes', $defaults);

        $a = shortcode_atts($defaults, $atts);

        $cs_job_map = $a['cs_job_map'];
        $cs_job_map_lat = $a['cs_job_map_lat'];
        $cs_job_map_long = $a['cs_job_map_long'];
        $cs_job_map_zoom = $a['cs_job_map_zoom'];
        $cs_job_map_height = $a['cs_job_map_height'];
        $cs_job_map_style = $a['cs_job_map_style'];

        $column_size = $a['column_size'];
        $login_user_is_employer_flag = 0;
        $login_user_is_candidate_flag = 0;
        $cs_emp_funs = new cs_employer_functions();
        if ( is_user_logged_in() ) {
            if ( $cs_emp_funs->is_employer() ) {
                $login_user_is_employer_flag = 1;
            } else {
                $login_user_is_candidate_flag = 1;
            }
        }
        // sorting filters
        $job_sort_by = 'default'; // default value
        $job_sort_order = 'desc';   // default value
        $job_filter_page_size = '';
        if ( $a['cs_job_show_pagination'] == 'pagination' ) {
            $cs_blog_num_post = $a['cs_job_pagination']; //pick from atribute 
        } else {
            if ( isset($a['cs_job_pagination']) and $a['cs_job_pagination'] <> '' ) {
                if ( $a['cs_job_pagination'] != 0 )
                    $cs_blog_num_post = $a['cs_job_pagination'];
                else
                    $cs_blog_num_post = "10";
            } else {
                $cs_blog_num_post = "10";
            }
        }
        if ( get_transient('job_filter_sort_by') != '' ) {
            $job_sort_by = get_transient('job_filter_sort_by');
        }

        $qryvar_sort_by_column = 'post_date';
        $qryvar_job_sort_type = 'DESC';
        $job_filter_page_size = $cs_blog_num_post;  // default value for paging
        if ( $a['cs_job_show_pagination'] == "pagination" ) {

            if ( get_transient('job_filter_page_size') != '' ) {
                $job_filter_page_size = get_transient('job_filter_page_size');
                $cs_blog_num_post = $job_filter_page_size;
            }
        }
        if ( $job_sort_by == 'default' ) {
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'cs_job_featured';
        } elseif ( $job_sort_by == 'recent' ) {
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'post_date';
        } elseif ( $job_sort_by == 'alphabetical' ) {
            $qryvar_job_sort_type = 'ASC';
            $qryvar_sort_by_column = 'post_title';
        } elseif ( $job_sort_by == 'featured' ) {
            $qryvar_job_sort_type = 'DESC';
            $qryvar_sort_by_column = 'cs_job_featured';
        }


        //$qryvar_job_sort_type = 'ASC';
        // getting all record of employer for paging
        if ( empty($_GET['page_job']) ) {
            $paged = 1;
        }

        if ( isset($_GET['cs_job_counter']) && isset($a['cs_job_counter']) && $a['cs_job_counter'] != '' && $_GET['cs_job_counter'] != $a['cs_job_counter'] ) {
            $paged = 1;
        } else {
            $paged = isset($_GET['page_job']) ? $_GET['page_job'] : 1;
            ;
        }

        $qrystr = '';
        $filter_arr = array();
        $radius_array = array();
        // check featured on or off
        if ( $a['cs_job_result_type'] == 'featured' ) {
            $filter_arr[] = array(
                'key' => 'cs_job_featured',
                'value' => 'yes',
                'compare' => '=',
            );
        }

        $posted = '';
        $specialisms = '';
        $job_title = '';
        $location = '';
        $job_type = '';
        $default_date_time_formate = 'd-m-Y H:i:s';
        $cs_job_posted_date_formate = 'd-m-Y H:i:s';
        $cs_job_expired_date_formate = 'd-m-Y H:i:s';
        if ( isset($_REQUEST['job_title']) ) {
            $job_title = $_REQUEST['job_title'];
            $job_title = str_replace("+", " ", $job_title);
        }
        if ( isset($_GET['location']) ) {
            $location = $_GET['location'];
        }
        if ( isset($_GET['posted']) ) {
            $posted = $_GET['posted'];
        }
        if ( isset($_GET['specialisms_string']) && $_GET['specialisms_string'] != '' ) {
            $specialisms = explode(",", $_GET['specialisms_string']);
            $qrystr .= '&specialisms=' . $_GET['specialisms_string'];
        } elseif ( isset($_GET['specialisms_string_all']) && $_GET['specialisms_string_all'] != '' ) {
            $specialisms = explode(",", $_GET['specialisms_string_all']);
            $qrystr .= '&specialisms=' . $_GET['specialisms_string_all'];
        } elseif ( isset($_GET['specialisms']) && $_GET['specialisms'] != '' ) {
            $specialisms = $_GET['specialisms'];
            $qrystr .= '&specialisms=' . $_GET['specialisms'];
            if ( ! is_array($specialisms) )
                $specialisms = Array( $specialisms );
        }

        if ( isset($_GET['job_type']) )
            $job_type = $_GET['job_type'];
        $cus_fields_count_arr = array();
        $location_condition_arr = array();
        // location check
        if ( $location != '' ) {
            $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
            $cs_google_api_key = isset($cs_plugin_options['cs_google_api_key']) ? $cs_plugin_options['cs_google_api_key'] : '';
            if ( isset($_GET['radius']) && $_GET['radius'] > 0 && $cs_radius_switch == 'on' ) {
                $cs_radius = $_GET['radius'];
                $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                $distance_km_miles = $cs_radius_measure;
                $qrystr .= '&radius=' . $cs_radius;  // added again this var in query string for linking again
                $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                if ( $distance_km_miles == 'km' ) {
                    if ( isset($_GET['radius']) ) {
                        $cs_radius = $cs_radius * 0.621371; // for km
                    }
                }
                $Latitude = '';
                $Longitude = '';
                $prepAddr = '';
                $minLat = '';
                $maxLat = '';
                $minLong = '';
                $maxLong = '';
                if ( isset($_GET['location']) && ! empty($_GET['location']) ) {
                    $address = sanitize_text_field($_GET['location']);
                    $prepAddr = str_replace(' ', '+', $address);
                    $geocode = file_get_contents('https://google.com/maps/api/geocode/json?' . $cs_google_api_key . '&address=' . $prepAddr . '&sensor=false');
                    $output = json_decode($geocode);
                    $Latitude = isset($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : '';
                    $Longitude = isset($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : '';
                    if ( isset($Latitude) && $Latitude <> '' && isset($Longitude) && $Longitude <> '' ) {
                        $zcdRadius = new RadiusCheck($Latitude, $Longitude, $cs_radius);
                        $minLat = $zcdRadius->MinLatitude();
                        $maxLat = $zcdRadius->MaxLatitude();
                        $minLong = $zcdRadius->MinLongitude();
                        $maxLong = $zcdRadius->MaxLongitude();
                    }
                }

                $cs_compare_type = 'CHAR';
                if ( $cs_radius > 0 ) {
                    $cs_compare_type = 'DECIMAL(10,6)';
                }
                //$cs_compare_type = 'DECIMAL';

                if ( $minLat != '' && $maxLat != '' && $minLong != '' && $maxLong != '' ) {

                    $radius_array = array(
                        'relation' => 'AND',
                        array(
                            'key' => 'cs_post_loc_latitude',
                            'value' => array( $minLat, $maxLat ),
                            'compare' => 'BETWEEN',
                            'type' => $cs_compare_type
                        ),
                        array(
                            'key' => 'cs_post_loc_longitude',
                            'value' => array( $minLong, $maxLong ),
                            'compare' => 'BETWEEN',
                            'type' => $cs_compare_type
                        ),
                    );
                }
            }
            $qrystr .= '&location=' . $location;
            $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
            if ( $cs_location_type == 'countries_and_cities' || $cs_location_type == 'countries_only' ) {
                if ( isset($radius_array) && is_array($radius_array) ) {

                    $location_condition_arr[] = array(
                        'relation' => 'AND',
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_country',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            $radius_array,
                        )
                    );
                    // for count query
                    $cus_fields_count_arr['location'][] = array(
                        'relation' => 'AND',
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_country',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            $radius_array,
                        )
                    );
                } else {

                    $location_condition_arr[] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'cs_post_loc_country',
                            'value' => $location,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'cs_post_loc_address',
                            'value' => $location,
                            'compare' => 'LIKE',
                        ),
                    );

                    // for count query
                    $cus_fields_count_arr['location'][] = array(
                        'key' => 'cs_post_loc_city',
                        'value' => $location,
                        'compare' => 'LIKE',
                    );
                    $cus_fields_count_arr['location'][] = array(
                        'key' => 'cs_post_loc_country',
                        'value' => $location,
                        'compare' => 'LIKE',
                    );
                    $cus_fields_count_arr['location'][] = array(
                        'key' => 'cs_post_loc_address',
                        'value' => $location,
                        'compare' => 'LIKE',
                    );
                }
            } elseif ( $cs_location_type == 'cities_only' || $cs_location_type == 'single_city' ) {

                if ( isset($radius_array) && is_array($radius_array) ) {
                    $location_condition_arr[] = array(
                        'relation' => 'AND',
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            $radius_array,
                        )
                    );
                    // for count query
                    $cus_fields_count_arr['location'][] = array(
                        'relation' => 'AND',
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            $radius_array,
                        )
                    );
                } else {
                    $location_condition_arr[] = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => 'LIKE',
                        ),
                        array(
                            'key' => 'cs_post_loc_address',
                            'value' => $location,
                            'compare' => 'LIKE',
                        ),
                    );

                    // for count query
                    $cus_fields_count_arr['location'][] = array(
                        'key' => 'cs_post_loc_city',
                        'value' => $location,
                        'compare' => 'LIKE',
                    );
                    $cus_fields_count_arr['location'][] = array(
                        'key' => 'cs_post_loc_address',
                        'value' => $location,
                        'compare' => 'LIKE',
                    );
                }
            }
        }
        //var_dump( $location_condition_arr );
        // posted date check
        if ( $posted != '' ) {
            $lastdate = '';
            $now = '';
            $qrystr .= '&posted=' . $posted;  // added again this var in query string for linking again
            if ( $posted == 'lasthour' ) {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-1 hours', $current_timestamp));
            } elseif ( $posted == 'last24' ) {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-24 hours', $current_timestamp));
            } elseif ( $posted == '7days' ) {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-7 days', $current_timestamp));
            } elseif ( $posted == '14days' ) {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-14 days', $current_timestamp));
            } elseif ( $posted == '30days' ) {
                $now = date($default_date_time_formate, $current_timestamp);
                $lastdate = date($default_date_time_formate, strtotime('-30 days', $current_timestamp));
            }
            if ( $lastdate != '' && $now != '' ) {
                $filter_arr[] = array(
                    'key' => 'cs_job_posted',
                    'value' => strtotime($lastdate),
                    'compare' => '>=',
                );
                // for count query
                $cus_fields_count_arr['posted'][] = array(
                    'key' => 'cs_job_posted',
                    'value' => strtotime($lastdate),
                    'compare' => '>=',
                );
            }
        }
        // end posted date check
        // posted date check
        $filter_arr2[] = '';
        // specialism check
        if ( $specialisms != '' && $specialisms != 'All specialisms' ) {
            $filter_multi_spec_arr = ['relation' => 'OR', ];
            foreach ( $specialisms as $specialisms_key ) {
                if ( $specialisms_key != '' ) {
                    $filter_multi_spec_arr[] = array(
                        'taxonomy' => 'specialisms',
                        'field' => 'slug',
                        'terms' => array( $specialisms_key )
                    );
                }
            }
            $filter_arr2[] = array(
                $filter_multi_spec_arr
            );
        }

        // job_type check
        if ( $job_type != '' ) {
            $qrystr .= '&job_type=' . $job_type;
            $filter_arr2[] = array(
                'taxonomy' => 'job_type',
                'field' => 'slug',
                'terms' => array( $job_type )
            );
        }

        $cs_job_cus_fields = get_option("cs_job_cus_fields");
        if ( is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0 ) {
            foreach ( $cs_job_cus_fields as $cus_field ) {
                if ( isset($cus_field['enable_srch']) && $cus_field['enable_srch'] == 'yes' ) {
                    $query_str_var_name = $cus_field['meta_key'];
                    if ( isset($_GET[$query_str_var_name]) && $_GET[$query_str_var_name] != '' ) {
                        if ( ! isset($cus_field['multi']) || $cus_field['multi'] != 'yes' ) {
                            $qrystr .= '&' . $query_str_var_name . '=' . $_GET[$query_str_var_name];
                        }
                        if ( $cus_field['type'] == 'dropdown' ) {
                            if ( isset($cus_field['multi']) && $cus_field['multi'] == 'yes' ) {
                                $_query_string_arr = getMultipleParameters();
                                $filter_multi_arr = ['relation' => 'OR', ];
                                foreach ( $_query_string_arr[$query_str_var_name] as $query_str_var_name_key ) {
                                    if ( $cus_field['post_multi'] == 'yes' ) {
                                        $filter_multi_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => serialize($query_str_var_name_key),
                                            'compare' => 'Like',
                                        );
                                    } else {
                                        $filter_multi_arr[] = array(
                                            'key' => $query_str_var_name,
                                            'value' => $query_str_var_name_key,
                                            'compare' => '=',
                                        );
                                    }
                                    $qrystr .= '&' . $query_str_var_name . '=' . $query_str_var_name_key;
                                }
                                $filter_arr[] = array(
                                    $filter_multi_arr
                                );
                                // for count query
                                $cus_fields_count_arr[$query_str_var_name][] = array(
                                    $filter_multi_arr
                                );
                            } else {
                                if ( $cus_field['post_multi'] == 'yes' ) {

                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => serialize($_GET[$query_str_var_name]),
                                        'compare' => 'Like',
                                    );
                                    // for count query
                                    $cus_fields_count_arr[$query_str_var_name][] = array(
                                        'key' => $query_str_var_name,
                                        'value' => serialize($_GET[$query_str_var_name]),
                                        'compare' => 'Like',
                                    );
                                } else {
                                    $filter_arr[] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_GET[$query_str_var_name],
                                        'compare' => '=',
                                    );
                                    // for count query
                                    $cus_fields_count_arr[$query_str_var_name][] = array(
                                        'key' => $query_str_var_name,
                                        'value' => $_GET[$query_str_var_name],
                                        'compare' => '=',
                                    );
                                }
                            }
                        } elseif ( $cus_field['type'] == 'text' || $cus_field['type'] == 'email' || $cus_field['type'] == 'url' ) {
                            $filter_arr[] = array(
                                'key' => $query_str_var_name,
                                'value' => $_GET[$query_str_var_name],
                                'compare' => 'LIKE',
                            );
                            // for count query
                            $cus_fields_count_arr[$query_str_var_name][] = array(
                                'key' => $query_str_var_name,
                                'value' => $_GET[$query_str_var_name],
                                'compare' => 'LIKE',
                            );
                        } elseif ( $cus_field['type'] == 'range' ) {
                            $ranges_str_arr = explode("-", $_GET[$query_str_var_name]);
                            if ( ! isset($ranges_str_arr[1]) ) {
                                $ranges_str_arr = explode(",", $ranges_str_arr[0]);
                            }
                            $range_first = $ranges_str_arr[0];
                            $range_seond = $ranges_str_arr[1];
                            $filter_arr[] = array(
                                'key' => $query_str_var_name,
                                'value' => $range_first,
                                'compare' => '>=',
                            );
                            $filter_arr[] = array(
                                'key' => $query_str_var_name,
                                'value' => $range_seond,
                                'compare' => '<=',
                            );

                            // for count query
                            $cus_fields_count_arr[$query_str_var_name][] = array(
                                'key' => $query_str_var_name,
                                'value' => $range_first,
                                'compare' => '>=',
                            );
                            // for count query
                            $cus_fields_count_arr[$query_str_var_name][] = array(
                                'key' => $query_str_var_name,
                                'value' => $range_seond,
                                'compare' => '<=',
                            );
                        } elseif ( $cus_field['type'] == 'date' ) {
                            $filter_arr[] = array(
                                'key' => $query_str_var_name,
                                'value' => $_GET[$query_str_var_name],
                                'compare' => 'LIKE',
                            );
                            $cus_fields_count_arr[$query_str_var_name][] = array(
                                'key' => $query_str_var_name,
                                'value' => $_GET[$query_str_var_name],
                                'compare' => 'LIKE',
                            );
                        }
                    }
                }
            }
        }
        $meta_post_ids_arr = '';
        $job_title_id_condition = '';
        if ( isset($filter_arr) && ! empty($filter_arr) ) {
            $meta_post_ids_arr = array();
            $meta_post_ids_arr = cs_get_query_whereclase_by_array($filter_arr);

            // if no result found in filtration 
            if ( empty($meta_post_ids_arr) ) {
                $meta_post_ids_arr = array( 0 );
            }
            $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
            $job_title_id_condition = " ID in (" . $ids . ") AND ";
        }
        ############ end filtration proccess ############

        $jobs_postqry = '';
        //if ($cs_job_map == 'yes') {
        if ( $job_title != '' ) {
            $qrystr .= '&job_title=' . $job_title;  // added again this var in query string for linking again

            $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'  AND post_type='jobs' AND post_status='publish'");
            if ( $post_ids ) {

                $jobs_postqry = array( 'posts_per_page' => "-1",
                    'post_type' => 'jobs',
                    'order' => $qryvar_job_sort_type,
                    'orderby' => $qryvar_sort_by_column,
                    'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                    'post__in' => $post_ids,
                    'fields' => 'ids', // only load ids
                    'tax_query' => array(
                        'relation' => 'AND',
                        $filter_arr2
                    ),
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
                        $location_condition_arr,
                    ),
                );
            }
        } else {
            $jobs_postqry = array( 'posts_per_page' => "-1", 'post_type' => 'jobs', 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column,
                'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                'post__in' => $meta_post_ids_arr,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    $location_condition_arr,
                ),
            );
        }

        $loop_count = new WP_Query($jobs_postqry);
        //temporary code
        $count_post = $loop_count->found_posts;
        // Map start
        include('jobs-map.php');
        //}
        if ( ! isset($args) ) {
            $args = '';
        }
        // getting job with page number
        if ( $qryvar_sort_by_column == 'cs_job_featured' ) {
            $qryvar_sort_by_column = 'meta_value';
            if ( $job_title != '' ) {
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                if ( $post_ids ) {
                    $args = array(
                        'posts_per_page' => "$cs_blog_num_post",
                        'post_type' => 'jobs',
                        'paged' => $paged,
                        'order' => $qryvar_job_sort_type,
                        //'orderby' => $qryvar_sort_by_column,
                        'orderby' => array(
                            'meta_value' => 'DESC',
                            'post_date' => 'DESC',
                        ),
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                        'post__in' => $post_ids,
                        'fields' => 'ids', // only load ids
                        'meta_key' => 'cs_job_featured',
                        'tax_query' => array(
                            'relation' => 'AND',
                            $filter_arr2
                        ),
                        'meta_query' => array(
                            array(
				'key' => 'cs_job_posted',
				'value' => strtotime($lastdate),
				'compare' => '>=',
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
                            $location_condition_arr,
                        ),
                    );
                }
            } else {
                $args = array(
                    'posts_per_page' => "$cs_blog_num_post",
                    'post_type' => 'jobs',
                    'paged' => $paged,
                    'order' => $qryvar_job_sort_type,
                    'orderby' => array(
                        'meta_value' => 'DESC',
                        'post_date' => 'DESC',
                    ),
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'cs_job_featured',
                    'fields' => 'ids', // only load ids
                    'tax_query' => array(
                        'relation' => 'AND',
                        $filter_arr2
                    ),
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
                        array(
                            'key' => 'cs_job_featured',
                            'compare' => 'EXISTS',
                            'type' => 'STRING'
                        ),
                        $location_condition_arr,
                    ),
                );
                /*
                 * For featured jobs only.
                 */
                if ( ! empty($meta_post_ids_arr) ) {
                    $args['post__in'] = $meta_post_ids_arr;
                }
            }
        } elseif ( $qryvar_sort_by_column == 'post_date' ) {
            $qryvar_sort_by_column = 'meta_value_num';
            if ( $job_title != '' ) {
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                if ( $post_ids ) {
                    $args = array( 'posts_per_page' => "$cs_blog_num_post", 'post_type' => 'jobs', 'paged' => $paged, 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column, 'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                        'post__in' => $post_ids,
                        'fields' => 'ids', // only load ids
                        'meta_key' => 'cs_job_posted',
                        'tax_query' => array(
                            'relation' => 'AND',
                            $filter_arr2
                        ),
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
                            $location_condition_arr,
                        ),
                    );
                }
            } else {
                $args = array( 'posts_per_page' => "$cs_blog_num_post", 'post_type' => 'jobs', 'paged' => $paged, 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column, 'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                    'meta_key' => 'cs_job_posted',
                    'post__in' => $meta_post_ids_arr,
                    'fields' => 'ids', // only load ids
                    'tax_query' => array(
                        'relation' => 'AND',
                        $filter_arr2
                    ),
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
                        $location_condition_arr,
                    ),
                );
            }
        } else {
            if ( $job_title != '' ) {
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                if ( $post_ids ) {
                    $args = array( 'posts_per_page' => "$cs_blog_num_post", 'post_type' => 'jobs', 'paged' => $paged, 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column, 'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                        'post__in' => $post_ids,
                        'fields' => 'ids', // only load ids
                        'tax_query' => array(
                            'relation' => 'AND',
                            $filter_arr2
                        ),
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
                            $location_condition_arr,
                        ),
                    );
                }
            } else {
                $args = array( 'posts_per_page' => "$cs_blog_num_post", 'post_type' => 'jobs', 'paged' => $paged, 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column, 'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                    'post__in' => $meta_post_ids_arr,
                    'fields' => 'ids', // only load ids
                    'tax_query' => array(
                        'relation' => 'AND',
                        $filter_arr2
                    ),
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
                        $location_condition_arr,
                    ),
                );
            }
        }

        $random_id = rand(0, 99999999);
        $number_option = rand(0, 99999999);
        if ( $a['cs_job_searchbox'] == 'yes' ) { // specialisms popup load when searchbox enable
            ?>
            <!-- start specialism popup -->
            <div class="modal fade" id="light" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-body">
                            <div class="white_content">
                                <a class="close" data-dismiss="modal">&nbsp;</a>
                                <form action="#" method="get" id="frm_all_specialisms<?php echo esc_html($random_id); ?>" >
                                    <?php
                                    // parse query string and create hidden fileds
                                    $final_query_str = str_replace("?", "", $qrystr);
                                    $query = explode('&', $final_query_str);
                                    foreach ( $query as $param ) {
                                        if ( ! empty($param) ) {
                                            list($name, $value) = explode('=', $param);
                                            $new_str = $name . "=" . $value;
                                            if ( is_array($name) ) {
                                                foreach ( $_query_str_single_value as $_query_str_single_value_arr ) {

                                                    $cs_opt_array = array(
                                                        'id' => '',
                                                        'std' => esc_attr($value),
                                                        'cust_id' => "",
                                                        'cust_name' => esc_attr($name) . '[]',
                                                    );
                                                    echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                                }
                                            } else {

                                                $cs_opt_array = array(
                                                    'id' => '',
                                                    'std' => esc_attr($value),
                                                    'cust_id' => "",
                                                    'cust_name' => esc_attr($name),
                                                );
                                                echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                            }
                                        }
                                    }
                                    ?>
                                    <ul class="custom-listing">
                                        <?php
                                        $specialisms_parent_id = 0;
                                        $input_type_specialism = 'radio';   // if first level then select only sigle specialism
                                        if ( $specialisms != '' ) {
                                            $selected_spec = get_term_by('slug', $specialisms[0], 'specialisms');
                                            $specialisms_parent_id = '';
                                            if ( isset($selected_spec->term_id) ) {
                                                $specialisms_parent_id = $selected_spec->term_id;
                                            }
                                        }
                                        $specialisms_args = array(
                                            'orderby' => 'name',
                                            'order' => 'ASC',
                                            'fields' => 'all',
                                            'hide_empty' => false,
                                            'slug' => '',
                                            'parent' => $specialisms_parent_id,
                                        );
                                        $all_specialisms = get_terms('specialisms', $specialisms_args);
                                        if ( count($all_specialisms) <= 0 ) {
                                            $specialisms_args = array(
                                                'orderby' => 'name',
                                                'order' => 'ASC',
                                                'fields' => 'all',
                                                'hide_empty' => false,
                                                'slug' => '',
                                                'parent' => isset($selected_spec->parent) ? $selected_spec->parent : '',
                                            );
                                            $all_specialisms = get_terms('specialisms', $specialisms_args);
                                            $cs_parent_id = isset($selected_spec->parent) ? $selected_spec->parent : '';
                                            if ( $cs_parent_id != 0 ) { // if parent is not root means not main parent
                                                $input_type_specialism = 'checkbox';   // if first level then select multiple specialism
                                            }
                                        } else {

                                            if ( $specialisms_parent_id != 0 ) { // if parent is not root means not main parent
                                                $input_type_specialism = 'checkbox';   // if first level then select multiple specialism
                                            }
                                        }
                                        if ( $input_type_specialism == 'checkbox' ) {
                                            $cs_opt_array = array(
                                                'id' => '',
                                                'std' => '',
                                                'cust_id' => "specialisms_string_all",
                                                'cust_name' => 'specialisms_string_all',
                                            );
                                            echo $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                        }
                                        if ( $all_specialisms != '' ) {
                                            // job_type check

                                            if ( $job_type != '' ) {
                                                $filter_arr2_job_type[] = array(
                                                    'taxonomy' => 'job_type',
                                                    'field' => 'slug',
                                                    'terms' => array( $job_type )
                                                );
                                            }
                                            if ( empty($filter_arr2_job_type) ) {
                                                $filter_arr2_job_type = '';
                                            }


                                            foreach ( $all_specialisms as $specialismsitem ) {
                                                ############ get count for this itration ##########
                                                $job_id_para = '';
                                                if ( $job_title != '' ) {
                                                    $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE " . $job_title_id_condition . " UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%'   AND post_type='jobs' AND post_status='publish'");
                                                    if ( $post_ids ) {
                                                        $specialisms_mypost = array( 'posts_per_page' => "1", 'post_type' => 'jobs', 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column,
                                                            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                            'post__in' => $post_ids,
                                                            'fields' => 'ids', // only load ids
                                                            'tax_query' => array(
                                                                'relation' => 'AND',
                                                                array(
                                                                    'taxonomy' => 'specialisms',
                                                                    'field' => 'slug',
                                                                    'terms' => array( $specialismsitem->slug ),
                                                                ), $filter_arr2_job_type
                                                            ),
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
                                                            )
                                                        );
                                                    }
                                                } else {
                                                    $specialisms_mypost = array( 'posts_per_page' => "1", 'post_type' => 'jobs', 'order' => $qryvar_job_sort_type, 'orderby' => $qryvar_sort_by_column,
                                                        'post__in' => $meta_post_ids_arr,
                                                        'fields' => 'ids', // only load ids
                                                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                                                        'tax_query' => array(
                                                            'relation' => 'AND',
                                                            array(
                                                                'taxonomy' => 'specialisms',
                                                                'field' => 'slug',
                                                                'terms' => array( $specialismsitem->slug ),
                                                            ), $filter_arr2_job_type
                                                        ),
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
                                                        )
                                                    );
                                                }
                                                $specialisms_loop_count = new WP_Query($specialisms_mypost);
                                                $specialisms_count_post = $specialisms_loop_count->found_posts;
                                                ###################################################

                                                if ( $input_type_specialism == 'checkbox' ) {
                                                    if ( isset($specialisms) && is_array($specialisms) ) {
                                                        if ( in_array($specialismsitem->slug, $specialisms) ) {
                                                            echo '<li class="' . $input_type_specialism . '">';
                                                            $cs_opt_array = array(
                                                                'extra_atr' => 'onchange="javascript:submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\', \'custom-listing\');"  checked="checked"',
                                                                'cust_name' => '',
                                                                'cust_id' => 'checklist' . esc_attr($number_option),
                                                                'classes' => '',
                                                                'std' => $specialismsitem->slug,
                                                                'return' => true,
                                                                'simple' => true,
                                                            );
                                                            echo $cs_form_fields2->cs_form_checkbox_render($cs_opt_array);

                                                            echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span></label></li>';
                                                        } else {
                                                            echo '<li class="' . $input_type_specialism . '">';
                                                            $cs_opt_array = array(
                                                                'extra_atr' => 'onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\', \'custom-listing\');" ',
                                                                'cust_name' => '',
                                                                'cust_id' => 'checklist' . esc_attr($number_option),
                                                                'classes' => '',
                                                                'std' => $specialismsitem->slug,
                                                                'return' => true,
                                                                'simple' => true,
                                                            );
                                                            echo $cs_form_fields2->cs_form_checkbox_render($cs_opt_array);
                                                            echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                        }
                                                    } else {
                                                        echo '<li class="' . $input_type_specialism . '">';
                                                        $cs_opt_array = array(
                                                            'extra_atr' => 'onchange="submit_specialism_form(\'frm_all_specialisms' . $random_id . '\', \'specialisms_string_all\');" ',
                                                            'cust_name' => '',
                                                            'cust_id' => 'checklistcomplete' . esc_attr($number_option),
                                                            'classes' => '',
                                                            'std' => $specialismsitem->slug,
                                                            'return' => true,
                                                        );
                                                        echo $cs_form_fields2->cs_form_checkbox_render($cs_opt_array);
                                                        echo '<label for="checklist' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                    }
                                                } else
                                                if ( $input_type_specialism == 'radio' ) {

                                                    if ( isset($specialisms) && is_array($specialisms) ) {
                                                        if ( in_array($specialismsitem->slug, $specialisms) ) {
                                                            echo '<li class="' . $input_type_specialism . '">';


                                                            $cs_opt_array = array(
                                                                'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();"',
                                                                'cust_name' => 'specialisms',
                                                                'cust_id' => 'checklistcomplete' . esc_attr($number_option),
                                                                'classes' => '',
                                                                'std' => $specialismsitem->slug,
                                                                'return' => true,
                                                            );
                                                            echo $cs_form_fields2->cs_form_radio_render($cs_opt_array);

                                                            echo '<label  class="active" for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . ' <span>(' . $specialisms_count_post . ')</span>  <i class="icon-check-circle"></i></label></li>';
                                                        } else {
                                                            echo '<li class="' . $input_type_specialism . '">';

                                                            $cs_opt_array = array(
                                                                'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();"',
                                                                'cust_name' => 'specialisms',
                                                                'cust_id' => 'checklistcomplete' . esc_attr($number_option),
                                                                'classes' => '',
                                                                'std' => $specialismsitem->slug,
                                                                'return' => true,
                                                            );
                                                            echo $cs_form_fields2->cs_form_radio_render($cs_opt_array);



                                                            echo '<label for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                        }
                                                    } else
                                                        echo '<li class="' . $input_type_specialism . '">';
                                                    $cs_opt_array = array(
                                                        'extra_atr' => 'onchange="javascript:frm_all_specialisms' . $random_id . '.submit();"',
                                                        'cust_name' => 'specialisms',
                                                        'cust_id' => 'checklistcomplete' . esc_attr($number_option),
                                                        'classes' => '',
                                                        'std' => $specialismsitem->slug,
                                                        'return' => true,
                                                    );
                                                    echo $cs_form_fields2->cs_form_radio_render($cs_opt_array);
                                                    echo '<label for="checklistcomplete' . $number_option . '">' . $specialismsitem->name . '<span>(' . $specialisms_count_post . ')</span></label></li>';
                                                }
                                                $number_option ++;
                                            }
                                        }
                                        ?>
                                    </ul>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } // end specialisms popup
        $rand_id = rand(0, 999999);
        ?>
        <div id="fade<?php echo esc_html($rand_id); ?>" class="black_overlay"></div>
        <!-- end popup -->
        <?php
        $column_class = jobcareer_custom_column_class($column_size);
        $cs_col_class = '';
        if ( $column_class != '' ) {
            $cs_col_class = $column_class;
            ?>
            <div class="<?php echo esc_html($cs_col_class); ?>">
                <?php
            }
            ?>
            <div class="cs-jobs-container">
                <?php
                // job hunt indeed add on jobs listing filter
                $args = apply_filters('job_hunt_indeed_jobs_listing_parameters', $args, $a);

                // job hunt deadline add on jobs listing parameters filter
                $args = apply_filters('job_hunt_jobs_listing_parameters', $args, $job_sort_by);
                //var_dump(json_encode($args));
                // Add query data
                $args = apply_filters('jobhunt_celine_job_argument', $args);
                do_action('jobhunt_after_jobs_listing', $args, $job_sort_by);
                
                
//                echo '<pre>';
//                print_r($args);
//                echo '</pre>';
                
                
                if ( $a['cs_job_searchbox'] == 'yes' ) {
                    echo '<div class="row">';
                    echo '<div class="cs-content-holder">';
                    include('jobs-searchbox.php');
                }
                // do_action('liamdemoncuit_job_style_call',$a,$args);
                // job view load
                if ( $a['cs_job_view'] == 'advance' ) {
                    include('views/cs_advance.php');
                }
                if ( $a['cs_job_view'] == 'modernv1' ) {
                    include('views/cs_modernv1.php');
                }
                
                if ( $a['cs_job_view'] == 'simple' ) { // duplicate
                    include('views/cs_simple.php');
                }

                if ( $a['cs_job_view'] == 'grid' ) {
                    include('views/cs_grid.php');
                }
                if ( $a['cs_job_view'] == 'boxed' ) {
                    include('views/cs_boxed.php');
                }
                if ( $a['cs_job_view'] == 'detail' ) {
                    include('views/cs_detail.php');
                }
                if ( $a['cs_job_view'] == 'modren' ) {
                    include('views/cs_modren.php');
                }
                if ( $a['cs_job_view'] == 'grid_classic' ) {
                    include('views/cs_grid_classic.php');
                }
                if ( $a['cs_job_view'] == 'grid_slider' ) {
                    include('views/cs_grid_slider.php');
                }
                if ( $a['cs_job_view'] == 'fancy' ) {
                    include('views/cs_fancy.php');
                }
                if ( $a['cs_job_view'] == 'classic' ) {
                    include('views/cs_classic.php');
                }// end job views
                if ( $a['cs_job_searchbox'] == 'yes' ) {
                    echo '</div>';
                    echo '</div>';
                }
                ?>          
            </div>
            <?php
            if ( isset($column_class) && $column_class != "" ) {
                ?>
            </div>
            <?php
        }
        //echo 'end shortcode';exit;
        $eventpost_data = ob_get_clean();
        return do_shortcode($eventpost_data);
    }

    add_shortcode('cs_jobs', 'cs_jobs_listing');
}