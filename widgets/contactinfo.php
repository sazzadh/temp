<?php
/**
 * @Jobs Counter widget Class
 *
 *
 */
if ( ! class_exists('contactinfo') ) {

    class contactinfo extends WP_Widget {

        /**
         * Start Function how to create Jobs Counter Module
         *        
         */
        public function __construct() {
            parent::__construct(
                    'contactinfo', // Base ID
                    esc_html__('CS : Contact info', 'jobhunt'), // Name
                    array( 'classname' => 'widget-text widget-contact-information', 'description' => esc_html__('Add a contact info to your sidebar', 'jobhunt'), )
            );
        }

        /**
         * Start Function how to create Jobs Counter html form
         *        
         */
        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields, $cs_theme_html_fields;
            $instance = wp_parse_args((array) $instance, array( 'title' => '' ));
            $title = $instance['title'];
            $sub_title = isset($instance['sub_title']) ? $instance['sub_title'] : '';
            $contact_view = isset($instance['contact_view']) ? $instance['contact_view'] : '';
            $image_url = isset($instance['image_url']) ? esc_url($instance['image_url']) : '';
            $telephone = isset($instance['telephone']) ? esc_attr($instance['telephone']) : '';
            $office_hours = isset($instance['office_hours']) ? esc_attr($instance['office_hours']) : '';
            $email = isset($instance['email']) ? esc_attr($instance['email']) : '';
            $fb_url = isset($instance['fb_url']) ? esc_url($instance['fb_url']) : '';
            $tw_url = isset($instance['tw_url']) ? esc_url($instance['tw_url']) : '';
            $lk_url = isset($instance['lk_url']) ? esc_url($instance['lk_url']) : '';
            $gl_url = isset($instance['gl_url']) ? esc_url($instance['gl_url']) : '';
            $ig_url = isset($instance['ig_url']) ? esc_url($instance['ig_url']) : '';
            $yt_url = isset($instance['yt_url']) ? esc_url($instance['yt_url']) : '';

            $randomID = rand(135434, 957655);
            $random = rand(1345434, 957345345655);

            $cs_opt_array = array(
                'name' => esc_html__('Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $sub_title,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('sub_title')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('sub_title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('View', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $contact_view,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('contact_view')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('contact_view')),
                    'options' => array( 'view-1' => esc_html__('View 1', 'jobhunt'), 'view-2' => esc_html__('View 2', 'jobhunt') ),
                    'return' => true,
                    'required' => false
                ),
            );
            $cs_html_fields->cs_select_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Telephone', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $telephone,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('telephone')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('telephone')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Enter Address', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $title,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('title')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_textarea_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Email', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $email,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('email')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('email')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Office Hours', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => $office_hours,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('office_hours')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('office_hours')),
                    'return' => true,
                    'required' => false
                ),
            );
            $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Facebook Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $fb_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('fb_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('fb_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Twitter Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $tw_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('tw_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('tw_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);


            $cs_opt_array = array(
                'name' => esc_html__('Linkedin Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $lk_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('lk_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('lk_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Google Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $gl_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('gl_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('gl_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'name' => esc_html__('Instagram Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $ig_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('ig_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('ig_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);


            $cs_opt_array = array(
                'name' => esc_html__('Youtube Url', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => false,
                'field_params' => array(
                    'std' => $yt_url,
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('yt_url')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('yt_url')),
                    'return' => true,
                    'required' => false
                ),
            );
            echo $cs_html_fields->cs_text_field($cs_opt_array);

            $cs_opt_array = array(
                'std' => $image_url,
                'id' => 'form-widget_cs_widget_logo' . absint($randomID),
                'name' => esc_html__('Logo', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'prefix' => '',
                'field_params' => array(
                    'std' => $image_url,
                    'id' => 'form-widget_cs_widget_logo' . absint($randomID),
                    'cust_name' => $this->get_field_name('image_url'),
                    'return' => true,
                    'prefix' => '',
                ),
            );

            $cs_html_fields->cs_upload_file_field($cs_opt_array);
        }

        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['sub_title'] = $new_instance['sub_title'];
            $instance['contact_view'] = $new_instance['contact_view'];
            $instance['telephone'] = $new_instance['telephone'];
            $instance['office_hours'] = $new_instance['office_hours'];
            $instance['email'] = $new_instance['email'];
            $instance['fb_url'] = $new_instance['fb_url'];
            $instance['tw_url'] = $new_instance['tw_url'];
            $instance['lk_url'] = $new_instance['lk_url'];
            $instance['gl_url'] = $new_instance['gl_url'];
            $instance['ig_url'] = $new_instance['ig_url'];
            $instance['yt_url'] = $new_instance['yt_url'];
            $instance['image_url'] = $new_instance['image_url'];

            return $instance;
        }

        /**
         * Start Function how to Display Jobs Counter widget
         *        
         */
        function widget($args, $instance) {
            global $cs_plugin_options;
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $sub_title = empty($instance['sub_title']) ? '' : esc_attr($instance['sub_title']);
            $contact_view = empty($instance['contact_view']) ? '' : esc_html($instance['contact_view']);
            $image_url = empty($instance['image_url']) ? '' : esc_url($instance['image_url']);
            $telephone = empty($instance['telephone']) ? '' : esc_attr($instance['telephone']);
            $office_hours = empty($instance['office_hours']) ? '' : esc_html($instance['office_hours']);
            $email = empty($instance['email']) ? '' : esc_attr($instance['email']);
            $fb_url = empty($instance['fb_url']) ? '' : esc_url($instance['fb_url']);
            $tw_url = empty($instance['tw_url']) ? '' : esc_url($instance['tw_url']);
            $lk_url = empty($instance['lk_url']) ? '' : esc_url($instance['lk_url']);
            $gl_url = empty($instance['gl_url']) ? '' : esc_url($instance['gl_url']);
            $ig_url = empty($instance['ig_url']) ? '' : esc_url($instance['ig_url']);
            $yt_url = empty($instance['yt_url']) ? '' : esc_url($instance['yt_url']);
            echo CS_FUNCTIONS()->cs_special_chars($before_widget);

            $widget_class = 'contact-info';
            if ( $contact_view == 'view-2' ) {
                $widget_class = 'contact-info v2';
            }
            ?>
            <div class="<?php echo CS_FUNCTIONS()->cs_special_chars($widget_class) ?>">
                <?php
                if ( ! empty($sub_title) && $sub_title <> ' ' ) {
                    echo CS_FUNCTIONS()->cs_special_chars($before_title);
                    echo CS_FUNCTIONS()->cs_special_chars($sub_title);
                    echo CS_FUNCTIONS()->cs_special_chars($after_title);
                }
                ?>
                <div class="widgettext">
                    <?php
                    if ( $contact_view == 'view-2' ) {
                        if ( isset($image_url) && $image_url != '' ) {
                            ?>
                            <div class="logo">
                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                            </div>
                            <?php
                        }
                        ?>
                        <ul>
                            <?php
                            if ( $telephone != '' ) {
                                ?>
                                <li><span><i class="icon-phone6"></i></span><p><?php echo esc_html($telephone); ?></p></li>
                                <?php
                            }
                            if ( $email != '' ) {
                                ?>
                                <li><span><i class="icon-envelope4"></i></span><p><a href="<?php echo 'mailto:' . esc_html($email); ?>"><?php echo esc_html($email); ?></a></p></li>
                                <?php
                            }
                            if ( $title != '' ) {
                                ?>
                                <li><span><i class="icon-map-marker"></i></span><p><?php echo ($title); ?></p></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                        if ( $office_hours != '' ) {
                            ?>
                            <div class="office-time">
                                <span><strong><?php esc_html_e('office hours', 'jobhunt') ?></strong>
                                    <?php echo esc_html($office_hours); ?>
                                </span>
                            </div>
                            <?php
                        }
                        if ( $fb_url <> '' || $tw_url <> '' || $lk_url <> '' || $gl_url <> '' || $ig_url <> '' || $yt_url <> '' ) {
                            ?>
                            <ul class="social-media">
                                <?php if ( $fb_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($fb_url); ?>" data-original-title="<?php esc_html_e('facebook', 'jobhunt'); ?>"><i class="icon-facebook7"></i></a></li>
                                <?php } if ( $tw_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($tw_url); ?>" data-original-title="<?php esc_html_e('twitter', 'jobhunt'); ?>"><i class=" icon-twitter6"></i></a></li>
                                <?php } if ( $lk_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($lk_url); ?>" data-original-title="<?php esc_html_e('linkedin', 'jobhunt'); ?>"><i class="icon-linkedin2"></i></a></li>
                                <?php } if ( $gl_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($gl_url); ?>" data-original-title="<?php esc_html_e('google', 'jobhunt'); ?>"><i class="icon-google"></i></a></li>
                                <?php } if ( $ig_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($ig_url); ?>" data-original-title="<?php esc_html_e('instagram', 'jobhunt'); ?>"><i class="icon-instagram"></i></a></li>
                                <?php } if ( $yt_url <> '' ) { ?>
                                    <li><a href="<?php echo esc_url($yt_url); ?>" data-original-title="<?php esc_html_e('youtube', 'jobhunt'); ?>"><i class="icon-youtube"></i></a></li>
                                <?php } ?>
                            </ul>
                            <?php
                        }
                    } else {
                        if ( isset($image_url) && $image_url != '' ) {
                            ?>
                            <div class="logo">
                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                            </div>
                        <?php } ?>
                        <address>
                            <span> 
                                <?php
                                echo ($title);
                                '<br />';
                                esc_html__('Telephone: ', 'jobhunt');
                                echo esc_attr($telephone);
                                ?><br>
                                <?php
                                esc_html__('E-mail: ', 'jobhunt');
                                echo esc_attr($email);
                                ?>
                            </span>
                        </address>
                        <?php
                        if ( $office_hours != '' ) {
                            ?>
                            <div class="office-time">
                                <span><strong><?php esc_html_e('office hours', 'jobhunt') ?></strong>
                                    <?php echo esc_html($office_hours); ?>
                                </span>
                            </div>
                            <?php
                        }
                        ?>
                        <ul class="social-media">
                            <?php if ( $fb_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($fb_url); ?>" data-original-title="<?php esc_html_e('facebook', 'jobhunt'); ?>"><i class="icon-facebook7"></i></a></li>
                            <?php } if ( $tw_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($tw_url); ?>" data-original-title="<?php esc_html_e('twitter', 'jobhunt'); ?>"><i class=" icon-twitter6"></i></a></li>
                            <?php } if ( $lk_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($lk_url); ?>" data-original-title="<?php esc_html_e('linkedin', 'jobhunt'); ?>"><i class="icon-linkedin2"></i></a></li>
                            <?php } if ( $gl_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($gl_url); ?>" data-original-title="<?php esc_html_e('google', 'jobhunt'); ?>"><i class="icon-google"></i></a></li>
                            <?php } if ( $ig_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($ig_url); ?>" data-original-title="<?php esc_html_e('instagram', 'jobhunt'); ?>"><i class="icon-instagram"></i></a></li>
                            <?php } if ( $yt_url <> '' ) { ?>
                                <li><a href="<?php echo esc_url($yt_url); ?>" data-original-title="<?php esc_html_e('youtube', 'jobhunt'); ?>"><i class="icon-youtube"></i></a></li>
                                    <?php } ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            echo CS_FUNCTIONS()->cs_special_chars($after_widget);
        }

    }

}
add_action('widgets_init', create_function('', 'return register_widget("contactinfo");'));
