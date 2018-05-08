<?php

/**
 * File Type: Candidate Profile Approved Email Template
 */
if (!class_exists('jobhunt_not_approved_candidate_profile_template')) {

    class jobhunt_not_approved_candidate_profile_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $template_type;
        public $email_template_index;
        public $template_group;
        public $candidate_id;
        public $is_email_sent;

        public function __construct() {

            $this->email_template_type = 'Not Approved Candidate Profile';
            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Not Approved Candidate Profile</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><p>Hello [CANDIDATE_NAME]! Your profile is not approved.</p></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2016</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
                    'tag' => 'CANDIDATE_NAME',
                    'display_text' => 'Candidate Name',
                    'value_callback' => array($this, 'get_not_approved_candidate_candidate_name'),
                ),
            );
            $this->template_group = 'Candidate';
            $this->email_template_index = 'not-approved-candidate-profile-template';

            add_filter('jobhunt_email_template_settings', array($this, 'template_settings_callback'), 12, 1);

            add_action('jobhunt_employer_profile_status_changed', array($this, 'candidate_profile_status_changed'), 10, 2);

            add_action('init', array($this, 'add_email_template'), 5);
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

            $email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

            return $email_template_options;
        }

        public function get_template() {
            return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        function get_not_approved_candidate_candidate_name() {
            $candidate_info = get_user_by('id', $this->candidate_id);
            return $candidate_info->display_name;
        }

        function get_not_approved_candidate_candidate_email() {
            $candidate_info = get_user_by('id', $this->candidate_id);
            return $candidate_info->user_email;
        }

        public function candidate_profile_status_changed($candidate_id, $candidate_old_status) {

            if ($candidate_id != '') {

                $this->candidate_id = $candidate_id;
                $user = new WP_User($candidate_id);
                $role = array_shift($user->roles);
                // checking user role
                if ($role == 'cs_candidate') {
                    // getting candidate status
                    $candidate_status = get_user_meta($candidate_id, 'cs_user_status', true);
                    $candidate_old_status;

                    // checking job status
                    if ($candidate_status == 'inactive' && $candidate_status != $candidate_old_status) {

                        $template = $this->get_template();

                        // checking email notification is enable/disable
                        if (isset($template['email_notification']) && $template['email_notification'] == 1) {

                            $blogname = get_option('blogname');
                            $admin_email = get_option('admin_email');
                            // getting template fields
                            $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : __('Not Approved Candidate Profile', 'jobhunt');
                            $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                            $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_not_approved_candidate_candidate_email();
                            $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                            $args = array(
                                'to' => $recipients,
                                'subject' => $subject,
                                'from' => $from,
                                'message' => $template['email_template'],
                                'email_type' => $email_type,
                            );
                            do_action('jobhunt_send_mail', $args);
                        }
                    }
                }
            }
        }

        public function add_email_template() {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => false,
                'description' => __('This template is used to sending email when candidate Status not approved', 'jobhunt'),
                'jh_email_type' => 'html',
            );
            do_action('jobhunt_load_email_templates', $email_templates);
        }

    }

    new jobhunt_not_approved_candidate_profile_template();
}