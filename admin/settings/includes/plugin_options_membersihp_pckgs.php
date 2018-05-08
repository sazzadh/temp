<?php

/*
 *File Type: jobhunt, membership packages options  
 */
if( ! class_exists( 'jobhunt_options_membership_packages' ) )
    {
        class jobhunt_options_membership_packages
        {
            public function __construct() 
            {
                add_action( 'wp_ajax_cs_add_membership_pkg_to_list', array( $this, 'cs_add_membership_pkg_to_list_callback' ) );
                add_action( 'wp_ajax_norpiv_cs_add_membership_pkg_to_list', array( $this, 'cs_add_membership_pkg_to_list_callback' ) );
            }
            
            public function save_memberhsip_pckgs_options()
            {
                $data = get_option( 'cs_plugin_options' );
                $membership_pkg_counter = 0;
                
                $membership_pkg_array   =   $membership_pkgs    =   $memberhsip_pkgsdata    =   array();
                if ( isset( $_POST['membership_pkg_id_array'] ) && ! empty( $_POST['membership_pkg_id_array'] ) ) 
                {
                    foreach ( $_POST['membership_pkg_id_array'] as $keys => $values ) 
                    {
                        if ( $values ) 
                        {
                            $membership_pkg_array['membership_pkg_id'] = $_POST['membership_pkg_id_array'][$membership_pkg_counter];
                            $membership_pkg_array['memberhsip_pkg_title'] = $_POST['memberhsip_pkg_title_array'][$membership_pkg_counter];
                            $membership_pkg_array['memberhsip_pkg_connects'] = $_POST['memberhsip_pkg_connects_array'][$membership_pkg_counter];
                            $membership_pkg_array['cs_membership_pkg_connects_rollover'] = $_POST['membership_pkg_connects_rollover_array'][$membership_pkg_counter];
                            $membership_pkg_array['memberhsip_pkg_price'] = $_POST['memberhsip_pkg_price_array'][$membership_pkg_counter];
                            $membership_pkg_array['membership_pkg_dur'] = $_POST['membership_pkg_dur_array'][$membership_pkg_counter];
                            $membership_pkg_array['membership_pkg_dur_period'] = $_POST['membership_pkg_dur_period_array'][$membership_pkg_counter];
                            $membership_pkg_array['membership_pkg_desc'] = $_POST['membership_pkg_desc_array'][$membership_pkg_counter];
                            $membership_pkgs[$values] = $membership_pkg_array;
                            $membership_pkg_counter ++;
                        }
                    }
                }
                
                $memberhsip_pkgsdata['cs_membership_pkgs_options']  =   $membership_pkgs;
                $cs_options = array_merge( $data, $memberhsip_pkgsdata );
                update_option( 'cs_plugin_options', $cs_options );
            }

            public function cs_add_membership_pkg_to_list_callback()
            {
                global $cs_form_fields2, $cs_html_fields, $post, $counter_membership_pkg, $memberhsip_pkg_title, $memberhsip_pkg_connects, $cs_membership_pkg_connects_rollover, $memberhsip_pkg_price, $membership_pkg_desc, $membership_pkg_dur_period, $membership_pkg_dur;
                
                foreach( $_POST as $key => $val )
                {
                    $$key   =   $val;
                }
                if( isset( $_POST['memberhsip_pkg_title'] ) && $_POST['memberhsip_pkg_title'] <> '' )
                {
                   $membership_pkg_id  =   time();
                }
                else
                {
                    $membership_pkg_id =   $counter_membership_pkg;
                }
                
                $cs_opt_array = array(
                    'id' => '',
                    'std' => absint($membership_pkg_id),
                    'cust_id' => '',
                    'cust_name' => "membership_pkg_id_array[]",
                    'return' => true,
                );
                
                $cs_html    =   '';
                
                $cs_html    .=  '<tr class="parentdelete" id="edit_track' . esc_attr($membership_pkg_id) . '">';
                $cs_html    .=  '   <td id="subject-title' . esc_attr($counter_membership_pkg) . '" style="width:100%;">' . esc_attr($memberhsip_pkg_title) . '</td>';
                $cs_html    .=  '   <td class="centr" style="width:20%;"><a href="javascript:cs_createpop(\'edit_track_form' . esc_js($counter_membership_pkg) . '\',\'filter\')" class="actions edit">&nbsp;</a> <a href="#" class="delete-it btndeleteit actions delete">&nbsp;</a></td>';
                $cs_html    .=  '   <td style="width:0"><div id="edit_track_form' . esc_attr($counter_membership_pkg) . '" style="display: none;" class="table-form-elem">';
                $cs_html    .=          $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                $cs_html    .=  '       <div class="cs-heading-area">';
                $cs_html    .=  '           <h5 style="text-align: left;"> ' . esc_html__('Package Settings', 'jobhunt') . '</h5>';
                $cs_html    .=  '           <span onclick="javascript:cs_remove_overlay(\'edit_track_form' . esc_js($counter_membership_pkg) . '\',\'append\')" class="cs-btnclose"> <i class="icon-times"></i></span>';
                $cs_html    .=  '           <div class="clear"></div>';
                $cs_html    .=  '       </div>';
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Package Title', 'jobhunt'),
                                            'desc' => '',
                                            'hint_text' => esc_html__("Enter Package Title here.", "jobhunt"),
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => htmlspecialchars($memberhsip_pkg_title),
                                                'cust_id' => 'memberhsip_pkg_title' . esc_attr($counter_membership_pkg),
                                                'cust_name' => 'memberhsip_pkg_title_array[]',
                                                'return' => true,
                                                'array' => true,
                                                'force_std' => true
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_text_field($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Number of Jobs', 'jobhunt'),
                                            'desc' => '',
                                            'hint_text' => esc_html__("Enter number of jobs awared with this package", "jobhunt"),
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => htmlspecialchars($memberhsip_pkg_connects),
                                                'cust_id' => 'memberhsip_pkg_connects' . esc_attr($counter_membership_pkg),
                                                'cust_name' => 'memberhsip_pkg_connects_array[]',
                                                'return' => true,
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_text_field($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Roll Over Jobs', 'jobhunt'),
                                            'desc' => '',
                                            'hint_text' => '',
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => $cs_membership_pkg_connects_rollover,
                                                'id' => 'membership_pkg_connects_rollover' . esc_attr($counter_membership_pkg),
                                                'cust_name' => 'membership_pkg_connects_rollover_array[]',
                                                'return' => true,
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_checkbox_field($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Price', 'jobhunt'),
                                            'desc' => '',
                                            'hint_text' => esc_html__("Enter price for this package", "jobhunt"),
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => htmlspecialchars($memberhsip_pkg_price),
                                                'cust_id' => 'memberhsip_pkg_price' . esc_attr($counter_membership_pkg),
                                                'cust_name' => 'memberhsip_pkg_price_array[]',
                                                'return' => true,
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_text_field($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Package Expiry', 'jobhunt'),
                                            'id' => '',
                                            'desc' => '',
                                            'fields_list' => array(
                                                array( 'type' => 'text', 'field_params' => array(
                                                        'std' => htmlspecialchars($membership_pkg_dur),
                                                        'id' => '',
                                                        'cust_id' => 'membership_pkg_dur' . esc_attr($counter_membership_pkg),
                                                        'cust_name' => 'membership_pkg_dur_array[]',
                                                        'cust_type' => '',
                                                        'classes' => 'input-large',
                                                        'return' => true,
                                                    ),
                                                ),
                                                array( 'type' => 'select', 'field_params' => array(
                                                        'std' => htmlspecialchars($membership_pkg_dur_period),
                                                        'id' => 'membership_pkg_dur_period',
                                                        'cust_type' => '',
                                                        'cust_id' => 'membership_pkg_dur_period' . esc_attr($counter_membership_pkg),
                                                        'cust_name' => 'membership_pkg_dur_period_array[]',
                                                        'classes' => 'chosen-select-no-single',
                                                        'div_classes' => 'select-small',
                                                        'return' => true,
                                                        'options' => array(
                                                            'days' => esc_html__('Days', 'jobhunt'),
                                                            'months' => esc_html__('Months', 'jobhunt'),
                                                            'years' => esc_html__('Years', 'jobhunt'),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_multi_fields($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => esc_html__('Description', 'jobhunt'),
                                            'desc' => '',
                                            'hint_text' => '',
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => htmlspecialchars($membership_pkg_desc),
                                                'cust_id' => 'membership_pkg_desc' . esc_attr($counter_membership_pkg),
                                                'cust_name' => 'membership_pkg_desc_array[]',
                                                'return' => true,
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_textarea_field($cs_opt_array);
                                        $cs_opt_array = array(
                                            'name' => '',
                                            'desc' => '',
                                            'hint_text' => '',
                                            'echo' => false,
                                            'field_params' => array(
                                                'std' => esc_html__('Update Package', 'jobhunt'),
                                                'cust_id' => '',
                                                'cust_name' => '',
                                                'return' => true,
                                                'cust_type' => 'button',
                                                'extra_atr' => 'onclick="update_title(' . esc_js($counter_membership_pkg) . '); cs_remove_overlay(\'edit_track_form' . esc_js($counter_membership_pkg) . '\',\'append\')"',
                                            ),
                                        );
                $cs_html    .=          $cs_html_fields->cs_text_field($cs_opt_array);
                
                $cs_html    .=  '   </td>';
                $cs_html    .=  '</tr>';
                
                if ( isset( $_POST['memberhsip_pkg_title'] ) ) 
                {
                    echo force_balance_tags($cs_html);
                    die();
                } 
                else 
                {
                    return $cs_html;
                }
            }

            public function cs_membership_packages()
            {
                global $cs_form_fields2, $cs_html_fields, $post, $counter_membership_pkg, $memberhsip_pkg_title, $cs_membership_pkg_connects_rollover, $memberhsip_pkg_connects, $memberhsip_pkg_price, $membership_pkg_desc, $membership_pkg_dur_period, $membership_pkg_dur;
                
                $cs_plugin_options = get_option('cs_plugin_options');
                $cs_membership_pkgs_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';
                //echo '<pre>'; print_r($cs_membership_pkgs_options); echo '</pre>';
                
                $cs_html    =   '';
                $cs_html    .=  '<div class="form-elements" id="safetysafe_switch_add_package_cv">';
		$cs_html    .=  '   <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">';
                $cs_html    .=  '       <a href="javascript:cs_createpop(\'add_memberhsip_pkg_title\',\'filter\')" class="button button_style">' . esc_html__('Add Package', 'jobhunt') . '</a>';
		$cs_html    .=  '   </div>';			
		$cs_html    .=  '</div>';		
                
                $cs_html    .=  '<div class="cs-list-table">';
                $cs_html    .=  '   <table class="to-table" border="0" cellspacing="0">';
                $cs_html    .=  '       <thead>';
                $cs_html    .=  '           <tr>';
                $cs_html    .=  '               <th style="width:80%;">' . esc_html__('Title', 'jobhunt') . '</th>';
                $cs_html    .=  '               <th style="width:20%;" class="centr">' . esc_html__('Actions', 'jobhunt') . '</th>';
                $cs_html    .=  '               <th style="width:0%;" class="centr"></th>';
                $cs_html    .=  '           </tr>';
                $cs_html    .=  '       </thead>';
                $cs_html    .=  '       <tbody id="total_memberhsip_pkgs">';
                                        if ( isset( $cs_membership_pkgs_options ) && is_array( $cs_membership_pkgs_options ) && count( $cs_membership_pkgs_options ) > 0 ) 
                                        {
                                            foreach ( $cs_membership_pkgs_options as $membership_pkg_key => $membership_pkg ) 
                                            {
                                                if ( isset($membership_pkg_key) && $membership_pkg_key <> '' ) 
                                                {
                                                    $counter_membership_pkg                 =   $membership_pkg_id = isset($membership_pkg['membership_pkg_id']) ? $membership_pkg['membership_pkg_id'] : '';
                                                    $memberhsip_pkg_title                   =   isset($membership_pkg['memberhsip_pkg_title']) ? $membership_pkg['memberhsip_pkg_title'] : '';
                                                    $memberhsip_pkg_connects                =   isset($membership_pkg['memberhsip_pkg_connects']) ? $membership_pkg['memberhsip_pkg_connects'] : '';
                                                    $cs_membership_pkg_connects_rollover    =   isset($membership_pkg['cs_membership_pkg_connects_rollover']) ? $membership_pkg['cs_membership_pkg_connects_rollover'] : '';
                                                    $memberhsip_pkg_price                   =   isset($membership_pkg['memberhsip_pkg_price']) ? $membership_pkg['memberhsip_pkg_price'] : '';
                                                    $membership_pkg_dur                     =   isset($membership_pkg['membership_pkg_dur']) ? $membership_pkg['membership_pkg_dur'] : '';
                                                    $membership_pkg_dur_period              =   isset($membership_pkg['membership_pkg_dur_period']) ? $membership_pkg['membership_pkg_dur_period'] : '';
                                                    $membership_pkg_desc                    =   isset($membership_pkg['membership_pkg_desc']) ? $membership_pkg['membership_pkg_desc'] : '';
                                                    
                                                    $cs_html                    .=  $this->cs_add_membership_pkg_to_list_callback();
                                                }
                                            }
                                        }
                $cs_html    .=  '       </tbody>';
                $cs_html    .=  '   </table>'; 
                $cs_html    .=  '</div>';
                
                $cs_html    .=  '<div id="add_memberhsip_pkg_title" style="display: none;">';
                $cs_html    .=  '   <div class="cs-heading-area">';
                $cs_html    .=  '       <h5> <i class="icon-plus-circle"></i> ' . esc_html__('Package Settings', 'jobhunt') . ' </h5>';
                $cs_html    .=  '       <span class="cs-btnclose" onClick="javascript:cs_remove_overlay(\'add_memberhsip_pkg_title\',\'append\')"> <i class="icon-times"></i></span>';
                $cs_html    .=  '   </div>';
                
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Package Title', 'jobhunt'),
                                        'desc' => '',
                                        'hint_text' => esc_html__("Enter title here.", "jobhunt"),
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => '',
                                            'cust_id' => 'memberhsip_pkg_title',
                                            'cust_name' => 'memberhsip_pkg_title',
                                            'return' => true,
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_text_field($cs_opt_array);
                
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Number of allowed Jobs', 'jobhunt'),
                                        'desc' => '',
                                        'hint_text' => esc_html__("Enter number of jobs allowed with this package", "jobhunt"),
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => '',
                                            'cust_id' => 'memberhsip_pkg_connects',
                                            'cust_name' => 'memberhsip_pkg_connects',
                                            'return' => true,
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_text_field($cs_opt_array);
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Roll Over Jobs', 'jobhunt'),
                                        'desc' => '',
                                        'hint_text' => '',
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => '',
                                            'id' => 'membership_pkg_connects_rollover',
                                            'return' => true,
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_checkbox_field($cs_opt_array);
                
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Price', 'jobhunt'),
                                        'desc' => '',
                                        'hint_text' => esc_html__("Enter price for this package", "jobhunt"),
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => '',
                                            'cust_id' => 'memberhsip_pkg_price',
                                            'cust_name' => 'memberhsip_pkg_price',
                                            'return' => true,
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_text_field($cs_opt_array);
                
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Package Expiry', 'jobhunt'),
                                        'id' => '',
                                        'desc' => '',
                                        'fields_list' => array(
                                            array( 'type' => 'text', 'field_params' => array(
                                                    'std' => '',
                                                    'id' => '',
                                                    'cust_id' => 'membership_pkg_dur',
                                                    'cust_name' => 'membership_pkg_dur',
                                                    'cust_type' => '',
                                                    'classes' => 'input-large',
                                                    'return' => true,
                                                ),
                                            ),
                                            array( 'type' => 'select', 'field_params' => array(
                                                    'std' => '',
                                                    'id' => 'membership_pkg_dur_period',
                                                    'cust_type' => '',
                                                    'cust_id' => 'membership_pkg_dur_period',
                                                    'cust_name' => 'membership_pkg_dur_period',
                                                    'classes' => 'chosen-select-no-single',
                                                    'div_classes' => 'select-small',
                                                    'return' => true,
                                                    'options' => array(
                                                        'days' => esc_html__('Days', 'jobhunt'),
                                                        'months' => esc_html__('Months', 'jobhunt'),
                                                        'years' => esc_html__('Years', 'jobhunt'),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_multi_fields($cs_opt_array);
            
                                    $cs_opt_array = array(
                                        'name' => esc_html__('Description', 'jobhunt'),
                                        'desc' => '',
                                        'hint_text' => '',
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => '',
                                            'cust_id' => 'membership_pkg_desc',
                                            'cust_name' => 'membership_pkg_desc',
                                            'return' => true,
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_textarea_field($cs_opt_array);
            
                                    $cs_opt_array = array(
                                        'name' => '',
                                        'desc' => '',
                                        'hint_text' => '',
                                        'echo' => false,
                                        'field_params' => array(
                                            'std' => esc_html__('Add Package to List', 'jobhunt'),
                                            'cust_id' => '',
                                            'cust_name' => '',
                                            'return' => true,
                                            'cust_type' => 'button',
                                            'after' => '<div class="membership_pkg-loader"></div>',
                                            'extra_atr' => 'onClick="add_membership_pkg_to_list(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_js(wp_jobhunt::plugin_url()) . '\')" ',
                                        ),
                                    );
                $cs_html    .=      $cs_html_fields->cs_text_field($cs_opt_array);
                
                $cs_html    .=  '</div>';
			
                return $cs_html;
            }
        }
}

if( class_exists( 'jobhunt_options_membership_packages' ) )
{
    $memberhsip_pckg_obj    =   new jobhunt_options_membership_packages();
}