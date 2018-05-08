<?php

/**
 *  File Type: 2Checkout Gateway
 *

 */
if (!class_exists('CS_2CHECKOUT_GATEWAY')) {

    class CS_2CHECKOUT_GATEWAY {

        public function __construct() {
            // Do Something
        }
         
        //start function for payment checkout setting gateways
        
        public function settings() {
            global $post;

            $cs_rand_id = CS_FUNCTIONS()->cs_rand_id();

            $on_off_option = array("show" => esc_html__("on", "jobhunt"), "hide" => esc_html__("off", "jobhunt"));

            $cs_settings[] = array("name" => esc_html__("Custom Logo", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "2checkout_gateway_logo",
                "std" => "",
                "display" => "none",
                "type" => "logo"
            );

            $cs_settings[] = array("name" => esc_html__("Default Status", "jobhunt"),
                "desc" => "",
                "hint_text" => esc_html__("Show/Hide Gateway On Front End.", "jobhunt"),
                "id" => "2checkout_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => esc_html__("2CheckOut Sandbox", "jobhunt"),
                "desc" => "",
                "hint_text" => esc_html__("Only for Developer use.", "jobhunt"),
                "id" => "2checkout_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $cs_settings[] = array("name" => esc_html__("2CheckOut Business Email", "jobhunt"),
                "desc" => "",
                "hint_text" => "",
                "id" => "2checkout_email",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_jobhunt::plugin_url() . 'payments/gateways/class-2checkout.php';
            $cs_settings[] = array("name" => esc_html__("2CheckOut Ipn Url", "jobhunt"),
                "desc" => "",
                "hint_text" => esc_html__("Do not edit this Url", "jobhunt"),
                "id" => "dir_2checkout_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );

            return $cs_settings;
        }

       //start function for generate form
        
        public function cs_generate_form() {
            global $post;
        }

    }

}