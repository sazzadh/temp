<?php
/*
 * Start Function how to manage of element_size
 */
if ( ! function_exists('element_size_data_array_index') ) {

    function element_size_data_array_index($size) {
        if ( $size == "" or $size == 100 )
            return 0;
        else if ( $size == 75 )
            return 1;
        else if ( $size == 67 )
            return 2;
        else if ( $size == 50 )
            return 3;
        else if ( $size == 33 )
            return 4;
        else if ( $size == 25 )
            return 5;
    }

}

/*
 * Start Function how to manage of element_size array index
 */
if ( ! function_exists('cs_element_size_data_array_index') ) {

    function cs_element_size_data_array_index($size) {
        if ( $size == "" or $size == 100 )
            return 0;
        else if ( $size == 75 )
            return 1;
        else if ( $size == 67 )
            return 2;
        else if ( $size == 50 )
            return 3;
        else if ( $size == 33 )
            return 4;
        else if ( $size == 25 )
            return 5;
    }

}

/*
 * Start Function how to manage of element_size using shortcode
 */
if ( ! function_exists('cs_shortcode_element_size') ) {

    function cs_shortcode_element_size($column_size = '') {
        global $cs_html_fields;
        $cs_opt_array = array(
            'name' => esc_html__('Size', 'jobhunt'),
            'desc' => '',
            'hint_text' => esc_html__('Select column width. This width will be calculated depend page width', 'jobhunt'),
            'echo' => true,
            'field_params' => array(
                'std' => $column_size,
                'id' => '',
                'cust_id' => 'column_size',
                'cust_name' => 'column_size[]',
                'options' => array(
                    '1/1' => esc_html__('1 Column', 'jobhunt'),
                    '1/2' => esc_html__('2 Columns', 'jobhunt'),
                    '1/3' => esc_html__('3 Columns', 'jobhunt'),
                    '1/4' => esc_html__('4 Columns', 'jobhunt'),
                    '1/6' => esc_html__('6 Columns', 'jobhunt'),
                ),
                'return' => true,
                'classes' => 'column_size chosen-select-no-single'
            ),
        );
        $cs_html_fields->cs_select_field($cs_opt_array);
    }

}

/*
 * Start Function how to manage of element setting
 */
if ( ! function_exists('cs_element_setting') ) {

    function cs_element_setting($name, $cs_counter, $element_size, $element_description = '', $page_element_icon = 'icon-star', $type = '') {
        global $cs_form_fields2;
        $element_title = str_replace("jobcareer_pb_", "", $name);
        $elm_name = str_replace("jobcareer_pb_", "", $name);
        $element_list = cs_element_list();
        ?>
        <div class="column-in">
            <?php
            $cs_opt_array = array(
                'id' => '',
                'std' => esc_attr($element_size),
                'cust_id' => "",
                'cust_name' => esc_attr($element_title) . "_element_size[]",
                'classes' => 'item',
            );
            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
            ?>
            <a href="javascript:;" onclick="javascript:_createpopshort(jQuery(this))" class="options"><i class="icon-gear"></i></a>
            <a href="#" class="delete-it btndeleteit"><i class="icon-trash-o"></i></a> &nbsp;
            <?php
            $icon_skip_array = array( "multi_counters", "spacer" );
            if ( ! in_array($elm_name, $icon_skip_array) ) {
                ?>
            <a class="decrement" onclick="javascript:decrement(this)"><i class="icon-minus4"></i></a> &nbsp;
            <a class="increment" onclick="javascript:increment(this)"><i class="icon-plus3"></i></a>
             <?php } ?>
            <span> 
                <?php $element_icon = apply_filters( 'jobcareer_shortcode_icon', 'cs-icon '.str_replace("jobcareer_pb_", "", $name).'-icon', $name ); ?>
                <i class="<?php echo $element_icon; ?>"></i>
                <strong><?php
                    if ( isset($element_list['element_list'][$elm_name]) ) {
                        echo cs_validate_data($element_list['element_list'][$elm_name]);
                    }
                    ?></strong><br/>
                <?php echo esc_attr($element_description); ?>
            </span>
        </div>
        <?php
    }

}

/*
 * Start Function how to manage of page element list
 */
if ( ! function_exists('cs_element_list') ) {

    function cs_element_list() {
        $element_list = array();
		
		$job_specialisms_label = esc_html__('Job specialisms', 'jobhunt');
		$job_specialisms_label = apply_filters( 'jobhunt_replace_job_specialisms_to_job_categories', $job_specialisms_label );
		
        $element_list['element_list'] = array(
            'register' => esc_html__('Register', 'jobhunt'),
            'cv_package' => esc_html__('CV Package', 'jobhunt'),
            'cv package' => esc_html__('CV Package', 'jobhunt'),
            'job_package' => esc_html__('Job Package', 'jobhunt'),
			'membership_package' => esc_html__('Apply Job Package', 'jobhunt'),
            'job package' => esc_html__('Job Package', 'jobhunt'),
            'jobs_search' => esc_html__('Jobs Search', 'jobhunt'),
            'jobs search' => esc_html__('Jobs Search', 'jobhunt'),
            'job_post' => esc_html__('Job Post', 'jobhunt'),
            'job post' => esc_html__('Job Post', 'jobhunt'),
            'about' => esc_html__('About', 'jobhunt'),
            'about' => esc_html__('About', 'jobhunt'),
            'candidate' => esc_html__('Candidate', 'jobhunt'),
            'quotes' => esc_html__('Quotes', 'jobhunt'),
            'employer' => esc_html__('Employer', 'jobhunt'),
            'jobhunt' => esc_html__('Jobs', 'jobhunt'),
            'job_specialisms' => $job_specialisms_label,
            'gallery' => esc_html__('gallery', 'jobhunt'),
            'slider' => esc_html__('Slider', 'jobhunt'),
            'blog' => esc_html__('Blog', 'jobhunt'),
            'flex_editor' => esc_html__('Flex Editor', 'jobhunt'),
            'flex editor' => esc_html__('Flex Editor', 'jobhunt'),
            'team' => esc_html__('Team', 'jobhunt'),
            'teams' => esc_html__('Teams', 'jobhunt'),
            'column' => esc_html__('Column', 'jobhunt'),
            'flex_column' => esc_html__('Column', 'jobhunt'),
            'flex column' => esc_html__('Column', 'jobhunt'),
            'accordions' => esc_html__('Accordions', 'jobhunt'),
            'contact' => esc_html__('Contact', 'jobhunt'),
            'divider' => esc_html__('Divider', 'jobhunt'),
            'message_box' => esc_html__('Message Box', 'jobhunt'),
            'image' => esc_html__('Image', 'jobhunt'),
            'image_frame' => esc_html__('Image Frame', 'jobhunt'),
            'map' => esc_html__('Map', 'jobhunt'),
            'video' => esc_html__('Video', 'jobhunt'),
            'slider' => esc_html__('Quote', 'jobhunt'),
            'quick_slider' => esc_html__('Quick Quote', 'jobhunt'),
            'quick slider' => esc_html__('Quick Quote', 'jobhunt'),
            'dropcap' => esc_html__('Drop cap', 'jobhunt'),
            'pricetable' => esc_html__('Price Table', 'jobhunt'),
            'tabs' => esc_html__('Tabs', 'jobhunt'),
            'sitemap' => esc_html__('Sitemap', 'jobhunt'),
            'accordion' => esc_html__('Accordion', 'jobhunt'),
            'prayer' => esc_html__('Prayer', 'jobhunt'),
            'prayer' => esc_html__('Prayer', 'jobhunt'),
            'table' => esc_html__('Table', 'jobhunt'),
            'call_to_action' => esc_html__('Call to Action', 'jobhunt'),
            'call to action' => esc_html__('Call to Action', 'jobhunt'),
            'clients' => esc_html__('Clients', 'jobhunt'),
            'heading' => esc_html__('Heading', 'jobhunt'),
            'testimonials' => esc_html__('Testimonials', 'jobhunt'),
            'infobox' => esc_html__('Info box', 'jobhunt'),
            'spacer' => esc_html__('Spacer', 'jobhunt'),
            'promobox' => esc_html__('Promo Box', 'jobhunt'),
            'offerslider' => esc_html__('Offer Slider', 'jobhunt'),
            'audio' => esc_html__('Audio', 'jobhunt'),
            'icons' => esc_html__('Icons', 'jobhunt'),
            'contactform' => esc_html__('Contact Form', 'jobhunt'),
            'tooltip' => esc_html__('Tooltip', 'jobhunt'),
            'services' => esc_html__('Services', 'jobhunt'),
            'icon_box' => esc_html__('Icon Box', 'jobhunt'),
            'highlight' => esc_html__('Highlight', 'jobhunt'),
            'list' => esc_html__('List', 'jobhunt'),
            'mesage' => esc_html__('Message', 'jobhunt'),
            'faq' => esc_html__('Faq', 'jobhunt'),
            'progressbars' => esc_html__('Progress bars', 'jobhunt'),
            'counter' => esc_html__('Counter', 'jobhunt'),
            'members' => esc_html__('Members', 'jobhunt'),
            'icon_box' => esc_html__('Icon Box', 'jobhunt'),
            'mailchimp' => esc_html__('Mail Chimp', 'jobhunt'),
            'facilities' => esc_html__('Facilities', 'jobhunt'),
            'tweets' => esc_html__('Tweets', 'jobhunt'),
            'button' => esc_html__('Button', 'jobhunt'),
            'team_post' => esc_html__('Team', 'jobhunt'),
            'team post' => esc_html__('Team', 'jobhunt'),
            'multi_counters' => esc_html__('Counter', 'jobhunt'),
            'multi counters' => esc_html__('Counter', 'jobhunt'),
            'portfolio' => esc_html__('Portfolio', 'jobhunt'),
            'multi_price_table' => esc_html__('Multi Price Table', 'jobhunt'),
            'employer' => esc_html__('Employer', 'jobhunt'),
            'jobs' => esc_html__('Jobs', 'jobhunt'),
            'job_specialisms' => $job_specialisms_label,
            'job specialisms' => $job_specialisms_label,
        );
        $element_list   = apply_filters( 'jobhunt_pagebuilder_elements_list', $element_list );
        return $element_list;
    }

}

/*
 * Start Function  to validate data
 */
if ( ! function_exists('cs_validate_data') ) {

    function cs_validate_data($input = '') {
        $output = $input;
        return $output;
    }

}