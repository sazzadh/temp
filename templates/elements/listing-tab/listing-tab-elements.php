<?php
/*
 *
 * @Shortcode Name : Job Post
 * @retrun
 *
 */
if ( ! function_exists('jobcareer_pb_listing_tab') ) {

    function jobcareer_pb_listing_tab($die = 0) {
        global $cs_node, $cs_html_fields, $post, $cs_form_fields2;

        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_view = '';
        $output = array();
        $counter = $_POST['counter'];
        $cs_counter = $_POST['counter'];
        if ( isset($_POST['action']) && ! isset($_POST['shortcode_element_id']) ) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $POSTID = $_POST['POSTID'];
            $PREFIX = 'cs_listing_tab';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'listing_tab_element_title' => '',
            'listing_tab_element_subtitle' => '',
            'listing_tab_post_per_tab' => '',
            'listing_tab_job_tab_switch' => '',
            'listing_tab_candidate_tab_switch' => '',
            'listing_tab_employer_tab_switch' => '',
            'listing_tab_sidebar_switch' => '',
            'listing_tab_sidebar_select' => '',
        );
        if ( isset($output['0']['atts']) )
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if ( isset($output['0']['content']) )
            $listing_tab_content = $output['0']['content'];
        else
            $listing_tab_content = '';
        $listing_tab_element_size = '100';
        foreach ( $defaults as $key => $values ) {
            if ( isset($atts[$key]) )
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_listing_tab';
        $coloumn_class = 'column_' . $listing_tab_element_size;
        if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        $cs_rand_id = rand(13441324, 93441324);
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="listing_tab" data="<?php echo cs_element_size_data_array_index($listing_tab_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $listing_tab_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_listing_tab {{attributes}}]{{content}}[/cs_listing_tab]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php esc_html_e('JC: Listing Tab Option', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        $cs_opt_array = array(
                            'name' => esc_html__('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $listing_tab_element_title,
                                'id' => 'listing_tab_element_title',
                                'cust_name' => 'listing_tab_element_title[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Element Subtitle', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $listing_tab_element_subtitle,
                                'id' => 'listing_tab_element_subtitle',
                                'cust_name' => 'listing_tab_element_subtitle[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Posts Per Tab', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $listing_tab_post_per_tab,
                                'id' => 'listing_tab_post_per_tab',
                                'cust_name' => 'listing_tab_post_per_tab[]',
                                'return' => true,
                            ),
                        );

                        $cs_html_fields->cs_text_field($cs_opt_array);


                        $cs_opt_array = array(
                            'name' => esc_html__('Jobs Tab', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_attr($listing_tab_job_tab_switch),
                                'id' => 'listing_tab_job_tab_switch',
                                'cust_name' => 'listing_tab_job_tab_switch[]',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'jobhunt'),
                                    'no' => esc_html__('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Candidate Tab', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_attr($listing_tab_candidate_tab_switch),
                                'id' => 'listing_tab_candidate_tab_switch',
                                'cust_name' => 'listing_tab_candidate_tab_switch[]',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'jobhunt'),
                                    'no' => esc_html__('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Employer Tab', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_attr($listing_tab_employer_tab_switch),
                                'id' => 'listing_tab_employer_tab_switch',
                                'cust_name' => 'listing_tab_employer_tab_switch[]',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'jobhunt'),
                                    'no' => esc_html__('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $cs_opt_array = array(
                            'name' => esc_html__('Tab Sidebar', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'extra_atr' => 'onchange="cs_listing_tab_sidebar(this.value);"',
                                'std' => esc_attr($listing_tab_sidebar_switch),
                                'id' => 'listing_tab_sidebar_switch',
                                'cust_name' => 'listing_tab_sidebar_switch[]',
                                'options' => array(
                                    'yes' => esc_html__('Yes', 'jobhunt'),
                                    'no' => esc_html__('No', 'jobhunt'),
                                ),
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);

                        $sidebar_list = array( '' => esc_html__('Select Sidebar', 'jobhunt') );
                        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
                            $sidebar_list[$sidebar['id']] = $sidebar['name'];
                        }
                        $sidebar_div_show = ' style="display:none;"';
                        if ( isset($listing_tab_sidebar_switch) && $listing_tab_sidebar_switch == 'yes' ) {
                            $sidebar_div_show = '';
                        }


                        echo '<div id="sidebar_listing_tab"' . $sidebar_div_show . '>';
                        $cs_opt_array = array(
                            'name' => esc_html__('Select Sidebar', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => esc_attr($listing_tab_sidebar_select),
                                'id' => 'listing_tab_sidebar_select',
                                'cust_name' => 'listing_tab_sidebar_select[]',
                                'options' => $sidebar_list,
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_select_field($cs_opt_array);
                        echo '</div>';
                        ?>

                        <script>
                            function cs_listing_tab_sidebar(sidebar_switch) {

                                if (sidebar_switch == 'yes') {
                                    jQuery('#sidebar_listing_tab').show();
                                } else {
                                    jQuery('#sidebar_listing_tab').hide();
                                }

                            }
                        </script>


                    </div>
                    <?php if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) { ?>
                        <ul class="form-elements insert-bg">
                            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('jobcareer_pb_', '', $name); ?>', '<?php echo esc_js($name . $cs_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php esc_html_e('Insert', 'jobhunt'); ?></a> </li>
                        </ul>
                        <div id="results-shortocde"></div>
                    <?php } else { ?>
                        <ul class="form-elements noborder">
                            <li class="to-label"></li>
                            <li class="to-field">
                                <?php
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => 'listing_tab',
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
        <?php
        if ( $die <> 1 )
            die();
    }

    add_action('wp_ajax_jobcareer_pb_listing_tab', 'jobcareer_pb_listing_tab');
}