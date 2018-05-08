<?php
/**
 * @return html
 */
/*
 * Start Function how to create job elements and job short codes
 */

if ( ! function_exists('jobcareer_pb_jobs') ) {

    function jobcareer_pb_jobs($die = 0) {
        global $cs_node, $cs_html_fields, $post, $cs_form_fields2, $cs_plugin_options;
        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_cs_job_view = '';
        $output = array();
        $counter = $_POST['counter'];
        $cs_counter = $_POST['counter'];
        if ( isset($_POST['action']) && ! isset($_POST['shortcode_element_id']) ) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $POSTID = $_POST['POSTID'];
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $PREFIX = 'cs_jobs';
            $parseObject = new ShortcodeParse();
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
		
		$cs_loc_latitude = isset($cs_plugin_options['cs_post_loc_latitude']) ? $cs_plugin_options['cs_post_loc_latitude'] : '';
        $cs_loc_longitude = isset($cs_plugin_options['cs_post_loc_longitude']) ? $cs_plugin_options['cs_post_loc_longitude'] : '';
        $cs_map_zoom_level = isset($cs_plugin_options['cs_map_zoom_level']) ? $cs_plugin_options['cs_map_zoom_level'] : '';

        $defaults = array( 'column_size' => '1/1', 'cs_job_title' => '', 'cs_job_sub_title' => '', 'cs_job_top_search' => '', 'cs_job_map' => '', 'cs_job_map_lat' => $cs_loc_latitude, 'cs_job_map_long' => $cs_loc_longitude, 'cs_job_map_zoom' => $cs_map_zoom_level, 'cs_job_map_height' => '', 'cs_job_map_style' => 'style-2', 'cs_job_view' => 'simple', 'cs_job_result_type' => 'all', 'cs_job_searchbox' => 'yes', 'cs_job_filterable' => 'yes', 'cs_job_show_pagination' => 'pagination', 'cs_job_pagination' => '10', 'cs_job_counter' => '' );
        $defaults = apply_filters('jobhunt_jobs_shortcode_admin_default_attributes', $defaults);
        if ( isset($output['0']['atts']) )
            $atts = $output['0']['atts'];
        else
            $atts = array();
        $jobs_element_size = '50';
        foreach ( $defaults as $key => $values ) {
            if ( isset($atts[$key]) )
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_jobs';
        $coloumn_class = 'column_' . $jobs_element_size;
        if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter); ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php
        if ( isset($shortcode_view) ) {
            echo esc_attr($shortcode_view);
        }
        ?>" item="jobs" data="<?php echo element_size_data_array_index($jobs_element_size) ?>">
                 <?php cs_element_setting($name, $cs_counter, $jobs_element_size); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter); ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter); ?>" data-shortcode-template="[cs_jobs {{attributes}}]"  style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php esc_html_e('JC: JOB OPTIONS', 'jobhunt') ?></h5>
                    <a href="javascript:cs_remove_overlay('<?php echo esc_attr($name . $cs_counter) ?>','<?php echo esc_attr($filter_element); ?>')" class="cs-btnclose">
                        <i class="icon-times"></i></a>
                </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
                            cs_shortcode_element_size();
                        }

						$rand_num = rand(9876, 5432);
						$cs_opt_array = array(
							'std' => $rand_num,
							'id' => 'job_counter',
							'cust_name' => 'cs_job_counter[]',
						);
						$cs_form_fields2->cs_form_hidden_render($cs_opt_array);
						
						
						$cs_opt_array = array(
                            'name' => esc_html__('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Enter element title here", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_title,
                                'id' => 'job_title',
                                'cust_name' => 'cs_job_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
						
                        $cs_opt_array = array(
                            'name' => esc_html__('Section Sub Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Enter section sub title here", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_sub_title,
                                'id' => 'job_sub_title',
                                'cust_name' => 'cs_job_sub_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Top Content', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Choose top content of section with drop down element title, total rocords with title and filters can be select..", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_top_search,
                                'id' => 'job_top_search',
                                'cust_name' => 'cs_job_top_search[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'None' => esc_html__('None', 'jobhunt'),
                                    'section_title' => esc_html__('Element Title', 'jobhunt'),
                                    'total_records' => esc_html__('Total Records with Title', 'jobhunt'),
                                    'Filters' => esc_html__('Filters', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Search Box', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("On/Off search on listing with this dropdown", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_searchbox,
                                'id' => 'job_searchbox',
                                'cust_name' => 'cs_job_searchbox[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'jobhunt'),
                                    'no' => esc_html__('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

												
						$cs_opt_array = array(
                            'name' => __('Map on Top', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("ON/OFF map. This will display a map on top.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_map,
                                'id' => 'job_map',
                                'cust_name' => 'cs_job_map[]',
                                'classes' => 'dropdown chosen-select',
                                'extra_atr' => ' onchange="cs_candidate_map_switch(this.value)"',
                                'options' => array(
                                    'no' => __('No', 'jobhunt'),
                                    'yes' => __('Yes', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_map_display = $cs_job_map == 'yes' ? 'block' : 'none';

                        echo '<div id="cs_cand_map_area" style="display:' . $cs_map_display . ';">';

                        $cs_opt_array = array(
                            'name' => __('Map Latitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Latitude for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_map_lat,
                                'id' => 'job_map_lat',
                                'cust_name' => 'cs_job_map_lat[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Map Longitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Longitude for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_map_long,
                                'id' => 'job_map_long',
                                'cust_name' => 'cs_job_map_long[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => __('Zoom Level', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Zoom Level for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_map_zoom,
                                'id' => 'job_map_zoom',
                                'cust_name' => 'cs_job_map_zoom[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => __('Map Container Height', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => __("Enter Height for Map.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_map_height,
                                'id' => 'job_map_height',
                                'cust_name' => 'cs_job_map_height[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);

                        echo '</div>';

						
                         $job_views = array(
                                    'advance' => esc_html__('Advance', 'jobhunt'),
                                    'classic' => esc_html__('Classic', 'jobhunt'),
                                    'detail' => esc_html__('Detail', 'jobhunt'),
                                    'fancy' => esc_html__('Fancy', 'jobhunt'),
                                    'grid' => esc_html__('Grid', 'jobhunt'),
                                    'modren' => esc_html__('Modern', 'jobhunt'),
                                    'simple' => esc_html__('Simple', 'jobhunt'),
                                    'modernv1' => esc_html__('Modern V1', 'jobhunt'),
                                    'boxed' => esc_html__('Boxed', 'jobhunt'),
                                    'grid_classic' => esc_html__('Grid Classic', 'jobhunt'),
                                );
                        
                         $job_views = apply_filters('liamdemoncuit_job_style_field_element',$job_views);

                        $cs_opt_array = array(
                            'name' => esc_html__('Job View', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Choose job view with this dropdown", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_view,
                                'id' => 'job_view',
                                'cust_name' => 'cs_job_view[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => $job_views,
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Result Type', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Choose result type for view only featured or all", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_result_type,
                                'id' => 'job_result_type',
                                'cust_name' => 'cs_job_result_type[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'all' => esc_html__('All', 'jobhunt'),
                                    'featured' => esc_html__('Featured Only', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        do_action('jobhunt_jobs_shortcode_admin_fields', array( 'cs_job_type' => $cs_job_type, 'cs_job_alert_button' => $cs_job_alert_button ));

                        $cs_opt_array = array(
                            'name' => esc_html__('Pagination', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Pagination is the process of dividing a document into discrete pages. Manage job listings pagiantion via this dropdown.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_show_pagination,
                                'id' => 'job_show_pagination',
                                'cust_name' => 'cs_job_show_pagination[]',
                                'classes' => 'dropdown chosen-select',
                                'options' => array(
                                    'pagination' => esc_html__('Pagination', 'jobhunt'),
                                    'single_page' => esc_html__('Single Page', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Post Per Page', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__("Add number of post for show posts on page.", "jobhunt"),
                            'echo' => true,
                            'field_params' => array(
                                'std' => $cs_job_pagination,
                                'id' => 'job_pagination',
                                'cust_name' => 'cs_job_pagination[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);
						
                        if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
                            ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('jobcareer_pb_', '', $name)); ?>', '<?php echo esc_js($name . $cs_counter); ?>', '<?php echo esc_js($filter_element); ?>')" ><?php esc_html_e('Insert', 'jobhunt') ?></a> </li>
                            </ul>
                            <div id="results-shortocde"></div>
                        <?php } else { ?>
                            <ul class="form-elements">
                                <li class="to-label"></li>
                                <li class="to-field">
                                    <?php
                                    $cs_opt_array = array(
                                        'id' => '',
                                        'std' => 'jobs',
                                        'cust_id' => "",
                                        'cust_name' => "cs_orderby[]",
                                    );

                                    $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                    $cs_opt_array = array(
                                        'id' => '',
                                        'std' => esc_html__('Save', 'jobhunt'),
                                        'cust_id' => "",
                                        'cust_name' => "",
                                        'cust_type' => 'button',
                                        'extra_atr' => 'style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))"',
                                    );

                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <script>

            /*
             * popup over 
             */
            popup_over();
            /*
             *End popup over 
             */

            /*
             * modern selection box function
             */
            jQuery(document).ready(function ($) {
                chosen_selectionbox();
            });
            /*
             * modern selection box function
             */
        </script> 
        <?php
        if ( $die <> 1 )
            die();
    }

    add_action('wp_ajax_jobcareer_pb_jobs', 'jobcareer_pb_jobs');
}