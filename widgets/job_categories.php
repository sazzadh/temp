<?php
/**
 * @Jobs Categories widget Class
 *
 */
if ( ! class_exists('job_categories') ) {

    class job_categories extends WP_Widget {

        /**
         * Start Function how to create Categories Module
         */
        public function __construct() {
            parent::__construct(
                    'job_categories', // Base ID
                    esc_html__('CS : Jobs Categories', 'jobhunt'), // Name
                    array( 'classname' => 'category-widget fancy', 'description' => esc_html__('Display Jobs Categories.', 'jobhunt'), )
            );
        }

        /**
         * Start Function how to create Jobs Categories html form
         */
        function form($instance) {
            global $cs_theme_form_fields, $cs_html_fields, $cs_theme_html_fields;
            $instance = wp_parse_args((array) $instance, array( 'title' => '' ));
            $title = $instance['title'];
            $select_category = isset($instance['select_category']) ? CS_FUNCTIONS()->cs_special_chars($instance['select_category']) : '';
            $showcount = isset($instance['showcount']) ? esc_attr($instance['showcount']) : '';

            $cs_opt_array = array(
                'name' => esc_html__('Title', 'jobhunt'),
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'field_params' => array(
                    'std' => esc_attr($title),
                    'id' => '',
                    'classes' => '',
                    'cust_id' => CS_FUNCTIONS()->cs_special_chars($this->get_field_id('title')),
                    'cust_name' => CS_FUNCTIONS()->cs_special_chars($this->get_field_name('title')),
                    'return' => true,
                    'required' => false
                ),
            );
            $cs_html_fields->cs_text_field($cs_opt_array);

            $a_options = array();
            $a_options = cs_show_all_cats('', '', cs_allow_special_char($this->get_field_id('select_category')), "specialisms", true);

			$select_specialisms_label = esc_html__('Select specialism:', 'jobhunt');
			$select_specialisms_label = apply_filters( 'jobhunt_replace_job_specialisms_to_job_categories', $select_specialisms_label );
			
            $cs_opt_array = array(
                'name' => $select_specialisms_label,
                'desc' => '',
                'hint_text' => '',
                'echo' => true,
                'multi' => true,
                'field_params' => array(
                    'std' => $select_category,
                    'cust_name' => $this->get_field_name('select_category') . '[]',
                    'cust_id' => $this->get_field_id('select_category'),
                    'id' => '',
                    'classes' => 'upcoming',
                    'options' => $a_options,
                    'return' => true,
                ),
            );

            $cs_html_fields->cs_select_field($cs_opt_array);
        }

        /**
         * Start Function how Categories update form data
         *        
         */
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = $new_instance['title'];
            $instance['select_category'] = $new_instance['select_category'];
            $instance['showcount'] = $new_instance['showcount'];
            return $instance;
        }

        /**
         * Start Function how Display Jobs Categories widget
         *        
         */
        function widget($args, $instance) {
            global $cs_plugin_options;
            extract($args, EXTR_SKIP);
            $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            $title = htmlspecialchars_decode(stripslashes($title));
            $select_category = empty($instance['select_category']) ? ' ' : CS_FUNCTIONS()->cs_special_chars($instance['select_category']);
            $showcount = absint($instance['showcount']);
            echo CS_FUNCTIONS()->cs_special_chars($before_widget);
            if ( ! empty($title) && $title <> ' ' ) {
                echo CS_FUNCTIONS()->cs_special_chars($before_title);
                echo CS_FUNCTIONS()->cs_special_chars($title);
                echo CS_FUNCTIONS()->cs_special_chars($after_title);
            }
            if ( is_array($select_category) && sizeof($select_category) > 0 ) {
                echo '<ul class="category-list">';
                foreach ( $select_category as $cs_cat ) {
                    if ( ! empty($cs_cat) ) {
                        $term = get_term_by('slug', $cs_cat, 'specialisms');
                        if ( is_object($term) ) {
                            $term_id = $term->term_id;
                            $cat_meta = get_term_meta($term_id, "spec_meta_data", true);
                            $cat_img = isset( $cat_meta['img'] )? $cat_meta['img'] : '';
                            $term_count = $term->count;
                            ?>
                            <li>
                                <figure class="effect-julia">
                                    <?php if( $cat_img != '' ){ ?>
                                        <img alt="" src="<?php echo esc_url($cat_img) ?>">
                                    <?php } ?>
                                    <figcaption><?php echo CS_FUNCTIONS()->cs_special_chars($term->name) ?></figcaption>
                                </figure>
                                <div class="cs-text"> <span><?php printf(__('%s + Jobs', 'jobhunt'), $term_count) ?></span> <a href="<?php echo esc_url(get_term_link($term->slug, 'specialisms')) ?>"><?php esc_html_e('View All', 'jobhunt') ?> </a> </div>
                            </li>
                            <?php
                        }
                    }
                }
                echo '</ul>';
            }
            echo CS_FUNCTIONS()->cs_special_chars($after_widget);
        }

    }

}
add_action('widgets_init', create_function('', 'return register_widget("job_categories");'));
