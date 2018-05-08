<?php

/**
 * File Type: Forget Password Email Templates
 */
if ( ! class_exists('jobhunt_confirm_reset_password_email_template') ) {

    class jobhunt_confirm_reset_password_email_template {

        public $email_template_type;
        public $email_default_template;
        public $email_template_variables;
        public $email_template_index;
        public $is_email_sent;
        public static $is_email_sent1;
        public $args;
        public $template_group;

        public function __construct() {

            $this->email_template_type = 'Confirm Reset Password';

            $this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Confirm Reset Password</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Hello! Someone requested that the password be reset for the following account:</td></tr><tr><td style="padding: 10px 0 0 0;">[HOME_URL]</td></tr><tr><td style="padding: 10px 0 0 0;">User Name: [USER_NAME]</td></tr><tr><td style="padding: 10px 0 0 0;">If this was a mistake, just ignore this email and nothing will happen.</td></tr><tr><td style="padding: 10px 0 0 0;">To reset your password, visit the following address:</td></tr><tr><td style="padding: 10px 0 0 0;">[RESET_LINK]</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2017</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

            $this->email_template_variables = array(
                array(
					'tag' => 'USER_NAME',
					'display_text' => 'User name',
					'value_callback' => array( $this, 'get_user_name' ),
				),
				array(
					'tag' => 'RESET_LINK',
					'display_text' => 'Reset Link',
					'value_callback' => array( $this, 'get_reset_link' ),
				),
                array(
					'tag' => 'HOME_URL',
					'display_text' => 'Home URL',
					'value_callback' => array( $this, 'get_home_url' ),
				),
            );
            $this->template_group = 'Notification';

            $this->email_template_index = 'confirm-reset-pass-template';

            add_action('init', array( $this, 'add_email_template' ), 5);
            add_action('jobhunt_confirm_reset_password_email', array( $this, 'confirm_reset_password_email' ), 10, 1);
            add_filter('jobhunt_email_template_settings', array( $this, 'template_settings_callback' ), 11, 1);
        }

        public function confirm_reset_password_email( $args = array()) {
		
           $this->args = $args;
            $template = $this->get_template();
            // checking email notification is enable/disable
            if ( isset($template['email_notification']) && $template['email_notification'] == 1 ) {

                $blogname = get_option('blogname');
                $admin_email = get_option('admin_email');
                // getting template fields
                $subject = (isset($template['subject']) && $template['subject'] != '' ) ? $template['subject'] : $this->get_title();
                $from = (isset($template['from']) && $template['from'] != '') ? $template['from'] : esc_attr($blogname) . ' <' . $admin_email . '>';
                $recipients = (isset($template['recipients']) && $template['recipients'] != '') ? $template['recipients'] : $this->get_user_email();
                $email_type = (isset($template['email_type']) && $template['email_type'] != '') ? $template['email_type'] : 'html';

                $args = array(
                    'to' => $recipients,
                    'subject' => $subject,
                    //'from' => $from,
                    'message' => $template['email_template'],
                    'email_type' => $email_type,
                    'class_obj' => $this,
                );
				do_action('jobhunt_send_mail', $args);
                jobhunt_confirm_reset_password_email_template::$is_email_sent1 = $this->is_email_sent;
            }
        }

        public function template_settings_callback($email_template_options) {

            $email_template_options["types"][] = $this->email_template_type;

            $email_template_options["templates"]["Forget Password"] = $this->email_default_template;

            $email_template_options["variables"]["Forget Password"] = $this->email_template_variables;

            return $email_template_options;
        }

        public function add_email_template() {
            $email_templates = array();
            $email_templates[$this->template_group] = array();
            $email_templates[$this->template_group][$this->email_template_index] = array(
                'title' => $this->email_template_type,
                'template' => $this->email_default_template,
                'email_template_type' => $this->email_template_type,
                'is_recipients_enabled' => FALSE,
                'description' => __('This template is used sending email to user when confirm reset password.', 'jobhunt'),
                'jh_email_type' => 'html',
            );
            do_action('jobhunt_load_email_templates', $email_templates);
        }

        public function get_template() {
            return wp_jobhunt::get_template($this->email_template_index, $this->email_template_variables, $this->email_default_template);
        }

        function get_user_name() {
			return isset( $this->args['user_login'] ) ? $this->args['user_login'] : '';
		}
		function get_user_email() {
			return isset( $this->args['user_email'] ) ? $this->args['user_email'] : '';
		}
		function get_title() {
			return isset( $this->args['title'] ) ? $this->args['title'] : '';
		}
		function get_reset_link() {
			return isset( $this->args['reset_link'] ) ? $this->args['reset_link'] : '';
		}
        function get_home_url(){
 			return isset( $this->args['home_url'] ) ? $this->args['home_url'] : '';                   
        }

    }

    new jobhunt_confirm_reset_password_email_template();
}
