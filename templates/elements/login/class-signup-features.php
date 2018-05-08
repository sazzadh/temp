<?php

// Direct access not allowed.
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Jobhunt Signup Features
 */
class Jobhunt_Signup_Features {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter('jobhunt_signup_terms_field', array( $this, 'jobhunt_signup_terms_field_callback' ), 10, 4);
        add_action('jobhunt_verify_terms_policy', array( $this, 'jobhunt_verify_terms_policy_callback' ), 10, 1);
        add_action('jobhunt_allow_search_save', array( $this, 'jobhunt_allow_search_save_callback' ), 10, 2);
        add_filter('jobhunt_signup_terms_policy_backend_fields', array( $this, 'jobhunt_signup_terms_policy_backend_fields_callback' ), 10, 2);
    }

    public function jobhunt_allow_search_save_callback($array_data = array(), $user_id = '') {
        global $cs_plugin_options;
        if ( empty($user_id) ) {
            return;
        }

        $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
        if ( isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch != 'on' ) {
            return;
        }
        $allow_in_search = 'no';
        $id = $array_data['id']; //rand id 
        $allow_in_search = $array_data['cs_allow_in_search' . $id];
        if ( isset($allow_in_search) && $allow_in_search == 'yes' ) {
            update_user_meta($user_id, 'cs_allow_search', $allow_in_search);
        } else {
            update_user_meta($user_id, 'cs_allow_search', $allow_in_search);
        }
    }

    public function jobhunt_signup_terms_policy_backend_fields_callback($plugin_options = array()) {

        $on_off_option = array( "show" => "on", "hide" => "off" );
        $plugin_options[] = array( "name" => esc_html__("Terms and Conditions For Registeration", "jobhunt"),
            "id" => "tab-job-options",
            "std" => esc_html__("Terms and Conditions For Registeration", "jobhunt"),
            "type" => "section",
            "options" => ""
        );
        $plugin_options[] = array(
            "name" => esc_html__("Terms and Conditions switch", "jobhunt"),
            "desc" => "",
            "hint_text" => esc_html__("Turn this condition on to add terms and policy chek in the signup.", "jobhunt"),
            "id" => "terms_policy_switch",
            "std" => "on",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        $plugin_options[] = array(
            "name" => esc_html__("Candidate Terms and Conditions Page", "jobhunt"),
            "desc" => "",
            "hint_text" => esc_html__("Please select the candidate terms and conditions page.", "jobhunt"),
            "id" => "jobhunt_cand_term_page",
            "std" => "",
            "classes" => "chosen-select-no-single",
            "type" => "select_dashboard",
            "options" => '',
        );
        $plugin_options[] = array(
            "name" => esc_html__("Employer Terms and Conditions Page", "jobhunt"),
            "desc" => "",
            "hint_text" => esc_html__("Please select the employer terms and conditions page.", "jobhunt"),
            "id" => "jobhunt_emp_term_page",
            "std" => "",
            "classes" => "chosen-select-no-single",
            "type" => "select_dashboard",
            "options" => '',
        );
        $plugin_options[] = array(
            "name" => esc_html__("Privacy Policy", "jobhunt"),
            "desc" => "",
            "hint_text" => esc_html__("Please select the privacy policy page.", "jobhunt"),
            "id" => "jobhunt_privacy_page",
            "std" => "",
            "classes" => "chosen-select-no-single",
            "type" => "select_dashboard",
            "options" => '',
        );

        return $plugin_options;
    }

    public function jobhunt_verify_terms_policy_callback($array_data = array()) {
        $cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
        $cs_msg_html = '</p></div>';
        $id = $array_data['id']; //rand id 
        $terms_check = $array_data['jobhunt_check_terms' . $id];
        if ( isset($terms_check) && ! empty($terms_check) && $terms_check == 'on' ) {
            
        } else {
            $json['type'] = "error";
            $json['message'] = $cs_danger_html . esc_html__("Please check and accept Terms and Conditions to Register Successfully.", "jobhunt") . $cs_msg_html;
            echo json_encode($json);
            exit();
        }
    }

    public function jobhunt_signup_terms_field_callback($output = '', $rand_id = '', $role_terms = 'employers', $element = '') {
        global $cs_plugin_options, $cs_form_fields_frontend;

        $div_class = 'side-by-side select-icon clearfix';
        if ( $element == 'register' ) {
            $div_class = 'col-md-12 col-lg-12 col-sm-12 col-xs-12';
        }
        $allow_search_option = array(
            '' => esc_html__('Allow in search & listing', 'jobhunt'),
            'yes' => esc_html__('Yes', 'jobhunt'),
            'no' => esc_html__('No', 'jobhunt'),
        );
        $cs_opt_array = array(
            'id' => 'allow_in_search' . $rand_id,
            'std' => '',
            'return' => true,
            'extra_atr' => 'data-placeholder="' . esc_html__('Allow in search & listing', 'jobhunt') . '"',
            'classes' => 'chosen-select form-control',
            'options' => $allow_search_option,
            'options_markup' => true,
            'hint_text' => '',
        );

        $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
        if ( isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch == 'on' ) {
            $output .= '<div class="' . $div_class . '">';
            $output .= '<div class="select-holder">';
            $output .= $cs_form_fields_frontend->cs_form_select_render($cs_opt_array);
            $output .= '</div>';
            $output .= '</div>';
        }

        $cs_jobhunt_cand_term_page = isset($cs_plugin_options['jobhunt_cand_term_page']) && ! empty($cs_plugin_options['jobhunt_cand_term_page']) ? $cs_plugin_options['jobhunt_cand_term_page'] : '';
        $cs_jobhunt_emp_term_page = isset($cs_plugin_options['jobhunt_emp_term_page']) && ! empty($cs_plugin_options['jobhunt_emp_term_page']) ? $cs_plugin_options['jobhunt_emp_term_page'] : '';
        $jobhunt_privacy_page = isset($cs_plugin_options['jobhunt_privacy_page']) && ! empty($cs_plugin_options['jobhunt_privacy_page']) ? $cs_plugin_options['jobhunt_privacy_page'] : '';
        $cs_terms_policy_switch = isset($cs_plugin_options['cs_terms_policy_switch']) && ! empty($cs_plugin_options['cs_terms_policy_switch']) ? $cs_plugin_options['cs_terms_policy_switch'] : '';
        $term_cand_link = 'javascript:void(0)';
        $term_emp_link = 'javascript:void(0)';
        $privacy_link = 'javascript:void(0)';
        if ( ! empty($cs_jobhunt_cand_term_page) ) {
            $term_cand_link = get_the_permalink($cs_jobhunt_cand_term_page);
        }
        if ( ! empty($cs_jobhunt_emp_term_page) ) {
            $term_emp_link = get_the_permalink($cs_jobhunt_emp_term_page);
        }
        if ( ! empty($jobhunt_privacy_page) ) {
            $privacy_link = get_the_permalink($jobhunt_privacy_page);
        }

        $term_class = false;
        ;
        if ( $element == 'register' ) {
            $term_class = true;
        }
        if ( $term_class ) {
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
        }
        $output .= '<label class="terms"><input type="checkbox" name="jobhunt_check_terms' . $rand_id . '" id="jobhunt_check_terms' . $rand_id . '">'
                . ' ' . esc_html__("By registering you confirm that you accept the ", 'jobhunt');
        if ( $role_terms == 'employer' ) {
            $output .='<a target="_blank" href="' . $term_emp_link . '">' . esc_html__('Terms & Conditions ', 'jobhunt') . '</a>';
        }
        if ( $role_terms == 'candidate' ) {
            $output .='<a target="_blank" href="' . $term_cand_link . '"> ' . esc_html__('Terms & Conditions ', 'jobhunt') . ' </a>';
        }
        $output .=' and ';
        $output .='<a target="_blank" href="' . $privacy_link . '"> ' . esc_html__('Privacy Policy', 'jobhunt') . ' </a>';
        $output .='</label>';
        if ( $term_class ) {
            $output .= '</div>';
        }




//        $terms  = esc_html__('Terms & Conditions ', 'jobhunt');
//        $output .='<label> <input type="checkbox" name="jobhunt_check_terms' . $rand_id . '" id="jobhunt_check_terms' . $rand_id . '">';
//        $output .= sprintf("By registering you confirm that you accept the <a target='_blank' href='".$term_emp_link."'>%s</a>",$terms);
//        $output .='</label>';
//        

        return $output;
    }

}

new Jobhunt_Signup_Features();
