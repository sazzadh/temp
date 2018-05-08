<?php
/**
 * Start Function how to 
 * Add User Image for Avatar
 */
if ( ! function_exists('cs_get_user_avatar') ) {

    function cs_get_user_avatar($size = 0, $cs_user_id = '') {
        if ( $cs_user_id != '' ) {
            $cs_user_avatars = get_the_author_meta('user_avatar_display', $cs_user_id);
            if ( is_array($cs_user_avatars) && isset($cs_user_avatars[$size]) ) {
                return $cs_user_avatars[$size];
            } else if ( ! is_array($cs_user_avatars) && $cs_user_avatars <> '' ) {
                return $cs_user_avatars;
            }
        }
    }

}

if ( ! function_exists('cs_front_change_password') ) {

    function cs_front_change_password() {
        global $current_user;
        $user = get_user_by('login', $current_user->user_login);
        $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
        $new_pass = isset($_POST['new_pass']) ? $_POST['new_pass'] : '';
        $confirm_pass = isset($_POST['confirm_pass']) ? $_POST['confirm_pass'] : '';
        if ( ! is_user_logged_in() ) {
            esc_html_e('Login again to change password.', 'jobhunt');
            die;
        }
        if ( $old_pass == '' || $new_pass == '' || $confirm_pass == '' ) {
            esc_html_e('Password field is empty.', 'jobhunt');
            die;
        }
        if ( $user && wp_check_password($old_pass, $user->data->user_pass, $user->ID) ) {
            if ( $new_pass !== $confirm_pass ) {
                esc_html_e('Mismatch Password fields.', 'jobhunt');
                die;
            } else {
                wp_set_password($new_pass, $user->ID);
                // Here is the magic:
                wp_cache_delete($current_user->ID, 'users');
                wp_cache_delete($current_user->user_login, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
                wp_logout();
                wp_signon(array( 'user_login' => $current_user->user_login, 'user_password' => $new_pass ));
                esc_html_e('Password Changed.', 'jobhunt');
                die;
            }
        } else {
            esc_html_e('Old Password is incorrect.', 'jobhunt');
            die;
        }
        esc_html_e('Password is incorrect.', 'jobhunt');
        die;
    }

    add_action('wp_ajax_cs_front_change_password', 'cs_front_change_password');
    add_action('wp_ajax_nopriv_cs_front_change_password', 'cs_front_change_password');
}

if ( ! function_exists('cs_header_cover_style') ) {

    function cs_header_cover_style($cs_user_page = '', $meta_cover_image = '', $default_size = '') {
        $cs_jobcareer_theme_options = get_option('cs_theme_options');
        $cs_sh_paddingtop = ( isset($cs_jobcareer_theme_options['cs_sh_paddingtop']) ) ? ' padding-top:' . $cs_jobcareer_theme_options['cs_sh_paddingtop'] . 'px;' : '';
        $cs_sh_paddingbottom = ( isset($cs_jobcareer_theme_options['cs_sh_paddingbottom']) ) ? ' padding-bottom:' . $cs_jobcareer_theme_options['cs_sh_paddingbottom'] . 'px;' : '';
        $page_subheader_color = ( isset($cs_jobcareer_theme_options['cs_sub_header_bg_color'])) ? $cs_jobcareer_theme_options['cs_sub_header_bg_color'] : '';
        $page_subheader_text_color = ( isset($cs_jobcareer_theme_options['cs_sub_header_text_color']) ) ? ' color:' . $cs_jobcareer_theme_options['cs_sub_header_text_color'] . ' !important;' : '';
        $cs_sub_header_default_h = isset($cs_jobcareer_theme_options['cs_sub_header_default_h']) ? $cs_jobcareer_theme_options['cs_sub_header_default_h'] : '';
        if ( $cs_user_page == 'candidate' ) {
            $header_banner_image = ( isset($cs_jobcareer_theme_options['cs_candidate_default_cover']) ) ? $cs_jobcareer_theme_options['cs_candidate_default_cover'] : '';
        } else {
            $header_banner_image = ( isset($cs_jobcareer_theme_options['cs_employer_default_cover']) ) ? $cs_jobcareer_theme_options['cs_employer_default_cover'] : '';
        }
        $page_subheader_parallax = ( isset($cs_jobcareer_theme_options['cs_parallax_bg_switch']) ) ? $cs_jobcareer_theme_options['cs_parallax_bg_switch'] : '';
        if ( $page_subheader_color ) {
            $subheader_style_elements = 'background: ' . $page_subheader_color . ';';
        } else {
            $subheader_style_elements = '';
        }
        $parallax_class = '';
        if ( isset($page_subheader_parallax) && (string) $page_subheader_parallax == 'on' ) {
            $parallax_class = 'parallex-bg';
        }
        if ( $meta_cover_image != '' ) {
            $header_banner_image = $meta_cover_image;
        }
        $cs_jobhunt_header_image_height = array();
        if ( $header_banner_image != '' ) {
            $cs_upload_dir = wp_upload_dir();
            $cs_upload_baseurl = isset($cs_upload_dir['baseurl']) ? $cs_upload_dir['baseurl'] . '/' : '';
            $cs_upload_dir = isset($cs_upload_dir['basedir']) ? $cs_upload_dir['basedir'] . '/' : '';
            if ( false !== strpos($header_banner_image, $cs_upload_baseurl) ) {
                $cs_upload_subdir_file = str_replace($cs_upload_baseurl, '', $header_banner_image);
            }
            $cs_images_dir = trailingslashit(wp_jobhunt::plugin_url()) . 'assets/images/';
            $cs_img_name = preg_replace('/^.+[\\\\\\/]/', '', $header_banner_image);
            if ( is_file($cs_upload_dir . $cs_img_name) || is_file($cs_images_dir . $cs_img_name) ) {
                if ( ini_get('allow_url_fopen') ) {
                    if ( $header_banner_image <> '' ) {
                        $cs_jobhunt_header_image_height = getimagesize($header_banner_image);
                    }
                }
            } else if ( isset($cs_upload_subdir_file) && is_file($cs_upload_dir . $cs_upload_subdir_file) ) {
                if ( ini_get('allow_url_fopen') ) {
                    if ( $header_banner_image <> '' ) {
                        $cs_file_response = wp_remote_get($header_banner_image);
                        if ( ! is_wp_error($cs_file_response) ) {
                            $cs_jobhunt_header_image_height = getimagesize($header_banner_image);
                        } else {
                            $imge_url = explode(site_url() . '/', $header_banner_image);
                            $upload_dir = get_home_path();
                            $relative_path = $upload_dir;
                            $image_name = end($imge_url);
                            $image_relative_path = $relative_path . end($imge_url);
                            if ( file_exists($image_relative_path) ) {
                                $cs_jobhunt_header_image_height = getimagesize($image_relative_path);
                            }
                        }
                    }
                }
            }
            $cs_jobhunt_header_image_heightt = '';
            if ( isset($cs_jobhunt_header_image_height) && $cs_jobhunt_header_image_height != '' && isset($cs_jobhunt_header_image_height[1]) ) {
                $cs_jobhunt_header_image_height = $cs_jobhunt_header_image_height[1] . 'px';
                $cs_jobhunt_header_image_heightt = ' min-height: ' . $cs_jobhunt_header_image_height . ' !important;';
            }
        } else {
            $cs_jobhunt_header_image_heightt = ' min-height: ' . $default_size . 'px !important;';
        }
        if ( $cs_sub_header_default_h != '' && $cs_sub_header_default_h >= 0 ) {
            $cs_jobhunt_header_image_heightt = ' min-height: ' . $cs_sub_header_default_h . 'px !important;';
        }
        if ( $header_banner_image != '' ) {
            if ( $page_subheader_parallax == 'on' ) {
                $parallaxStatus = 'no-repeat fixed';
            } else {
                $parallaxStatus = '';
            }
            if ( $page_subheader_parallax == 'on' ) {
                $header_banner_image = 'url(' . $header_banner_image . ') center top ' . $parallaxStatus . '';
                $subheader_style_elements = 'background: ' . $header_banner_image . ' ' . $page_subheader_color . ';' . ' background-size:cover;';
            } else {
                $header_banner_image = 'url(' . $header_banner_image . ') center top ' . $parallaxStatus . '';
                $subheader_style_elements = 'background: ' . $header_banner_image . ' ' . $page_subheader_color . ';';
            }
        }
        if ( $subheader_style_elements <> '' && $cs_jobhunt_header_image_heightt <> '' ) {
            $subheader_style_elements = $subheader_style_elements . $cs_jobhunt_header_image_heightt . $page_subheader_text_color . $cs_sh_paddingtop . $cs_sh_paddingbottom;
        } else {
            if ( $cs_jobhunt_header_image_heightt <> '' ) {
                $subheader_style_elements = $cs_jobhunt_header_image_heightt . $page_subheader_text_color . $cs_sh_paddingtop . $cs_sh_paddingbottom;
            } else {
                $subheader_style_elements = $page_subheader_text_color . $cs_sh_paddingtop . $cs_sh_paddingbottom;
            }
        }
        return array( $subheader_style_elements, $parallax_class );
    }

}

if ( ! function_exists('cs_author_role_template') ) {

    function cs_author_role_template($author_template = '') {

        $author = get_queried_object();
        $role = $author->roles[0];
        if ( $role == 'cs_employer' ) {
            $author_template = plugin_dir_path(__FILE__) . 'single_pages/single-employer.php';
        } else if ( $role == 'cs_candidate' ) {
            $author_template = plugin_dir_path(__FILE__) . 'single_pages/single-candidate.php';
        }
        return $author_template;
    }

    add_filter('author_template', 'cs_author_role_template');
}

if ( ! function_exists('cs_user_pagination') ) {

    function cs_user_pagination($total_pages = 1, $page = 1) {
        wp_reset_query();
        $query_string = $_SERVER['QUERY_STRING'];
        $base = get_permalink() . '?' . remove_query_arg('page_id_all', $query_string) . '%_%';
        $cs_pagination = paginate_links(array(
            'base' => $base, // the base URL, including query arg
            'format' => '&page_id_all=%#%', // this defines the query parameter that will be used, in this case "p"
            'prev_text' => '<i class="icon-angle-left"></i> ' . esc_html__('Previous', 'jobhunt'), // text for previous page
            'next_text' => esc_html__('Next', 'jobhunt') . ' <i class="icon-angle-right"></i>', // text for next page
            'total' => $total_pages, // the total number of pages we have
            'current' => $page, // the current page
            'end_size' => 1,
            'mid_size' => 2,
            'type' => 'array',
        ));
        $cs_pages = '';
        if ( is_array($cs_pagination) && sizeof($cs_pagination) > 0 ) {
            $cs_pages .= '<ul class="pagination">';
            foreach ( $cs_pagination as $cs_link ) {
                if ( strpos($cs_link, 'current') !== false ) {
                    $cs_pages .= '<li><a class="active">' . preg_replace("/[^0-9]/", "", $cs_link) . '</a></li>';
                } else {
                    $cs_pages .= '<li>' . $cs_link . '</li>';
                }
            }
            $cs_pages .= '</ul>';
        }
        echo force_balance_tags($cs_pages);
    }

}

if ( ! function_exists('cs_show_all_cats') ) {

    function cs_show_all_cats($parent, $separator, $selected = "", $taxonomy, $optional = '') {
        if ( $parent == "" ) {
            global $wpdb;
            $parent = 0;
        } else {
            $separator .= " &ndash; ";
        }
        $args = array(
            'parent' => $parent,
            'hide_empty' => 0,
            'taxonomy' => $taxonomy
        );
        $categories = get_categories($args);
        if ( $optional ) {
            $a_options = array();
            $a_options[''] = esc_html__("Please select..", 'jobhunt');
            foreach ( $categories as $category ) {
                $a_options[$category->slug] = $category->cat_name;
            }
            return $a_options;
        } else {
            foreach ( $categories as $category ) {
                ?>
                <option
                <?php
                if ( $selected == $category->slug ) {
                    echo "selected";
                }
                ?> value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_attr($separator . $category->cat_name); ?>
                </option>
                <?php
                cs_show_all_cats($category->term_id, $separator, $selected, $taxonomy);
            }
        }
    }

}

/**
 * Start Function how to Set Post Views
 */
if ( ! function_exists('cs_set_post_views') ) {

    function cs_set_post_views($postID) {
        if ( ! isset($_COOKIE["cs_count_views" . $postID]) ) {
            setcookie("cs_count_views" . $postID, 'post_view_count', current_time('timestamp') + 86400);
            $count_key = 'cs_count_views';
            $count = get_post_meta($postID, $count_key, true);
            if ( $count == '' ) {
                $count = 0;
                delete_post_meta($postID, $count_key);
                add_post_meta($postID, $count_key, '0');
            } else {
                $count ++;
                update_post_meta($postID, $count_key, $count);
            }
        }
    }

}

/**
 * Start Function how to Share Posts
 */
if ( ! function_exists('cs_addthis_script_init_method') ) {

    function cs_addthis_script_init_method() {
        wp_enqueue_script('cs_addthis', cs_server_protocol() . 's7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4412d954dccc64', '', '', true);
    }

}

/**
 * check whether file exsit or not
 */
if ( ! function_exists('cs_check_coverletter_exist') ) {

    function cs_check_coverletter_exist($file) {
        $is_exist = false;
        if ( isset($file) && $file <> "" ) {
            $file_headers = @get_headers($file);
            if ( $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
                $is_exist = false;
            } else {
                $is_exist = true;
            }
        }
        return $is_exist;
    }

}

/**
 * Start Function how to Get Current User ID
 */
if ( ! function_exists('cs_get_user_id') ) {

    function cs_get_user_id() {

        global $current_user;
        wp_get_current_user();
        return $current_user->ID;
    }

}

/**
 * Start Function how to Add your Favourite Dirpost
 */
if ( ! function_exists('cs_add_dirpost_favourite') ) {

    function cs_add_dirpost_favourite($cs_post_id = '') {
        global $post;

        $display_shortlist_button = 'yes';
        $display_shortlist_button = apply_filters('jobhunt_candidate_lists_shortlist_button', $display_shortlist_button);
        if ( $display_shortlist_button == 'yes' ) {
            $cs_emp_funs = new cs_employer_functions();
            $cs_post_id = isset($cs_post_id) ? $cs_post_id : '';
            if ( ! is_user_logged_in() || ! $cs_emp_funs->is_employer() ) {
                if ( is_user_logged_in() ) {
                    $user = cs_get_user_id();
                    $finded_result_list = cs_find_index_user_meta_list($cs_post_id, 'cs-user-jobs-wishlist', 'post_id', cs_get_user_id());
                    if ( isset($user) and $user <> '' and is_user_logged_in() ) {
                        if ( is_array($finded_result_list) && ! empty($finded_result_list) ) {
                            ?>
                            <a class="cs-add-wishlist tolbtn" data-toggle="tooltip" data-placement="top" data-original-title="<?php esc_html_e('Shortlist', 'jobhunt') ?>" onclick="cs_delete_from_favourite('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_post_id); ?>', 'post')" >
                                <i class="icon-heart6"></i><?php esc_html_e('Shortlisted', 'jobhunt'); ?>
                            </a>
                            <?php
                        } else {
                            ?>
                            <a class="cs-add-wishlist tolbtn" onclick="cs_addto_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_post_id); ?>', 'post')" data-placement="top" data-toggle="tooltip" data-original-title="<?php esc_html_e('Shortlisted', 'jobhunt') ?>">
                                <i class="icon-heart-o"></i><?php esc_html_e('Shortlist', 'jobhunt'); ?>
                            </a>
                            <?php
                        }
                    } else {
                        ?>
                        <a class="cs-add-wishlist tolbtn" onclick="cs_addto_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_post_id); ?>', 'post')" data-placement="top" data-toggle="tooltip" data-original-title="<?php esc_html_e('Shortlisted', 'jobhunt') ?>"> 
                            <i class="icon-heart-o"></i><?php esc_html_e('Shortlist', 'jobhunt'); ?>
                        </a>	
                        <?php
                    }
                } else {
                    ?>
                    <a href="javascript:void(0);" class="cs-add-wishlist" onclick="trigger_func('#btn-header-main-login');"><i class="icon-heart-o"></i><?php esc_html_e('Shortlist', 'jobhunt'); ?> </a>
                    <?php
                }
            }
        }
    }

}

/**
 * Start Function how to Add User Meta
 */
if ( ! function_exists('cs_addto_usermeta') ) {

    function cs_addto_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                ?>
                <i class="icon-heart6"></i><?php esc_html_e('Shortlisted', 'jobhunt'); ?>
                <?php
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_addto_usermeta", "cs_addto_usermeta");
    add_action("wp_ajax_nopriv_cs_addto_usermeta", "cs_addto_usermeta");
}

/**
 * Start Function how to Add User Apply Meta For Job
 */
if ( ! function_exists('cs_get_user_jobapply_meta') ) {

    function cs_get_user_jobapply_meta($user = "") {
        if ( ! empty($user) ) {
            $userdata = get_user_by('login', $user);
            $user_id = $userdata->ID;
            return get_user_meta($user_id, 'cs-jobs-applied', true);
        } else {
            return get_user_meta(cs_get_user_id(), 'cs-jobs-applied', true);
        }
    }

}

/**
 * Start Function how to Update User Apply Meta For Job
 */
if ( ! function_exists('cs_update_user_jobapply_meta') ) {

    function cs_update_user_jobapply_meta($arr) {
        return update_user_meta(cs_get_user_id(), 'cs-jobs-applied', $arr);
    }

}

/**
 * Start Function how to Delete Favourites User 
 */
if ( ! function_exists('cs_delete_from_favourite') ) {

    function cs_delete_from_favourite() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                echo '<i class="icon-heart-o"></i>';
                esc_html_e('Shortlist', 'jobhunt');
            } else {
                esc_html_e('You are not authorised', 'jobhunt');
            }
        }
        die();
    }

    add_action("wp_ajax_cs_delete_from_favourite", "cs_delete_from_favourite");
    add_action("wp_ajax_nopriv_cs_delete_from_favourite", "cs_delete_from_favourite");
}

/**
 * Start Function how to Delete User From Wishlist 
 */
if ( ! function_exists('cs_delete_wishlist') ) {

    function cs_delete_wishlist() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            // check this record is in his list
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                esc_html_e('Removed From Favourite', 'jobhunt');
            } else {
                esc_html_e('You are not authorised', 'jobhunt');
            }
        }
        die();
    }

    add_action("wp_ajax_cs_delete_wishlist", "cs_delete_wishlist");
    add_action("wp_ajax_nopriv_cs_delete_wishlist", "cs_delete_wishlist");
}
add_filter('wp_mail_from_name', 'cs_wp_mail_from_name');

function cs_wp_mail_from_name($original_email_from) {
    $options = get_option('cs_plugin_options');
    // Don't configure for SMTP if no host is provided.
    if ( empty($options['cs_use_smtp_mail']) || $options['cs_use_smtp_mail'] != 'on' || $options['cs_sender_name'] == '' ) {
        return get_bloginfo('name');
    } else {
        return $options['cs_sender_name'];
    }
}

/*
  candidate contact form
 */
if ( ! function_exists('ajaxcontact_send_mail_cand') ) {

    function ajaxcontact_send_mail_cand() {
        $results = '';
        $error = 0;
        $error_result = 0;
        $message = "";
        $name = '';
        $email = '';
        $phone = '';
        $contents = '';
        $candidateid = '';
        if ( isset($_POST['ajaxcontactname']) ) {
            $name = $_POST['ajaxcontactname'];
        }
        if ( isset($_POST['ajaxcontactemail']) ) {
            $email = $_POST['ajaxcontactemail'];
        }
        if ( isset($_POST['ajaxcontactphone']) ) {
            $phone = $_POST['ajaxcontactphone'];
        }
        if ( isset($_POST['ajaxcontactcontents']) ) {
            $contents = $_POST['ajaxcontactcontents'];
        }
        if ( isset($_POST['candidateid']) ) {
            $candidateid = $_POST['candidateid'];   // user id for candidate
        }
        if ( isset($_POST['cs_terms_page']) ) {
            $cs_terms_page = 'on';
            $cs_contact_terms = isset($_POST['cs_contact_terms']) ? $_POST['cs_contact_terms'] : '';
        } else {
            $cs_terms_page = 'off';
            $cs_contact_terms = '';
        }
        $subject = esc_html__("Employer Contact from job hunt", "jobhunt");
        $admin_email_from = get_option('admin_email');
        // getting candidate email address
        // getting email address from user table
        $cs_user_id = $candidateid;
        $user_info = get_userdata($cs_user_id);
        $admin_email = '';
        if ( isset($user_info->user_email) && $user_info->user_email <> '' ) {
            $admin_email = $user_info->user_email;
        }
        if ( $admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL) ) {
            if ( strlen($name) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( strlen($email) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } elseif ( ! filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                $results = " '" . $email . "' " . esc_html__('email address is not valid.', 'jobhunt');
                $error = 1;
                $error_result = 1;
            } else if ( strlen($contents) == 0 || strlen($contents) < 5 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( $cs_terms_page == 'on' && $cs_contact_terms != 'on' ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'jobhunt') . "</span>";
                $error = 1;
                $error_result = 1;
            } else if ( isset($_POST['captcha_id']) && $_POST['captcha_id'] != '' && $_POST['captcha_id'] != 'undefined' ) {
                if ( cs_captcha_verify(true) ) {
                    $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate', 'jobhunt') . "</span>";
                    $error = 1;
                    $error_result = 1;
                }
            }
            if ( $error == 0 ) {
                $form_array = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $contents,
                    'candidate_email' => $admin_email
                );
                do_action('jobhunt_employer_contact_candidate', $form_array);
                if ( class_exists('jobhunt_employer_contact_candidate_email_template') && isset(jobhunt_employer_contact_candidate_email_template::$is_email_sent1) ) {
                    $error = 0;
                    $error_result = 0;
                    $results = '';
                    $results .= "&nbsp; <span style=\"color: #060;\">";
                    $results .= esc_html__('Your inquiry has been sent User will contact you shortly', 'jobhunt');
                    $results .= '|';
                    $results .= $error_result;
                    $results .= '|';
                    $results .= '</span>';
                } else {
                    $error = 1;
                    $error_result = 1;
                    $results = '';
                    $results .= "&nbsp; <span style=\"color: #ff0000;\">*";
                    $results .= esc_html__('The mail could not be sent due to some resons, Please try again', 'jobhunt');
                    $results .= '</span>';
                }
                $args = array(
                    'to' => $admin_email,
                    'subject' => $subject,
                    'message' => $template,
                    'class_obj' => $obj_template,
                );
            }
        } else {
            $results = "&nbsp; <span style=\"color: #ff0000;\">*" . esc_html__('The profile email does not exist, Please try later', 'jobhunt') . "</span>";
            $error = 1;
            $error_result = 1;
        }
        if ( $error_result == 1 ) {
            $data = 1;
            $message = $results;
            die($message);
        } else {
            $data = 0;
            $message = $results;
            die($message);
        }
    }

    add_action('wp_ajax_nopriv_ajaxcontact_send_mail_cand', 'ajaxcontact_send_mail_cand');
    add_action('wp_ajax_ajaxcontact_send_mail_cand', 'ajaxcontact_send_mail_cand');
}

/**
 * Start Function how to send mail using Ajax
 */
if ( ! function_exists('ajaxcontact_send_mail') ) {

    function ajaxcontact_send_mail() {

        $results = '';
        $error = 0;
        $error_result = 0;
        $message = "";
        $name = '';
        $email = '';
        $phone = '';
        $contents = '';
        $candidateid = '';
        if ( isset($_POST['cs_ajaxcontactname']) ) {
            $name = $_POST['cs_ajaxcontactname'];
        }
        if ( isset($_POST['cs_ajaxcontactemail']) ) {
            $email = $_POST['cs_ajaxcontactemail'];
        }
        if ( isset($_POST['cs_ajaxcontactphone']) ) {
            $phone = $_POST['cs_ajaxcontactphone'];
        }
        if ( isset($_POST['cs_ajaxcontactcontents']) ) {
            $contents = $_POST['cs_ajaxcontactcontents'];
        }
        if ( $name == '' ) {
            if ( isset($_POST['ajaxcontactname']) ) {
                $name = $_POST['ajaxcontactname'];
            }
        }
        if ( $email == '' ) {
            if ( isset($_POST['ajaxcontactemail']) ) {
                $email = $_POST['ajaxcontactemail'];
            }
        }
        if ( $phone == '' ) {
            if ( isset($_POST['ajaxcontactphone']) ) {
                $phone = $_POST['ajaxcontactphone'];
            }
        }
        if ( $contents == '' ) {
            if ( isset($_POST['ajaxcontactcontents']) ) {
                $contents = $_POST['ajaxcontactcontents'];
            }
        }
        if ( isset($_POST['candidateid']) ) {
            $candidateid = $_POST['candidateid'];   // user id for candidate
        }
        if ( isset($_POST['cs_terms_page']) ) {
            $cs_terms_page = 'on';
            $cs_contact_terms = isset($_POST['cs_contact_terms']) ? $_POST['cs_contact_terms'] : '';
        } else {
            $cs_terms_page = 'off';
            $cs_contact_terms = '';
        }
        $subject = esc_html__("Employer Contact from job hunt", "jobhunt");
        $admin_email_from = get_option('admin_email');
        // getting candidate email address
        // getting email address from user table
        $cs_user_id = $candidateid;
        $user_info = get_userdata($cs_user_id);
        $admin_email = '';
        if ( isset($user_info->user_email) && $user_info->user_email <> '' ) {
            $admin_email = $user_info->user_email;
        }
        if ( $admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL) ) {
            if ( strlen($name) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( strlen($email) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } elseif ( ! filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                $results = " '" . $email . "' " . esc_html__('email address is not valid.', 'jobhunt');
                $error = 1;
                $error_result = 1;
            } else if ( strlen($contents) == 0 || strlen($contents) < 50 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( $cs_terms_page == 'on' && $cs_contact_terms != 'on' ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'jobhunt') . "</span>";
                $error = 1;
                $error_result = 1;
            } else if ( isset($_POST['captcha_id']) && $_POST['captcha_id'] != '' && $_POST['captcha_id'] != 'undefined' ) {

                if ( cs_captcha_verify(true) ) {
                    $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate', 'jobhunt') . "</span>";
                    $error = 1;
                    $error_result = 1;
                }
            }
            if ( $error == 0 ) {
                $form_args = array( 'name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $contents, 'candidate_email' => $admin_email );
                do_action('jobhunt_employer_contact_candidate', $form_args);
                if ( class_exists('jobhunt_employer_contact_candidate_email_template') && isset(jobhunt_employer_contact_candidate_email_template::$is_email_sent1) ) {
                    $error = 0;
                    $error_result = 0;
                    $results = "&nbsp; <span style=\"color: #060;\">" . esc_html__("Your inquiry has been sent User will contact you shortly", "jobhunt") . "</span>";
                } else {
                    $error = 1;
                    $error_result = 1;
                    $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__("The mail could not be sent due to some resons, Please try again", "jobhunt") . "</span>";
                }
            }
        } else {
            $results = "&nbsp; <span style=\"color: #ff0000;\">*" . esc_html__('The profile email does not exist, Please try later', 'jobhunt') . "</span>";
            $error = 1;
            $error_result = 1;
        }
        if ( $error_result == 1 ) {
            $data = 1;
            $message = $results;
            die($message);
        } else {
            $data = 0;
            $message = $results;
            die($message);
        }
    }

    // creating Ajax call for WordPress
    add_action('wp_ajax_nopriv_ajaxcontact_send_mail', 'ajaxcontact_send_mail');
    add_action('wp_ajax_ajaxcontact_send_mail', 'ajaxcontact_send_mail');
}

/**
 * Start Function how to send Employeer Contact mail using Ajax
 */
if ( ! function_exists('ajaxcontact_employer_send_mail') ) {

    function ajaxcontact_employer_send_mail() {
        global $cs_plugin_options;
        $results = '';
        $message = "";
        $error = 0;
        $name = '';
        $email = '';
        $phone = '';
        $employerid_contactuscheckbox = '';
        $phone = '';
        $messgae = '';
        $error_result = 0;
        $contents = '';
        $employerid = '';
        $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';
        if ( isset($_POST['ajaxcontactname']) ) {
            $name = $_POST['ajaxcontactname'];
        }
        if ( isset($_POST['employerid_contactuscheckbox']) ) {
            $employerid_contactuscheckbox = $_POST['employerid_contactuscheckbox'];
        }
        if ( isset($_POST['ajaxcontactemail']) ) {
            $email = $_POST['ajaxcontactemail'];
        }if ( isset($_POST['ajaxcontactphone']) ) {
            $phone = $_POST['ajaxcontactphone'];
        }if ( isset($_POST['ajaxcontactcontents']) ) {
            $contents = $_POST['ajaxcontactcontents'];
            $messgae = $_POST['ajaxcontactcontents'];
        }if ( isset($_POST['employerid']) ) {
            $employerid = $_POST['employerid'];
        }
        if ( isset($_POST['cs_terms_page']) ) {
            $cs_terms_page = 'on';
            $cs_contact_terms = isset($_POST['cs_contact_terms']) ? $_POST['cs_contact_terms'] : '';
        } else {
            $cs_terms_page = 'off';
            $cs_contact_terms = '';
        }
        // user id for candidate
        $subject = esc_html__("Candidate Contact from job hunt", "jobhunt");
        $admin_email_from = get_option('admin_email');
        // getting employer email address
        $cs_user_id = $employerid;
        $user_info = get_userdata($cs_user_id);
        $admin_email = '';
        if ( isset($user_info->user_email) ) {
            $admin_email = $user_info->user_email;
        }
        if ( $admin_email != '' && filter_var($admin_email, FILTER_VALIDATE_EMAIL) ) {
            if ( strlen($name) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter name.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( strlen($email) == 0 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Please enter email.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( ! filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                $results = "&nbsp; '<span style=\"color: #ff0000;\">" . $email . "' " . esc_html__('email address is not valid.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( strlen($contents) == 0 || strlen($contents) < 50 ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Message should have more than 50 characters', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            } else if ( $cs_terms_page == 'on' && $cs_contact_terms != 'on' ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('You should accept Terms and Conditions.', 'jobhunt') . "</span>";
                $error = 1;
                $error_result = 1;
            } else if ( cs_captcha_verify(true) ) {
                $results = "&nbsp; <span style=\"color: #ff0000;\">" . esc_html__('Captcha should must be validate.', 'jobhunt') . "</span><br/>";
                $error = 1;
                $error_result = 1;
            }
            if ( $error == 0 ) {
                $email_template_atts = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'message' => $messgae,
                    'employer_email' => $admin_email,
                );
                do_action('jobhunt_candidate_contact_email', $email_template_atts);
                if ( class_exists('jobhunt_candidate_contact_email_template') && isset(jobhunt_candidate_contact_email_template::$is_email_sent1) ) {
                    $error = 0;
                    $error_result = 0;
                    $results = "&nbsp; <span style=\"color: #060;\">" . esc_html__('Your inquiry has been sent User will contact you shortly', 'jobhunt') . "</span>";
                } else {
                    $error = 1;
                    $error_result = 1;
                    $results = "&nbsp; <span style=\"color: #ff0000;\">**" . esc_html__('Something Wrong, Please try later.', 'jobhunt') . "</span> ";
                }
            }
        } else {
            $results = "&nbsp; <span style=\"color: #ff0000;\">**" . esc_html__('The profile email does not exist, Please try later.', 'jobhunt') . "</span> ";
            $error = 1;
            $error_result = 1;
        }
        if ( $error_result == 1 ) {
            $data = 1;
            $message = $results . '|' . $data;
            die($message);
        } else {
            $data = 0;
            $message = $results . '|' . $data;
            die($message);
        }
    }

    // creating Ajax call for WordPress
    add_action('wp_ajax_nopriv_ajaxcontact_employer_send_mail', 'ajaxcontact_employer_send_mail');
    add_action('wp_ajax_ajaxcontact_employer_send_mail', 'ajaxcontact_employer_send_mail');
}

/**
 * @time elapsed string
 */
if ( ! function_exists('cs_time_elapsed_string') ) {

    function cs_time_elapsed_string($ptime) {
        return human_time_diff($ptime, current_time('timestamp')) . ' ' . esc_html__('ago', 'jobhunt');
    }

}

/**
 * Start Function how to create Custom Pagination
 */
if ( ! function_exists('cs_pagination') ) {

    function cs_pagination($total_records, $per_page, $qrystr = '', $show_pagination = 'Show Pagination', $query_string_variable = 'page_id_all', $atts = array()) {

        $active_plugin = false;
        $active_plugin = apply_filters('jobhunt_email_template_depedency', $active_plugin);
        if ( $active_plugin ) {
            $html = '';
            $html = apply_filters('jobhunt_pagination_frontend', $total_records, $per_page, $qrystr, $show_pagination, $query_string_variable);
            return $html;
        }


        if ( $show_pagination <> 'Show Pagination' ) {
            return;
        } else if ( $total_records < $per_page ) {
            return;
        } else {
            $html = '';
            $dot_pre = '';
            $dot_more = '';
            $total_page = 0;

            $cs_job_counter = (isset($atts['cs_job_counter']) && $atts['cs_job_counter'] != '') ? $atts['cs_job_counter'] : '';
            if ( $cs_job_counter != '' ) {
                $cs_job_counter = '&cs_job_counter=' . $cs_job_counter;
            }

            if ( $per_page <> 0 )
                $total_page = ceil($total_records / $per_page);
            $page_id_all = 1;
            if ( isset($_GET[$query_string_variable]) && $_GET[$query_string_variable] != '' ) {
                $page_id_all = $_GET[$query_string_variable];
            }

            if ( isset($_GET['cs_job_counter']) && isset($atts['cs_job_counter']) && $atts['cs_job_counter'] != '' && $_GET['cs_job_counter'] != $atts['cs_job_counter'] ) {
                $page_id_all = 1;
            }

            $loop_start = $page_id_all - 2;
            $loop_end = $page_id_all + 2;
            if ( $page_id_all < 3 ) {
                $loop_start = 1;
                if ( $total_page < 5 )
                    $loop_end = $total_page;
                else
                    $loop_end = 5;
            }
            else if ( $page_id_all >= $total_page - 1 ) {
                if ( $total_page < 5 )
                    $loop_start = 1;
                else
                    $loop_start = $total_page - 4;
                $loop_end = $total_page;
            }
            $html .= "<ul class='pagination'>";
            if ( $page_id_all > 1 ) {

                $html .= "<li><a href='?$query_string_variable=" . ($page_id_all - 1) . "$qrystr$cs_job_counter' aria-label='" . esc_html__('Previous', 'jobhunt') . "' ><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'jobhunt') . " </span></a></li>";
            } else {
                $html .= "<li><a aria-label='" . esc_html__('Previous', 'jobhunt') . "'><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'jobhunt') . "</span></a></li>";
            }
            if ( $page_id_all > 3 and $total_page > 5 )
                $html .= "<li><a href='?$query_string_variable=1$qrystr$cs_job_counter'>1</a></li>";
            if ( $page_id_all > 4 and $total_page > 6 )
                $html .= "<li> <a>. . .</a> </li>";
            if ( $total_page > 1 ) {
                for ( $i = $loop_start; $i <= $loop_end; $i ++  ) {
                    if ( $i <> $page_id_all )
                        $html .= "<li><a href='?$query_string_variable=$i$qrystr$cs_job_counter'>" . $i . "</a></li>";
                    else
                        $html .= "<li><a class='active'>" . $i . "</a></li>";
                }
            }
            if ( $loop_end <> $total_page and $loop_end <> $total_page - 1 )
                $html .= "<li> <a>. . .</a> </li>";
            if ( $loop_end <> $total_page )
                $html .= "<li><a href='?$query_string_variable=$total_page$qrystr$cs_job_counter'>$total_page</a></li>";
            if ( $per_page > 0 and $page_id_all < $total_records / $per_page ) {
                $html .= "<li><a aria-label='" . esc_html__('Next', 'jobhunt') . "' href='?$query_string_variable=" . ($page_id_all + 1) . "$qrystr$cs_job_counter' ><span aria-hidden='true'>" . esc_html__('Next', 'jobhunt') . " <i class='icon-angle-right'></i></span></a></li>";
            } else {
                $html .= "<li><a aria-label='" . esc_html__('Next', 'jobhunt') . "'><span aria-hidden='true'>" . esc_html__('Next', 'jobhunt') . " <i class='icon-angle-right'></i></span></a></li>";
            }
            $html .= "</ul>";
            return $html;
        }
    }

}

/**
 * Start Function how to create Custom Pagination using Ajax
 */
if ( ! function_exists('cs_ajax_pagination') ) {

    function cs_ajax_pagination($total_records, $per_page, $tab, $type, $uid, $pack_array) {
        $admin_url = esc_url(admin_url('admin-ajax.php'));
        if ( $total_records < $per_page ) {
            return;
        } else {
            $html = '';
            $dot_pre = '';
            $dot_more = '';
            $total_page = 0;
            if ( $per_page <> 0 )
                $total_page = ceil($total_records / $per_page);
            $page_id_all = 0;
            if ( isset($_REQUEST['page_id_all']) && $_REQUEST['page_id_all'] != '' ) {
                $page_id_all = $_REQUEST['page_id_all'];
            }
            $loop_start = $page_id_all - 2;
            $loop_end = $page_id_all + 2;
            if ( $page_id_all < 3 ) {
                $loop_start = 1;
                if ( $total_page < 5 )
                    $loop_end = $total_page;
                else
                    $loop_end = 5;
            }
            else if ( $page_id_all >= $total_page - 1 ) {
                if ( $total_page < 5 )
                    $loop_start = 1;
                else
                    $loop_start = $total_page - 4;
                $loop_end = $total_page;
            }
            $html .= "<ul class='pagination'>";
            if ( $page_id_all > 1 ) {
                $html .= "<li><a onclick=\"cs_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($page_id_all - 1) . "')\" href='javascript:void(0);' aria-label='" . esc_html__('Previous', 'jobhunt') . "' ><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'jobhunt') . " </span></a></li>";
            } else {

                $html .= "<li><a aria-label='" . esc_html__('Previous', 'jobhunt') . "'><span aria-hidden='true'><i class='icon-angle-left'></i> " . esc_html__('Previous', 'jobhunt') . "</span></a></li>";
            }
            if ( $page_id_all > 3 and $total_page > 5 )
                $html .= "<li><a href='javascript:void(0);' onclick=\"cs_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '1')\">1</a></li>";
            if ( $page_id_all > 4 and $total_page > 6 )
                $html .= "<li> <a>. . .</a> </li>";
            if ( $total_page > 1 ) {
                for ( $i = $loop_start; $i <= $loop_end; $i ++  ) {
                    if ( $i <> $page_id_all )
                        $html .= "<li><a href='javascript:void(0);' onclick=\"cs_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($i) . "')\" >" . $i . "</a></li>";
                    else
                        $html .= "<li><a class='active'>" . $i . "</a></li>";
                }
            }
            if ( $loop_end <> $total_page and $loop_end <> $total_page - 1 )
                $html .= "<li> <a>. . .</a> </li>";
            if ( $loop_end <> $total_page )
                $html .= "<li><a href='javascript:void(0);' onclick=\"cs_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "', '" . ($total_page) . "')\">$total_page</a></li>";
            if ( $per_page > 0 and $page_id_all < $total_records / $per_page ) {
                $html .= "<li><a href='javascript:void(0);' aria-label='" . esc_html__('Next', 'jobhunt') . "' onclick=\"cs_dashboard_tab_load('" . $tab . "', '" . $type . "', '" . $admin_url . "', '" . $uid . "', '" . $pack_array . "','" . ($page_id_all + 1) . "')\" ><span aria-hidden='true'>" . esc_html__('Next', 'jobhunt') . " <i class='icon-angle-right'></i></span></a></li>";
            } else {
                $html .= "<li><a href='javascript:void(0);' aria-label='" . esc_html__('Next', 'jobhunt') . "'><span aria-hidden='true'>" . esc_html__('Next', 'jobhunt') . " <i class='icon-angle-right'></i></span></a></li>";
            }
            $html .= "</ul>";
            return $html;
        }
    }

}

/**
 * Start Function how to Add Job User Meta
 */
if ( ! function_exists('cs_addjob_to_usermeta') ) {

    function cs_addjob_to_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            $view_style = isset($_POST['view']) ? $_POST['view'] : '';
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                if ( $view_style == 'boxed' ) {
                    ?>
                    <i class="icon-star2"></i> 
                <?php } else { ?>
                    <i class="icon-heart6"></i>
                    <?php
                }
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_addjob_to_usermeta", "cs_addjob_to_usermeta");
    add_action("wp_ajax_nopriv_cs_addjob_to_usermeta", "cs_addjob_to_usermeta");
}


if ( ! function_exists('cs_addjob_to_user') ) {

    function cs_addjob_to_user() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                ?>
                <i class="icon-heart6"></i>
                <?php
                esc_html_e('Shortlisted', 'jobhunt');
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_addjob_to_user", "cs_addjob_to_user");
    add_action("wp_ajax_nopriv_cs_addjob_to_user", "cs_addjob_to_user");
}


/**
 * Start Function how to Remove Job from User Meta
 */
if ( ! function_exists('cs_removejob_to_usermeta') ) {

    function cs_removejob_to_usermeta() {
        $user = cs_get_user_id();

        if ( isset($user) && $user <> '' ) {
            $view_style = isset($_POST['view']) ? $_POST['view'] : '';
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                if ( $view_style == 'boxed' ) {
                    echo '<i class="icon-star-o"></i>';
                } else {
                    echo '<i class="icon-heart7"></i>';
                }
            } else {
                esc_html_e('You are not authorised', 'jobhunt');
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_removejob_to_usermeta", "cs_removejob_to_usermeta");
    add_action("wp_ajax_nopriv_cs_removejob_to_usermeta", "cs_removejob_to_usermeta");
}

if ( ! function_exists('cs_removejob_to_user') ) {

    function cs_removejob_to_user() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-wishlist', $user);
                echo '<i class="icon-heart7"></i>';
                esc_html_e('Shortlist', 'jobhunt');
            } else {
                esc_html_e('You are not authorised', 'jobhunt');
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_removejob_to_user", "cs_removejob_to_user");
    add_action("wp_ajax_nopriv_cs_removejob_to_user", "cs_removejob_to_user");
}

/**
 * Start Function how to Apply for job in User Meta without Login
 */
if ( ! function_exists('cs_add_applied_job_withoutlogin_to_usermeta') ) {

    function cs_add_applied_job_withoutlogin_to_usermeta() {
        global $cs_plugin_options;
        $response = array();
        $cs_user_apply_job = isset($cs_plugin_options['cs_user_apply_job']) ? $cs_plugin_options['cs_user_apply_job'] : '';
        $package_apply_check = true;
        if ( ! is_user_logged_in() ) {
            $package_apply_check = false;
        }
        if ( (isset($_POST['post_id']) && $_POST['post_id'] <> '' ) ) {
            $job_expired = cs_check_expire_job($_POST['post_id']);
            if ( $job_expired ) {
                $response['status'] = 0;
                $response['msg'] = sprintf(esc_html__("You can't apply because this job has been expired.", 'jobhunt'));
                echo json_encode($response);
                die;
            }
        }
        if ( $cs_user_apply_job == 'on' && $package_apply_check ) {
            $cs_memberhsip_packages_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';
            $cs_packages_ids = array();
            if ( is_array($cs_memberhsip_packages_options) && sizeof($cs_memberhsip_packages_options) > 0 ) {
                foreach ( $cs_memberhsip_packages_options as $cs_memberhsip_package ) {
                    $cs_packages_ids[] = $cs_memberhsip_package['membership_pkg_id'];
                }
            }
            $cs_candidate_func = new cs_candidate_fnctions();
            $cs_package_check = $cs_candidate_func->cs_is_membership_pkg_subscribed($cs_packages_ids);

            if ( $cs_package_check['cs_trans_count'] == 0 ) {
                $response['status'] = 0;
                $response['count'] = $cs_package_check['cs_trans_count'];
                $response['msg'] = sprintf(esc_html__("Please subscribe a package to apply.", 'jobhunt'));
                echo json_encode($response);
                die;
            }
        } else {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $cover_letter = $_POST['cover_letter'];
            if ( username_exists($fullname) == null && email_exists($email) == false ) {

                $parts = explode("@", $email); // get username from email id.
                $username = $parts[0];

                $user_id = wp_create_user($username, $phone, $email);
                $user = get_user_by('id', $user_id);
                //$user->remove_role('subscriber');
                //$user->add_role('without_login');
                $signup_user_role = 'cs_candidate';
                wp_update_user(array( 'ID' => esc_sql($user_id), 'role' => esc_sql($signup_user_role), 'user_status' => 1 ));
                update_user_meta($user_id, 'cs_allow_search', 'no');
                update_user_meta($user_id, "first_name", $fullname);
                update_user_meta($user_id, "cover_letter", $cover_letter);
                update_user_meta($user_id, 'cs_user_last_activity_date', strtotime(current_time('d-m-Y H:i:s')));
                $cs_media_image = cs_user_cv_without_login();
                if ( isset($cs_media_image['error']) && $cs_media_image['error'] == 1 ) {
                    echo esc_html($cs_media_image['message']);
                } else {
                    if ( $cs_media_image == '' ) {
                        $cs_media_image = isset($_POST['cs_candidate_cv']) ? $_POST['cs_candidate_cv'] : '';
                    }
                    update_user_meta($user_id, 'cs_candidate_cv', $cs_media_image);
                }
            }
            $user = $user_id;
            $response = array();
            if ( isset($user) && $user <> '' ) {

                $view = isset($_POST['view']) && $_POST['view'] != '' ? $_POST['view'] : '';
                if ( (isset($_POST['post_id']) && $_POST['post_id'] <> '' ) ) {
                    // checking application deadline date
                    $default_args = array( 'status' => 1, 'msg' => '' );
                    $dealine_response = apply_filters('job_hunt_check_job_deadline_date', $_POST['post_id'], $default_args);
                    if ( $dealine_response['status'] == 1 ) {
                        $cs_wishlist = cs_get_user_jobapply_meta();
                        $cs_wishlist = (isset($cs_wishlist) and is_array($cs_wishlist)) ? $cs_wishlist : array();
                        if ( isset($cs_wishlist) && in_array($_POST['post_id'], $cs_wishlist) ) {
                            $post_id = array();
                            $post_id[] = $_POST['post_id'];
                            $cs_wishlist = array_diff($post_id, $cs_wishlist);
                            //cs_update_user_jobapply_meta($cs_wishlist);
                            update_user_meta($user, 'cs-jobs-applied', $cs_wishlist);
                            $response['status'] = 1;
                            if ( $view == 'modern-v1' ) {
                                $response['msg'] = esc_html__('Applied', 'jobhunt');
                            } else {
                                $response['msg'] = '<i class="icon-thumbsup"></i><span>' . esc_html__('You have applied successfully.', 'jobhunt') . '</span>';
                            }
                        }

                        $cs_wishlist = array();
                        $cs_wishlist = get_user_meta($user, 'cs-jobs-applied', true);
                        if ( ! is_array($cs_wishlist) && $cs_wishlist == '' ) {
                            $cs_wishlist = array();
                        }
                        $cs_wishlist[] = $_POST['post_id'];
                        $cs_wishlist = array_unique($cs_wishlist);
                        update_user_meta($user, 'cs-jobs-applied', $cs_wishlist);
                        $user_watchlist = get_user_meta($user, 'cs-jobs-applied', true);
                        $job_employer = get_post_meta($_POST['post_id'], 'cs_job_username', true);
                        cs_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-applied-list', $user);
                        do_action('jobhunt_applied_multi_jobs', $_POST['post_id'], $user);
                        $cs_email_template_atts = array(
                            'candidate_id' => $user,
                            'job_id' => $_POST['post_id'],
                            'user_id' => $job_employer,
                        );
                        do_action('jobhunt_user_job_applied', $_POST, $user);
                        do_action('job_applied_candidate_notification', $cs_email_template_atts);
                        do_action('job_applied_employer_notification', $cs_email_template_atts);
                        $response['status'] = 1;
                        if ( $view == 'modern-v1' ) {
                            $response['msg'] = esc_html__('Applied', 'jobhunt');
                        } else {
                            $response['msg'] = '<i class="icon-thumbsup"></i><span>' . esc_html__('You have applied successfully.', 'jobhunt') . '</span>';
                        }
                    } else {

                        $response['status'] = 0;
                        $response['msg'] = esc_html__("You can't Apply Expired and outdated Job.", 'jobhunt');
                    }
                } else {
                    $response['status'] = 0;
                    $response['msg'] = esc_html__('You are not authorised', 'jobhunt');
                }
            } else {
                $response['status'] = 0;
                $response['msg'] = esc_html__('You have Already Apllied for this job', 'jobhunt');
            }
        }
        echo json_encode($response);
        die();
    }

    add_action("wp_ajax_cs_add_applied_job_withoutlogin_to_usermeta", "cs_add_applied_job_withoutlogin_to_usermeta");
    add_action("wp_ajax_nopriv_cs_add_applied_job_withoutlogin_to_usermeta", "cs_add_applied_job_withoutlogin_to_usermeta");
}
/**
 * Start Function  how to upload User CV
 */
if ( ! function_exists('cs_user_cv_without_login') ) {

    function cs_user_cv_without_login() {
        $resized_url = '';
        if ( isset($_FILES['media_upload']) && $_FILES['media_upload']['name'] != '' ) {
            $json = array();
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $current_user_id = get_current_user_id();
            $cs_allowed_file_types = array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'rtf' => 'application/rtf',
                'txt' => 'text/plain',
            );
            $status = wp_handle_upload($_FILES['media_upload'], array( 'test_form' => false, 'mimes' => $cs_allowed_file_types, 'unique_filename_callback' => 'my_cust_filename' ));
            if ( isset($status) && ! isset($status['error']) ) {
                $uploads = wp_upload_dir();
                $resized_url = $status['url'];
            } else {
                if ( isset($status['error']) ) {
                    $resized_url = array( 'error' => 1, 'message' => $status['error'] );
                } else {
                    $resized_url = '';
                }
            }
        }
        return $resized_url;
    }

}



if ( ! function_exists('cs_check_expire_job') ) {

    function cs_check_expire_job($job_id = '', $return_type = 'bool') {
        $current_date_time = current_time('timestamp');
        $cs_job_expired = get_post_meta($job_id, 'cs_job_expired', true);
        if ( $cs_job_expired >= $current_date_time ) {
            $expired_flag = false;
        } else {
            $expired_flag = true;
        }
        if ( $return_type == 'html' && $expired_flag ) {
            $html = '<div class="expired-job-notice">
                            <span>' . esc_html__('This job has been Expired', 'jobhunt') . '</span>
                    </div>';
            return force_balance_tags($html);
        } else {
            return $expired_flag;
        }
    }

}


/**
 * Start Function how to Apply for job in User Meta
 */
if ( ! function_exists('cs_add_applied_job_to_usermeta') ) {

    function cs_add_applied_job_to_usermeta() {

        global $cs_plugin_options;
        $user = cs_get_user_id();
        $response = array();
        $cs_user_apply_job = isset($cs_plugin_options['cs_user_apply_job']) ? $cs_plugin_options['cs_user_apply_job'] : '';
        $is_pckage = false;

        if ( (isset($_POST['post_id']) && $_POST['post_id'] <> '' ) ) {
            $job_expired = cs_check_expire_job($_POST['post_id']);
            if ( $job_expired ) {
                $response['status'] = 0;
                $response['msg'] = sprintf(esc_html__("You can't apply because this job has been expired.", 'jobhunt'));
                echo json_encode($response);
                die;
            }
        }



        if ( $cs_user_apply_job == 'on' ) {
            $cs_memberhsip_packages_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';
            $cs_packages_ids = array();
            if ( is_array($cs_memberhsip_packages_options) && sizeof($cs_memberhsip_packages_options) > 0 ) {
                foreach ( $cs_memberhsip_packages_options as $cs_memberhsip_package ) {
                    $cs_packages_ids[] = $cs_memberhsip_package['membership_pkg_id'];
                }
            }
            $cs_candidate_func = new cs_candidate_fnctions();
            $cs_package_check = $cs_candidate_func->cs_is_membership_pkg_subscribed($cs_packages_ids);
            if ( $cs_package_check['cs_trans_count'] == 0 ) {
                $response['status'] = 0;
                $response['count'] = $cs_package_check['cs_trans_count'];
                $response['msg'] = sprintf(esc_html__("Please subscribe a package to apply.", 'jobhunt'));
                echo json_encode($response);
                die;
            } else {
                $is_pckage = true;
            }
        }

        if ( $cs_user_apply_job != 'on' ) {
            $is_pckage = true;
        }



        if ( isset($user) && $user <> '' && $is_pckage ) {

            do_action('jobhunt_applied_candidate_fill_profile', $user);
            $view = isset($_POST['view']) && $_POST['view'] != '' ? $_POST['view'] : '';
            $user_status = 'inactive';
            $user_status = get_user_meta($user, 'cs_user_status', true);
            if ( $user_status == 'inactive' ) {
                $response['status'] = 0;
                $response['msg'] = sprintf(esc_html__("You can't apply because your account is not activated yet.", 'jobhunt'));
                echo json_encode($response);
                die;
            }
            $candidate_approve_skill = isset($cs_plugin_options['cs_candidate_skills_percentage']) ? $cs_plugin_options['cs_candidate_skills_percentage'] : 0;
            $candidate_skill_perc = get_user_meta($user, 'cs_candidate_skills_percentage', true);
            if ( $candidate_approve_skill > 0 && $candidate_skill_perc < $candidate_approve_skill ) {
                $response['status'] = 0;
                $response['msg'] = sprintf(esc_html__('You must have atleast %s skills set to apply this job.', 'jobhunt'), $candidate_approve_skill . '%');
                echo json_encode($response);
                die;
            }

            if ( (isset($_POST['post_id']) && $_POST['post_id'] <> '' ) ) {
                // checking application deadline date
                $default_args = array( 'status' => 1, 'msg' => '' );
                $dealine_response = apply_filters('job_hunt_check_job_deadline_date', $_POST['post_id'], $default_args);
                if ( $dealine_response['status'] == 1 ) {
                    $cs_wishlist = cs_get_user_jobapply_meta();
                    $cs_wishlist = (isset($cs_wishlist) and is_array($cs_wishlist)) ? $cs_wishlist : array();
                    if ( isset($cs_wishlist) && in_array($_POST['post_id'], $cs_wishlist) ) {
                        $post_id = array();
                        $post_id[] = $_POST['post_id'];
                        $cs_wishlist = array_diff($post_id, $cs_wishlist);
                        cs_update_user_jobapply_meta($cs_wishlist);
                        $response['status'] = 1;
                        if ( $view == 'modern-v1' ) {
                            $response['msg'] = esc_html__('Applied', 'jobhunt');
                        } else {
                            $response['msg'] = '<span><i class="icon-thumbsup"></i></span>' . esc_html__('Applied', 'jobhunt') . '';
                        }
                    }

                    $cs_wishlist = array();
                    $cs_wishlist = get_user_meta(cs_get_user_id(), 'cs-jobs-applied', true);
                    if ( ! is_array($cs_wishlist) && $cs_wishlist == '' ) {
                        $cs_wishlist = array();
                    }
                    $cs_wishlist[] = $_POST['post_id'];
                    $cs_wishlist = array_unique($cs_wishlist);
                    update_user_meta(cs_get_user_id(), 'cs-jobs-applied', $cs_wishlist);
                    $user_watchlist = get_user_meta(cs_get_user_id(), 'cs-jobs-applied', true);
                    $job_employer = get_post_meta($_POST['post_id'], 'cs_job_username', true);
                    
                    
                    if(isset($_POST['cover_letter']) && !empty($_POST['cover_letter'])){
                        update_user_meta($user, 'cs_updated_cover_letter_'.$_POST['post_id'].' ', $_POST['cover_letter']); // change cover letter data for each job apply
                    }
                    

                    cs_create_user_meta_list($_POST['post_id'], 'cs-user-jobs-applied-list', $user);
                    do_action('jobhunt_applied_multi_jobs', $_POST['post_id'], $user);
                    $cs_email_template_atts = array(
                        'candidate_id' => $user,
                        'job_id' => $_POST['post_id'],
                        'user_id' => $job_employer,
                    );
                    $cs_connects_args = array(
                        'candidate_id' => $user,
                        'job_id' => $_POST['post_id'],
                        'trans_id' => $cs_package_check['cs_trans_id']
                    );

                    do_action('johunt_manage_connects_after_job_applied', $cs_connects_args);
                    do_action('jobhunt_user_job_applied', $_POST, $user);
                    do_action('job_applied_candidate_notification', $cs_email_template_atts);
                    do_action('job_applied_employer_notification', $cs_email_template_atts);
                    $response['status'] = 1;
                    if ( $view == 'modern-v1' ) {
                        $response['msg'] = esc_html__('Applied', 'jobhunt');
                    } else {
                        $response['msg'] = '<span><i class="icon-thumbsup"></i></span>' . esc_html__('Applied', 'jobhunt') . '';
                    }
                } else {
                    $response['status'] = 0;
                    $response['msg'] = esc_html__("You can't Apply Expired and outdated Job.", 'jobhunt');
                }
            } else {
                $response['status'] = 0;
                $response['msg'] = esc_html__('You are not authorised', 'jobhunt');
            }
        } else {
            $response['status'] = 0;
            $response['msg'] = esc_html__('You have to login first.', 'jobhunt');
        }
        echo json_encode($response);
        die();
    }

    add_action("wp_ajax_cs_add_applied_job_to_usermeta", "cs_add_applied_job_to_usermeta");
    add_action("wp_ajax_nopriv_cs_add_applied_job_to_usermeta", "cs_add_applied_job_to_usermeta");
}

/**
 * Start Function how to Remove for job in User Meta
 */
if ( ! function_exists('cs_remove_applied_job_to_usermeta') ) {

    function cs_remove_applied_job_to_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            $cs_job_expired = '';
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                $cs_job_expired = get_post_meta($_POST['post_id'], 'cs_job_expired', true); //get expire date of job
            }
            if ( (isset($_POST['post_id']) && $_POST['post_id'] <> '') && ($cs_job_expired < strtotime(current_time('d-m-Y'))) ) {
                cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-applied-list', $user);
            } else {
                $response['status'] = 0;
                $response['msg'] = esc_html__('You are not authorised', 'jobhunt');
            }
        } else {
            $response['status'] = 0;
            $response['msg'] = esc_html__('You have to login first.', 'jobhunt');
        }
        echo json_encode($response);
        die();
    }

    add_action("wp_ajax_cs_remove_applied_job_to_usermeta", "cs_remove_applied_job_to_usermeta");
    add_action("wp_ajax_nopriv_cs_remove_applied_job_to_usermeta", "cs_remove_applied_job_to_usermeta");
}

/**
 * Start Function how to Remove for job in User Meta
 */
if ( ! function_exists('cs_remove_applied_job_to_usermeta') ) {

    function cs_remove_applied_job_to_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( $cs_job_expired < strtotime(current_time('d-m-Y')) ) {
                if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                    cs_remove_from_user_meta_list($_POST['post_id'], 'cs-user-jobs-applied-list', $user);
                } else {
                    esc_html_e('You are not authorised', 'jobhunt');
                }
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_remove_applied_job_to_usermeta", "cs_remove_applied_job_to_usermeta");
    add_action("wp_ajax_nopriv_cs_remove_applied_job_to_usermeta", "cs_remove_applied_job_to_usermeta");
}

/**
 * Start Function how to Remove for job Using Ajax
 */
if ( ! function_exists('cs_ajax_remove_appliedjobs') ) {

    function cs_ajax_remove_appliedjobs($uid = '') {
        global $post;
        $uid = (isset($_POST['cs_uid']) and $_POST['cs_uid'] <> '') ? $_POST['cs_uid'] : '';
        $cs_post_id = cs_candidate_post_id($uid);
        if ( $cs_post_id == '' ) {
            if ( isset($uid) && $uid <> '' ) {
                $cs_jobapplied_array = get_user_meta($uid, 'cs-user-jobs-applied-list', true);
                if ( ! empty($cs_jobapplied_array) )
                    $cs_jobapplied = array_column_by_two_dimensional($cs_jobapplied_array, 'post_id');
                else
                    $cs_jobapplied = array();
            }
            $args = array( 'posts_per_page' => "-1", 'post__in' => $cs_jobapplied,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_expired',
                        'value' => strtotime(current_time('d-m-Y')),
                        'compare' => '<',
                        'type' => 'numeric'
                    )
                ),
                'post_type' => 'jobs', 'order' => "ASC"
            );
            $custom_query = new WP_Query($args);
            $postlist = get_posts($args);
            $post_id = array();
            foreach ( $postlist as $post ) {
                $post_id[] += $post->ID;
                cs_remove_from_user_meta_list($post->ID, 'cs-user-jobs-applied-list', $uid);
            }
            esc_html_e('Removed From Applied Job', 'jobhunt');
        } else {
            esc_html_e('You are not authorised', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_ajax_remove_appliedjobs", "cs_ajax_remove_appliedjobs");
    add_action("wp_ajax_nopriv_cs_ajax_remove_appliedjobs", "cs_ajax_remove_appliedjobs");
}

/**
 * Start Function how to Remove Extra Variables using Query String
 */
if ( ! function_exists('cs_remove_qrystr_extra_var') ) {

    function cs_remove_qrystr_extra_var($qStr, $key, $withqury_start = 'yes') {
        $qr_str = preg_replace('/[?&]' . $key . '=[^&]+$|([?&])' . $key . '=[^&]+&/', '$1', $qStr);
        if ( ! (strpos($qr_str, '?') !== false) ) {
            $qr_str = "?" . $qr_str;
        }
        $qr_str = str_replace("?&", "?", $qr_str);
        $qr_str = remove_dupplicate_var_val($qr_str);
        if ( $withqury_start == 'no' ) {
            $qr_str = str_replace("?", "", $qr_str);
        }
        return $qr_str;
        die();
    }

}

/**
 * Start Function how to get all Countries and Cities Function
 */
if ( ! function_exists('cs_get_all_countries_cities') ) {

    function cs_get_all_countries_cities() {

        global $cs_plugin_options;
        $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
        $location_name = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
        $locations_parent_id = 0;
        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
            'parent' => $locations_parent_id,
        );
        $cs_location_countries = get_terms('cs_locations', $country_args);
        $location_list = array();
        $selectedkey = '';
        if ( isset($_REQUEST['location']) && $_REQUEST['location'] != '' ) {
            $selectedkey = $_REQUEST['location'];
        }
        if ( $cs_location_type == 'countries_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {
                        $selected = 'selected';
                    }
                    if ( preg_match("/^$location_name/i", $country->name) ) {
                        $location_list[] = array( 'slug' => $country->slug, 'value' => $country->name );
                    }
                }
            }
        } else if ( $cs_location_type == 'countries_and_cities' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $country_added = 0;  // check for country added in array or not
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {
                        $selected = 'selected';
                    }
                    if ( preg_match("/^$location_name/i", $country->name) ) {
                        $location_list[] = array( 'slug' => $country->slug, 'value' => $country->name );
                        $country_added = 1;
                    }
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $state_parent_id = $selected_spec->term_id;
                    $cities = '';
                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );
                    $cities = get_terms('cs_locations', $states_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        $flag_i = 0;
                        foreach ( $cities as $key => $city ) {
                            if ( preg_match("/^$location_name/i", $city->name) ) {
                                if ( $country_added == 0 ) { // means if country not added in array then add one time in array for this city
                                    if ( $flag_i == 0 ) {
                                        $location_list[] = array( 'slug' => $country->slug, 'value' => $country->name );
                                    }
                                }
                                $location_list[]['child'] = array( 'slug' => $city->slug, 'value' => $city->name );
                                $flag_i ++;
                            }
                        }
                    }
                }
            }
        } else if ( $cs_location_type == 'cities_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $city_parent_id = $selected_spec->term_id;
                    $cities_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $city_parent_id,
                    );
                    $cities = get_terms('cs_locations', $cities_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        foreach ( $cities as $key => $city ) {
                            if ( preg_match("/^$location_name/i", $city->name) ) {
                                $location_list[] = array( 'slug' => $city->slug, 'value' => $city->name );
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($location_list);
        die();
    }

    add_action("wp_ajax_cs_get_all_countries_cities", "cs_get_all_countries_cities");
    add_action("wp_ajax_nopriv_cs_get_all_countries_cities", "cs_get_all_countries_cities");
}

/**
 * Start Function how to get Custom Loaction Using Google Info
 */
if ( ! function_exists('cs_get_custom_locationswith_google_auto') ) {

    function cs_get_custom_locationswith_google_auto($dropdown_start_html = '', $dropdown_end_html = '', $cs_text_ret = false, $cs_top_search = false) {
        global $cs_plugin_options, $cs_form_fields, $cs_form_fields2;
        $list_rand = rand(10000, 4999999);
        $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
        $location_list = '';
        $selectedkey = '';
        if ( isset($_REQUEST['location']) && $_REQUEST['location'] != '' ) {
            $selectedkey = $_REQUEST['location'];
        }
        $output = '';
        $output .= '<div class="cs_searchbox_div" data-locationadminurl="' . esc_url(admin_url("admin-ajax.php")) . '">';
        $cs_opt_array = array(
            'std' => $selectedkey,
            'id' => '',
            'before' => '',
            'echo' => false,
            'after' => '',
            'classes' => 'form-control cs_search_location_field',
            'extra_atr' => ' autocomplete="off" placeholder="' . esc_html__('All Locations', 'jobhunt') . '"',
            'cust_name' => '',
            'prefix_on' => false,
            'return' => true,
        );
        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $output .= '<input type="hidden" class="search_keyword" name="location" value="' . $selectedkey . '" />';
        $output .= '</div>';
        cs_google_autocomplete_scripts();
        echo force_balance_tags($output);
    }

}

/**
 * Start Function how to get Custom Loaction 
 */
if ( ! function_exists('cs_get_custom_locations') ) {

    function cs_get_custom_locations($dropdown_start_html = '', $dropdown_end_html = '', $cs_text_ret = false) {
        global $cs_plugin_options, $cs_form_fields2;
        $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
        $locations_parent_id = 0;
        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
            'parent' => $locations_parent_id,
        );
        $cs_location_countries = get_terms('cs_locations', $country_args);
        ob_start();
        $location_list = '';
        $selectedkey = '';
        $output = '';
        if ( isset($_REQUEST['location']) && $_REQUEST['location'] != '' ) {
            $selectedkey = $_REQUEST['location'];
        }
        if ( $cs_location_type == 'countries_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {

                        $selected = 'selected';
                    }
                    $location_list .= "<option class=\"item\" " . $selected . "  value='" . $country->slug . "'>" . $country->name . "</option>";
                }
            }
        } else if ( $cs_location_type == 'countries_and_cities' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {
                        $selected = 'selected';
                    }
                    $location_list .= "<option disabled class=\"category\" " . $selected . "  value='" . $country->slug . "'>" . $country->name . "</option>";
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $cities = '';
                    $state_parent_id = $selected_spec->term_id;
                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );
                    $cities = get_terms('cs_locations', $states_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        foreach ( $cities as $key => $city ) {
                            $selected = ( $selectedkey == $city->slug) ? 'selected' : '';
                            $location_list .= "<option class=\"item\" style=\"padding-left:30px;\" " . $selected . " value='" . $city->slug . "'>" . $city->name . "</option>";
                        }
                    }
                }
            }
        } else if ( $cs_location_type == 'cities_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    $cities = '';
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $state_parent_id = $selected_spec->term_id;
                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );
                    $cities = get_terms('cs_locations', $states_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        foreach ( $cities as $key => $city ) {
                            $selected = ( $selectedkey == $city->slug) ? 'selected' : '';
                            $location_list .= "<option class=\"item\" " . $selected . " value='" . $city->slug . "'>" . $city->name . "</option>";
                        }
                    }
                }
            }
        } else if ( $cs_location_type == 'single_city' ) {
            $location_city = isset($cs_plugin_options['cs_search_by_location_city']) ? $cs_plugin_options['cs_search_by_location_city'] : '';
            if ( isset($location_city) && ! empty($location_city) ) {
                $cs_opt_array = array(
                    'std' => $location_city,
                    'id' => '',
                    'before' => '',
                    'after' => '',
                    'classes' => '',
                    'extra_atr' => '',
                    'cust_id' => '',
                    'cust_name' => 'location',
                    'return' => true,
                    'required' => false
                );
                $output .= $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
            }
        }
        if ( $cs_location_type != 'single_city' ) {
            $output .= force_balance_tags($dropdown_start_html);
            $cs_locatin_cust = cs_location_convert();
            $cs_loc_name = ' name="location"';
            if ( $cs_locatin_cust != '' && $cs_text_ret == true ) {
                $cs_loc_name = '';
            }
            $location_list = '<option value="" class="category" >' . esc_html__('All Locations', 'jobhunt') . '</option>' . $location_list;
            $cs_opt_array = array(
                'cust_id' => 'employer-search-location',
                'cust_name' => '',
                'std' => $selectedkey,
                'desc' => '',
                'extra_atr' => 'title="' . esc_html__('Location', 'jobhunt') . '"' . cs_allow_special_char($cs_loc_name) . ' data-placeholder="' . esc_html__("All Locations", "jobhunt") . '" onchange="this.form.submit()"',
                'classes' => 'dir-map-search single-select search-custom-location chosen-select',
                'options' => $location_list,
                'hint_text' => '',
                'options_markup' => true,
                'return' => true,
            );
            $output .= $cs_form_fields2->cs_form_select_render($cs_opt_array);
            $output .= force_balance_tags($dropdown_end_html);
            echo force_balance_tags($output);
        }
        $post_data = ob_get_clean();
        echo force_balance_tags($post_data);
    }

}

/**
 * Start Function how to Convert  Custom Loaction 
 */
if ( ! function_exists('cs_location_convert') ) {

    function cs_location_convert() {
        global $cs_plugin_options;
        $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
        $cs_field_ret = true;
        $selectedkey = '';
        $locations_parent_id = 0;
        $country_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
            'parent' => $locations_parent_id,
        );
        $cs_location_countries = get_terms('cs_locations', $country_args);
        if ( isset($_GET['location']) && $_GET['location'] != '' ) {
            $selectedkey = $_GET['location'];
        }
        if ( $cs_location_type == 'countries_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {
                        $cs_field_ret = false;
                    }
                }
            }
        } else if ( $cs_location_type == 'countries_and_cities' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    if ( isset($selectedkey) && $selectedkey == $country->slug ) {
                        $cs_field_ret = false;
                    }
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $cities = '';
                    $state_parent_id = $selected_spec->term_id;
                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );
                    $cities = get_terms('cs_locations', $states_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        foreach ( $cities as $key => $city ) {
                            if ( $selectedkey == $city->slug ) {
                                $cs_field_ret = false;
                            }
                        }
                    }
                }
            }
        } else if ( $cs_location_type == 'cities_only' ) {
            if ( isset($cs_location_countries) && ! empty($cs_location_countries) ) {
                foreach ( $cs_location_countries as $key => $country ) {
                    $selected = '';
                    // load all cities against state  
                    $cities = '';
                    $selected_spec = get_term_by('slug', $country->slug, 'cs_locations');
                    $state_parent_id = $selected_spec->term_id;
                    $states_args = array(
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'fields' => 'all',
                        'slug' => '',
                        'hide_empty' => false,
                        'parent' => $state_parent_id,
                    );
                    $cities = get_terms('cs_locations', $states_args);
                    if ( isset($cities) && $cities != '' && is_array($cities) ) {
                        foreach ( $cities as $key => $city ) {
                            if ( $selectedkey == $city->slug ) {
                                $cs_field_ret = false;
                            }
                        }
                    }
                }
            }
        }

        if ( $cs_field_ret == true && $selectedkey != '' ) {
            return $selectedkey;
        }
        return '';
    }

}

/**
 * Start Function how to Count User Meta 
 */
if ( ! function_exists('count_usermeta') ) {

    function count_usermeta($key, $value, $opr, $return = false) {
        $arg = array(
            'meta_key' => $key,
            'meta_value' => $value,
            'meta_compare' => $opr,
        );
        $users = get_users($arg);
        if ( $return == true ) {
            return $users;
        }
        return count($users);
    }

}

/**
 * Start Function get to Post Meta 
 */
if ( ! function_exists('cs_get_postmeta_data') ) {

    function cs_get_postmeta_data($key, $value, $opr, $post_type, $return = false) {
        $user_post_arr = array( 'posts_per_page' => "-1", 'post_type' => $post_type, 'order' => "DESC", 'orderby' => 'post_date',
            'post_status' => 'publish', 'ignore_sticky_posts' => 1,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $value,
                    'compare' => $opr,
                )
            )
        );
        $user_data = get_posts($user_post_arr);
        if ( $return == true ) {
            return $user_data;
        }
    }

}

/**
 * @check array emptiness 
 */
if ( ! function_exists('is_array_empty') ) {

    function is_array_empty($a) {
        foreach ( $a as $elm )
            if ( ! empty($elm) )
                return false;
        return true;
    }

}

/**
 * @find heighes date index 
 */
if ( ! function_exists('find_heighest_date_index') ) {

    function find_heighest_date_index($cs_dates, $date_format = 'd-m-Y') {
        if ( ! empty($cs_dates) ) {
            $max = max(array_map('strtotime', $cs_dates));
            $finded_date = date($date_format, $max);
            $maxs = array_keys($cs_dates, $finded_date);
            if ( isset($maxs[0]) ) {
                return $maxs[0];
            }
        }
    }

}

/**
 * Start Function how to Save last User login Save
 */
if ( ! function_exists('user_last_login') ) {

    add_action('wp_login', 'user_last_login', 0, 2);

    function user_last_login($login, $user) {
        $user = get_user_by('login', $login);
        $now = time();
        update_user_meta($user->ID, 'user_last_login', $now);
    }

}

/**
 * Start Function how to Get last User login Save
 */
if ( ! function_exists('get_user_last_login') ) {

    function get_user_last_login($user_ID = '') {
        if ( $user_ID == '' ) {
            $user_ID = get_current_user_id();
        }
        $key = 'user_last_login';
        $single = true;
        $user_last_login = get_user_meta($user_ID, $key, $single);
        return $user_last_login;
    }

}

/**
 * @get user registeration time
 */
if ( ! function_exists('get_user_registered_timestamp') ) {

    function get_user_registered_timestamp($user_ID = '') {
        if ( $user_ID == '' ) {
            $user_ID = get_current_user_id();
        }
        if ( isset(get_userdata($user_ID)->user_registered) ) {
            $user_registered_str = strtotime(get_userdata($user_ID)->user_registered);
            return $user_registered_str;
        } else {
            return '';
        }
    }

}

/**
 * Start Function how to Update User Cv Selected CV Meta
 */
if ( ! function_exists('cs_update_user_cv_selected_list_meta') ) {

    function cs_update_user_cv_selected_list_meta($arr) {
        return update_user_meta(cs_get_user_id(), 'cs-candidate-selected-list', $arr);
    }

}

/**
 * Start Function how to Add  User In Selected Cv  Meta
 */
if ( ! function_exists('cs_add_cv_selected_list_usermeta') ) {

    function cs_add_cv_selected_list_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                $cs_selected_list = cs_get_user_cv_selected_list_meta();
                $cs_selected_list = (isset($cs_selected_list) and is_array($cs_selected_list)) ? $cs_selected_list : array();
                if ( isset($cs_selected_list) && in_array($_POST['post_id'], $cs_selected_list) ) {
                    $post_id = array();
                    $post_id[] = $_POST['post_id'];
                    $cs_selected_list = array_diff($post_id, $cs_selected_list);
                    cs_update_user_cv_selected_list_meta($cs_selected_list);
                    esc_html_e('Added to List', 'jobhunt');
                    die();
                }
                $cs_selected_list = array();
                $cs_selected_list = get_user_meta(cs_get_user_id(), 'cs-candidate-selected-list', true);
                $cs_selected_list[] = $_POST['post_id'];
                $cs_selected_list = array_unique($cs_selected_list);
                update_user_meta(cs_get_user_id(), 'cs-candidate-selected-list', $cs_selected_list);
                $user_watchlist = get_user_meta(cs_get_user_id(), 'cs-candidate-selected-list', true);
                esc_html_e('Added to List', 'jobhunt');
                ?>
                <div class="outerwrapp-layer<?php echo esc_html($_POST['post_id']); ?> cs-added-msg">
                    <?php esc_html_e('Added to Selected List', 'jobhunt'); ?>
                </div>
                <?php
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_add_cv_selected_list_usermeta", "cs_add_cv_selected_list_usermeta");
    add_action("wp_ajax_nopriv_cs_add_cv_selected_list_usermeta", "cs_add_cv_selected_list_usermeta");
}

/**
 * Start Function how to Remove  User In Selected Cv
 */
if ( ! function_exists('cs_remove_cv_selected_list_usermeta') ) {

    function cs_remove_cv_selected_list_usermeta() {
        $user = cs_get_user_id();
        if ( isset($user) && $user <> '' ) {
            if ( isset($_POST['post_id']) && $_POST['post_id'] <> '' ) {
                $cs_selected_list = cs_get_user_cv_selected_list_meta();
                $cs_selected_list = (isset($cs_selected_list) and is_array($cs_selected_list)) ? $cs_selected_list : array();
                $post_id = array();
                $post_id[] = $_POST['post_id'];
                $cs_selected_list = array_diff($cs_selected_list, $post_id);
                cs_update_user_cv_selected_list_meta($cs_selected_list);
                echo esc_html__('Add to List', 'jobhunt') . '<div class="outerwrapp-layer' . $_POST['post_id'] . ' cs-remove-msg">';
                esc_html_e('Removed From Selected List', 'jobhunt');
                echo '</div>';
            } else {
                esc_html_e('You are not authorised', 'jobhunt');
            }
        } else {
            esc_html_e('You have to login first.', 'jobhunt');
        }
        die();
    }

    add_action("wp_ajax_cs_remove_cv_selected_list_usermeta", "cs_remove_cv_selected_list_usermeta");
    add_action("wp_ajax_nopriv_cs_remove_cv_selected_list_usermeta", "cs_remove_cv_selected_list_usermeta");
}

/**
 * Start Function how to Add Enqueue Scripts  
 */
if ( ! function_exists('my_enqueue_scripts') ) {

    add_action('wp_print_scripts', 'my_enqueue_scripts');

    function my_enqueue_scripts() {
        wp_enqueue_script('tiny_mce');
    }

}

/**
 * Start Function how to Get Job Type Jobs in Dropdown  
 */
if ( ! function_exists('cs_get_job_type_dropdown') ) {

    function cs_get_job_type_dropdown($name, $id, $selected_post_id = '', $class = '', $required_status = 'false') {
        global $cs_form_fields2;
        $selected_slug = array();
        $required = '';
        if ( $required_status == 'true' ) {
            $required = ' required';
        }
        if ( $selected_post_id != '' ) {
            // get all job types
            $all_job_type = get_the_terms($selected_post_id, 'job_type');
            $job_type_values = '';
            $job_type_class = '';
            $specialism_flag = 1;
            if ( $all_job_type != '' ) {
                foreach ( $all_job_type as $job_typeitem ) {
                    $selected_slug = $job_typeitem->term_id;
                }
            }
        }
        $job_types_all_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );
        $all_job_types = get_terms('job_type', $job_types_all_args);
        $select_options = array();
        if ( isset($all_job_types) && is_array($all_job_types) ) {
            foreach ( $all_job_types as $job_typesitem ) {
                $select_options[$job_typesitem->term_id] = $job_typesitem->name;
            }
        }
        $cs_opt_array = array(
            'cust_id' => $id,
            'cust_name' => $name,
            'std' => $selected_slug,
            'desc' => '',
            'extra_atr' => 'data-placeholder="' . esc_html__("Please Select", "jobhunt") . '"',
            'classes' => $class,
            'options' => $select_options,
            'hint_text' => '',
            'required' => 'yes',
        );
        if ( isset($required_status) && $required_status == 'true' ) {
            $cs_opt_array['required'] = 'yes';
        }
        $cs_form_fields2->cs_form_select_render($cs_opt_array);
    }

}
/**

 * Start Function how to Get specialisms Jobs in Dropdown  

 */
if ( ! function_exists('cs_get_job_specialisms_dropdown') ) {

    function cs_get_job_specialisms_dropdown($name, $id, $selected_post_id = '', $class = '', $required_status = 'false') {

        global $cs_form_fields2;
        $selected_slug = array();
        $required = '';
        if ( $required_status == 'true' ) {
            $required = ' required';
        }
        if ( $selected_post_id != '' ) {
            // get all job types			
            $all_specialisms = get_the_terms($selected_post_id, 'specialisms');
            $specialisms_values = '';
            $specialisms_class = '';
            $specialism_flag = 1;
            if ( $all_specialisms != '' ) {
                foreach ( $all_specialisms as $specialismsitem ) {
                    $selected_slug[] = $specialismsitem->term_id;
                }
            }
        }
        $specialisms_all_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );
        $all_specialisms = get_terms('specialisms', $specialisms_all_args);
        $select_options = array();
        if ( isset($all_specialisms) && is_array($all_specialisms) ) {

            foreach ( $all_specialisms as $specialismsitem ) {

                $select_options[$specialismsitem->term_id] = $specialismsitem->name;
            }
        }

        $please_select_specialisms_label = esc_html__('Please Select specialism', 'jobhunt');
        $please_select_specialisms_label = apply_filters('jobhunt_replace_please_select_specialism_to_category', $please_select_specialisms_label);

        $cs_opt_array = array(
            'id' => $id,
            'cust_id' => $id,
            'cust_name' => $name . '[]',
            'std' => $selected_slug,
            'desc' => '',
            'extra_atr' => 'data-placeholder="' . $please_select_specialisms_label . '"',
            'classes' => $class,
            'options' => $select_options,
            'hint_text' => '',
            'required' => 'yes',
        );
        if ( isset($required_status) && $required_status == 'true' ) {
            $cs_opt_array['required'] = 'yes';
        }
        $cs_form_fields2->cs_form_multiselect_render($cs_opt_array);
    }

}

/**
 * Start Function how to Add specialisms  in Dropdown  
 */
if ( ! function_exists('cs_get_specialisms_dropdown') ) {

    function cs_get_specialisms_dropdown($name, $id, $user_id = '', $class = '', $required_status = 'false') {
        global $cs_form_fields2, $post;
        $output = '';
        $cs_spec_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'fields' => 'all',
            'slug' => '',
            'hide_empty' => false,
        );
        $terms = get_terms('specialisms', $cs_spec_args);
        if ( ! empty($terms) ) {

            $please_select_specialisms_label = esc_html__('Please Select specialism', 'jobhunt');
            $please_select_specialisms_label = apply_filters('jobhunt_replace_please_select_specialism_to_category', $please_select_specialisms_label);

            $cs_selected_specs = get_user_meta($user_id, $name, true);
            $specialisms_option = '';
            foreach ( $terms as $term ) {
                $cs_selected = '';
                if ( is_array($cs_selected_specs) && in_array($term->slug, $cs_selected_specs) ) {
                    $cs_selected = ' selected="selected"';
                }
                $specialisms_option .= '<option' . $cs_selected . ' value="' . esc_attr($term->slug) . '">' . $term->name . '</option>';
            }
            $cs_opt_array = array(
                'cust_id' => $id,
                'cust_name' => $name . '[]',
                'std' => '',
                'desc' => '',
                'return' => true,
                'extra_atr' => 'data-placeholder="' . $please_select_specialisms_label . '"',
                'classes' => $class,
                'options' => $specialisms_option,
                'options_markup' => true,
                'hint_text' => '',
            );
            if ( isset($required_status) && $required_status == true ) {
                $cs_opt_array['required'] = 'yes';
            }
            $output .= $cs_form_fields2->cs_form_multiselect_render($cs_opt_array);
        } else {
            $no_specialisms_available_label = esc_html__('There are no specialisms available.', 'jobhunt');
            $no_specialisms_available_label = apply_filters('jobhunt_replace_no_specialisms_available', $no_specialisms_available_label);
            $output .= $no_specialisms_available_label;
        }
        return $output;
    }

}

/**
 * Start Function how to Add images sizes and their URL's 
 */
if ( ! function_exists('cs_get_img_url') ) {

    function cs_get_img_url($img_name = '', $size = 'cs_media_2', $return_sizes = false, $dir_filter = true) {
        $ret_name = '';
        $cs_img_sizes = array(
            'cs_media_1' => '-870x489',
            'cs_media_2' => '-270x203',
            'cs_media_3' => '-236x168',
            'cs_media_4' => '-200x200',
            'cs_media_5' => '-180x135',
            'cs_media_6' => '-150x113',
        );
        if ( $return_sizes == true ) {
            return $cs_img_sizes;
        }
        // Register our new path for user images.
        if ( $dir_filter == true ) {
            add_filter('upload_dir', 'cs_user_images_custom_directory');
        }
        $cs_upload_dir = wp_upload_dir();
        $cs_upload_sub_dir = '';
        if ( (strpos($img_name, $cs_img_sizes['cs_media_1']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_2']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_3']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_4']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_5']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_6']) !== false) ) {
            if ( strpos($img_name, $cs_img_sizes['cs_media_1']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_1']) + strlen($cs_img_sizes['cs_media_1'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_1']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_2']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_2']) + strlen($cs_img_sizes['cs_media_2'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_2']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_3']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_3']) + strlen($cs_img_sizes['cs_media_3'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_3']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_4']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_4']) + strlen($cs_img_sizes['cs_media_4'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_4']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_5']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_5']) + strlen($cs_img_sizes['cs_media_5'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_5']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_6']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_6']) + strlen($cs_img_sizes['cs_media_6'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_6']));
            }
            $cs_upload_dir = isset($cs_upload_dir['url']) ? $cs_upload_dir['url'] . '/' : '';
            $cs_upload_dir = $cs_upload_dir . $cs_upload_sub_dir;
            if ( $ret_name != '' ) {
                if ( isset($cs_img_sizes[$size]) ) {
                    $ret_name = $cs_upload_dir . $ret_name . $cs_img_sizes[$size] . $img_ext;
                } else {
                    $ret_name = $cs_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {
            if ( $img_name != '' ) {
                $ret_name = '';
            }
        }
        // Set everything back to normal.
        if ( $dir_filter == true ) {
            remove_filter('upload_dir', 'cs_user_images_custom_directory');
        }
        return $ret_name;
    }

}

/**
 * Start Function how to  get image
 */
if ( ! function_exists('cs_get_orignal_image_nam') ) {

    function cs_get_orignal_image_nam($img_name = '', $size = 'cs_media_2') {
        $ret_name = '';
        $cs_img_sizes = array(
            'cs_media_1' => '-870x489',
            'cs_media_2' => '-270x203',
            'cs_media_3' => '-236x168',
            'cs_media_4' => '-200x200',
            'cs_media_5' => '-180x135',
            'cs_media_6' => '-150x113',
        );
        if ( (strpos($img_name, $cs_img_sizes['cs_media_1']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_2']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_3']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_4']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_5']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_6']) !== false) ) {
            if ( strpos($img_name, $cs_img_sizes['cs_media_1']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_1']) + strlen($cs_img_sizes['cs_media_1'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_1']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_2']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_2']) + strlen($cs_img_sizes['cs_media_2'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_2']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_3']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_3']) + strlen($cs_img_sizes['cs_media_3'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_3']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_4']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_4']) + strlen($cs_img_sizes['cs_media_4'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_4']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_5']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_5']) + strlen($cs_img_sizes['cs_media_5'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_5']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_6']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_6']) + strlen($cs_img_sizes['cs_media_6'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_6']));
            }
            $cs_upload_dir = isset($cs_upload_dir['url']) ? $cs_upload_dir['url'] . '/' : '';
            if ( $ret_name != '' ) {
                if ( isset($cs_img_sizes[$size]) ) {
                    $ret_name = $cs_upload_dir . $ret_name . $cs_img_sizes[$size] . $img_ext;
                } else {
                    $ret_name = $cs_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {
            if ( $img_name != '' ) {
                $ret_name = '';
            }
        }
        return $ret_name;
    }

}

/**
 * Start Function how to  get image
 */
if ( ! function_exists('cs_get_image_url') ) {

    function cs_get_image_url($img_name = '', $size = 'cs_media_2', $return_sizes = false) {
        $ret_name = '';
        $cs_img_sizes = array(
            'cs_media_1' => '-870x489',
            'cs_media_2' => '-270x203',
            'cs_media_3' => '-236x168',
            'cs_media_4' => '-200x200',
            'cs_media_5' => '-180x135',
            'cs_media_6' => '-150x113',
        );
        if ( $return_sizes == true ) {
            return $cs_img_sizes;
        }
        add_filter('upload_dir', 'cs_user_images_custom_directory');
        $cs_upload_dir = wp_upload_dir();
        $cs_upload_sub_dir = '';
        if ( (strpos($img_name, $cs_img_sizes['cs_media_1']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_2']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_3']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_4']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_5']) !== false) || (strpos($img_name, $cs_img_sizes['cs_media_6']) !== false) ) {
            if ( strpos($img_name, $cs_img_sizes['cs_media_1']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_1']) + strlen($cs_img_sizes['cs_media_1'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_1']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_2']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_2']) + strlen($cs_img_sizes['cs_media_2'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_2']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_3']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_3']) + strlen($cs_img_sizes['cs_media_3'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_3']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_4']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_4']) + strlen($cs_img_sizes['cs_media_4'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_4']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_5']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_5']) + strlen($cs_img_sizes['cs_media_5'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_5']));
            } elseif ( strpos($img_name, $cs_img_sizes['cs_media_6']) !== false ) {
                $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_6']) + strlen($cs_img_sizes['cs_media_6'])), strlen($img_name));
                $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_6']));
            }
            $cs_upload_dir = isset($cs_upload_dir['url']) ? $cs_upload_dir['url'] . '/' : '';
            $cs_upload_dir = $cs_upload_dir . $cs_upload_sub_dir;
            if ( $ret_name != '' ) {
                if ( isset($cs_img_sizes[$size]) ) {
                    $ret_name = $cs_upload_dir . $ret_name . $cs_img_sizes[$size] . $img_ext;
                } else {
                    $ret_name = $cs_upload_dir . $ret_name . $img_ext;
                }
            }
        } else {
            if ( $img_name != '' ) {
                $ret_name = '';
            }
        }
        // Set everything back to normal.
        remove_filter('upload_dir', 'cs_user_images_custom_directory');
        return $ret_name;
    }

}

/**
 * Start Function how to Add get portfolio images  URL's 
 */
if ( ! function_exists('cs_get_portfolio_img_url') ) {

    function cs_get_portfolio_img_url($img_name = '', $size = 'cs_media_5', $return_sizes = false) {
        $cs_img_sizes = array(
            'cs_media_5' => '-180x135',
        );
        if ( $return_sizes == true ) {
            return $cs_img_sizes;
        }
        $cs_upload_dir = wp_upload_dir();
        $cs_upload_dir = isset($cs_upload_dir['url']) ? $cs_upload_dir['url'] . '/' : '';
        if ( strpos($img_name, $cs_img_sizes['cs_media_5']) !== false ) {
            $img_ext = substr($img_name, ( strpos($img_name, $cs_img_sizes['cs_media_5']) + strlen($cs_img_sizes['cs_media_5'])), strlen($img_name));
            $ret_name = substr($img_name, 0, strpos($img_name, $cs_img_sizes['cs_media_5']));
            if ( isset($cs_img_sizes[$size]) ) {
                $ret_name = $cs_upload_dir . $ret_name . $cs_img_sizes[$size] . $img_ext;
            } else {
                $ret_name = $cs_upload_dir . $ret_name . $img_ext;
            }
        } else {
            $ret_name = $cs_upload_dir . $img_name;
        }
        return $ret_name;
    }

}

/**
 * Start Function how to Save  images  URL's 
 */
if ( ! function_exists('cs_save_img_url') ) {

    function cs_save_img_url($img_url = '') {
        if ( $img_url != '' ) {
            $img_id = cs_get_attachment_id_from_url($img_url);
            $img_url = wp_get_attachment_image_src($img_id, 'cs_media_2');
            if ( isset($img_url[0]) ) {
                $img_url = $img_url[0];
                if ( strpos($img_url, 'uploads/') !== false ) {
                    $img_url = substr($img_url, ( strpos($img_url, 'uploads/') + strlen('uploads/')), strlen($img_url));
                }
            }
        }
        return $img_url;
    }

}

/**
 * Start Function how to get attachment id from url 
 */
if ( ! function_exists('cs_get_attachment_id_from_url') ) {

    function cs_get_attachment_id_from_url($attachment_url = '') {
        global $wpdb;
        $attachment_id = false;
        // If there is no url, return.
        if ( '' == $attachment_url )
            return;
        // Get the upload directory paths
        $upload_dir_paths = wp_upload_dir();
        if ( false !== strpos($attachment_url, $upload_dir_paths['baseurl']) ) {
            // If this is the URL of an auto-generated thumbnail, get the URL of the original image
            $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
        }
        return $attachment_id;
    }

}

/**
 * Start Function how to Remove Image URL's 
 */
if ( ! function_exists('cs_remove_img_url') ) {

    function cs_remove_img_url($img_url = '') {
        $cs_upload_dir = wp_upload_dir();
        $cs_upload_dir = isset($cs_upload_dir['basedir']) ? $cs_upload_dir['basedir'] . '/' : '';
        if ( $img_url != '' ) {
            $cs_img_sizes = cs_get_img_url('', '', true);
            if ( isset($cs_img_sizes['cs_media_2']) && strpos($img_url, $cs_img_sizes['cs_media_2']) !== false ) {
                $img_ext = substr($img_url, ( strpos($img_url, $cs_img_sizes['cs_media_2']) + strlen($cs_img_sizes['cs_media_2'])), strlen($img_url));
                $img_name = substr($img_url, 0, strpos($img_url, $cs_img_sizes['cs_media_2']));
                if ( is_file($cs_upload_dir . $img_name . $img_ext) ) {

                    unlink($cs_upload_dir . $img_name . $img_ext);
                }
                if ( is_array($cs_img_sizes) ) {
                    foreach ( $cs_img_sizes as $cs_key => $cs_size ) {
                        if ( is_file($cs_upload_dir . $img_name . $cs_size . $img_ext) ) {

                            unlink($cs_upload_dir . $img_name . $cs_size . $img_ext);
                        }
                    }
                }
            } else {
                if ( is_file($cs_upload_dir . $img_url) ) {

                    unlink($cs_upload_dir . $img_url);
                }
            }
        }
    }

}
/**
 * End Function how to Remove Image URL's 
 */
/**
 * Start Function how to Add Wishlist in Candidate
 */
if ( ! function_exists('candidate_header_wishlist') ) {

    function candidate_header_wishlist($return = 'no') {
        global $post, $cs_plugin_options;


        $display_shortlist_button = 'yes';
        $display_shortlist_button = apply_filters('jobhunt_candidate_lists_shortlist_button', $display_shortlist_button);
        if ( $display_shortlist_button == 'yes' ) {
            $top_wishlist_menu_html = '';
            $cs_employer_functions = new cs_employer_functions();

            $user = cs_get_user_id();
            if ( isset($user) && $user <> '' ) {
                $cs_shortlist_array = get_user_meta($user, 'cs-user-jobs-wishlist', true);

                if ( ! empty($cs_shortlist_array) )
                    $cs_shortlist = array_column_by_two_dimensional($cs_shortlist_array, 'post_id');
                else
                    $cs_shortlist = array();
            }
            if ( ! empty($cs_shortlist) && count($cs_shortlist) > 0 ) {
                $args = array( 'posts_per_page' => "-1", 'post__in' => $cs_shortlist, 'post_type' => 'jobs' );
                $custom_query = new WP_Query($args);
                $wishlist_count = $custom_query->found_posts;
                if ( $custom_query->have_posts() ):
                    $top_wishlist_menu_html .= '<div class="wish-list dsdsdsdsdsdsd" id="top-wishlist-content"><a><i class="icon-heart6"></i></a> <em class="cs-bgcolor" id="cs-fav-counts">' . absint($wishlist_count) . '</em>
					<div class="recruiter-widget wish-list-dropdown">
						<ul class="recruiter-list">';
                    $top_wishlist_menu_html .= '<li><span class="cs_shortlisted_count">' . esc_html__("My Shortlisted Jobs", 'jobhunt') . ' (<span id="cs-heading-counts">' . absint($wishlist_count) . '</span>)</span></li>';
                    $wishlist_count = 1;

                    while ( $custom_query->have_posts() ): $custom_query->the_post();
                        $cs_jobs_thumb_url = '';
                        $employer_img = '';
                        // get employer images at run time
                        $cs_job_employer = get_post_meta($post->ID, "cs_job_username", true); //
                        $cs_job_employer_data = cs_get_postmeta_data('cs_user', $cs_job_employer, '=', 'employer', true);
                        $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                        if ( $employer_img == '' ) {
                            $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                        } else {
                            $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                        }

                        $top_wishlist_menu_html .= '<li class="alert alert-dismissible">
									<a class="cs-remove-top-shortlist" id="cs-rem-' . esc_html($post->ID) . '" onclick="cs_unset_user_job_fav(\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . esc_html($post->ID) . '\')"><span>&times;</span></a>';
                        if ( $cs_jobs_thumb_url != '' ) {
                            $top_wishlist_menu_html .= '<a href="' . esc_url(get_the_permalink($post->ID)) . '"><img src="' . esc_url($cs_jobs_thumb_url) . '" alt="" /></a>';
                        }
                        $top_wishlist_menu_html .= '<div class="cs-info">
										<h6><a href="' . esc_url(get_the_permalink($post->ID)) . '">' . $post->post_title . '</a></h6>
										' . esc_html__('Added ', 'jobhunt') . '<span>';
                        // getting added in wishlist date
                        $finded = in_multiarray($post->ID, $cs_shortlist_array, 'post_id');
                        if ( $finded != '' )
                            if ( $cs_shortlist_array[$finded[0]]['date_time'] != '' ) {
                                $top_wishlist_menu_html .= date_i18n(get_option('date_format'), $cs_shortlist_array[$finded[0]]['date_time']);
                            }
                        $top_wishlist_menu_html .= '</span>
									</div>
								</li>';
                        $wishlist_count ++;
                        if ( $wishlist_count > 5 ) {
                            break;
                        }
                    endwhile;
                    $cs_page_id = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : '';
                    $top_wishlist_menu_html .= '<li class="alert alert-dismissible"><a href="' . esc_url(cs_users_profile_link($cs_page_id, 'shortlisted_jobs', $user)) . '" >' . esc_html__('View All', 'jobhunt') . '</a></li>
						</ul>
					</div></div>';
                    wp_reset_postdata();
                endif;
            }
            if ( $return == 'no' )
                echo force_balance_tags($top_wishlist_menu_html);
            else
                return $top_wishlist_menu_html;
        }
    }

}

/**
 * Start Function how to Find Other Fields User Meta List
 */
if ( ! function_exists('cs_find_other_field_user_meta_list') ) {

    function cs_find_other_field_user_meta_list($post_id, $post_column, $list_name, $need_find, $user_id) {
        $finded = cs_find_index_user_meta_list($post_id, $list_name, $post_column, $user_id);
        $index = '';
        $need_find_value = '';
        if ( isset($finded[0]) ) {
            $index = $finded[0];
            $existing_list_data = get_user_meta($user_id, $list_name, true);
            $need_find_value = $existing_list_data[$index][$need_find];
        }
        return $need_find_value;
    }

}
/**
 * Start Function how to find Index
 */
if ( ! function_exists('find_in_multiarray') ) {

    function find_in_multiarray($elem, $array, $field) {
        $top = sizeof($array);
        $k = 0;
        $new_array = array();
        for ( $i = 0; $i <= $top; $i ++  ) {
            if ( isset($array[$i]) ) {
                $new_array[$k] = $array[$i];
                $k ++;
            }
        }
        $array = $new_array;
        $top = sizeof($array) - 1;
        $bottom = 0;
        $finded_index = array();
        if ( is_array($array) ) {
            while ( $bottom <= $top ) {
                if ( $array[$bottom][$field] == $elem )
                    $finded_index[] = $bottom;
                else
                if ( is_array($array[$bottom][$field]) )
                    if ( find_in_multiarray($elem, ($array[$bottom][$field])) )
                        $finded_index[] = $bottom;
                $bottom ++;
            }
        }
        return $finded_index;
    }

}

/**
 * Start Function how to Find Index User Meta List
 */
if ( ! function_exists('cs_find_index_user_meta_list') ) {

    function cs_find_index_user_meta_list($post_id, $list_name, $need_find, $user_id) {
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        if ( empty($existing_list_data) ) {
            $existing_list_data = array();
        }
        $finded = array();
        if ( is_array($existing_list_data) && ! empty($existing_list_data) ) {
            $finded = find_in_multiarray($post_id, $existing_list_data, $need_find);
        }

        if ( $need_find == 'post_id' ) {
            $finded = apply_filters('jobhunt_apply_job_multi_times', $finded);
        }
        return $finded;
    }

}

/**
 * Start Function how to Remove List From User Meta List
 */
if ( ! function_exists('cs_remove_from_user_meta_list') ) {

    function cs_remove_from_user_meta_list($post_id, $list_name, $user_id) {
        $existing_list_data = '';
        $existing_list_data = get_user_meta($user_id, $list_name, true);
        $finded = in_multiarray($post_id, $existing_list_data, 'post_id');
        $existing_list_data = remove_index_from_array($existing_list_data, $finded);
        update_user_meta($user_id, $list_name, $existing_list_data);
        do_action('jobhunt_removed_multi_applied_expired_jobs', $user_id, $post_id);
    }

}

/**
 * Start Function how to Create  User Meta List
 */
if ( ! function_exists('cs_create_user_meta_list') ) {

    function cs_create_user_meta_list($post_id, $list_name, $user_id) {
        $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));
        $existing_list_data = array();
        $existing_list_data = get_user_meta($user_id, $list_name, true);

        if ( ! is_array($existing_list_data) ) {
            $existing_list_data = array();
        }


        if ( is_array($existing_list_data) ) {

            // search duplicat and remove it then arrange new ordering
            $finded = in_multiarray($post_id, $existing_list_data, 'post_id');
            $existing_list_data = remove_index_from_array($existing_list_data, $finded);
            // adding one more entry
            $existing_list_data[] = array( 'post_id' => $post_id, 'date_time' => $current_timestamp );
            update_user_meta($user_id, $list_name, $existing_list_data);
        }
    }

}

/**
 * Start Function how to find Index
 */
if ( ! function_exists('in_multiarray') ) {

    function in_multiarray($elem, $array, $field) {
        $top = sizeof($array) - 1;
        $bottom = 0;
        $finded_index = array();
        if ( is_array($array) ) {
            while ( $bottom <= $top ) {
                if ( $array[$bottom][$field] == $elem )
                    $finded_index[] = $bottom;
                else
                if ( is_array($array[$bottom][$field]) )
                    if ( in_multiarray($elem, ($array[$bottom][$field])) )
                        $finded_index[] = $bottom;
                $bottom ++;
            }
        }
        return $finded_index;
    }

}

/**
 * Start Function how to remove given Indexes
 */
if ( ! function_exists('remove_index_from_array') ) {

    function remove_index_from_array($array, $index_array) {
        $top = sizeof($index_array) - 1;
        $bottom = 0;
        if ( is_array($index_array) ) {
            while ( $bottom <= $top ) {
                unset($array[$index_array[$bottom]]);
                $bottom ++;
            }
        }
        if ( ! empty($array) )
            return array_values($array);
        else
            return $array;
    }

}

/**
 * Start Function how to get only one Index from two dimenssion array
 */
if ( ! function_exists("array_column_by_two_dimensional") ) {

    function array_column_by_two_dimensional($array, $column_name) {
        if ( isset($array) && is_array($array) ) {
            return array_map(function($element = array()) use($column_name) {
                $element = empty($element) ? array() : $element;
                return $element[$column_name];
            }, $array);
        }
    }

}

/**
 * Start Function how prevent guest not access admin panel
 */
if ( ! function_exists('redirect_user') ) {

    add_action('admin_init', 'redirect_user');

    function redirect_user() {
        $user = wp_get_current_user();
        if ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ( empty($user) || in_array("cs_employer", (array) $user->roles) || in_array("cs_candidate", (array) $user->roles)) ) {
            wp_safe_redirect(home_url());
            exit;
        }
    }

}

/**
 * Start Function how to get Job Detail
 */
if ( ! function_exists('get_job_detail') ) {

    function get_job_detail($job_id) {
        $post = get_post($job_id);
        return $post;
    }

}

/**
 * Start Function how to Check Candidate Applications
 */
if ( ! function_exists('check_candidate_applications') ) {

    function check_candidate_applications($candidate_meta_id) {
        global $current_user;
        $result_count = 0;
        $cs_emp_funs = new cs_employer_functions();
        if ( is_user_logged_in() && $cs_emp_funs->is_employer() ) {
            $employer_id = $current_user->ID;   // employer id
            // get all applied job array for candidate
            $cs_jobapplied_array = get_user_meta($candidate_meta_id, 'cs-user-jobs-applied-list', true);
            if ( ! empty($cs_jobapplied_array) )
                $cs_jobapplied = array_column_by_two_dimensional($cs_jobapplied_array, 'post_id');
            else
                $cs_jobapplied = array();

            if ( is_array($cs_jobapplied) && sizeof($cs_jobapplied) > 0 ) {
                $args = array( 'posts_per_page' => "-1",
                    'post__in' => $cs_jobapplied,
                    'post_type' => 'jobs',
                    'order' => "ASC",
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'cs_job_expired',
                            'value' => strtotime(current_time('d-m-Y')),
                            'compare' => '>=',
                            'type' => 'numeric',
                        ),
                        array(
                            'key' => 'cs_job_username',
                            'value' => $employer_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'cs_job_status',
                            'value' => 'delete',
                            'compare' => '!=',
                        ),
                    ),
                );
                $custom_query = new WP_Query($args);
                $result_count = $custom_query->found_posts;
            }
        }
        return $result_count;
    }

}

/**
 * Start Function how to get User Address for listing
 */
if ( ! function_exists('get_user_address_string_for_list') ) {

    function get_user_address_string_for_list($post_id, $type = 'post') {
        $complete_address = '';
        if ( $type == 'post' ) {
            $cs_post_loc_address = get_post_meta($post_id, 'cs_post_loc_address', true);
            $cs_post_loc_country = get_post_meta($post_id, 'cs_post_loc_country', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_country, 'cs_locations');
            $cs_post_loc_country = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_region = get_post_meta($post_id, 'cs_post_loc_region', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_region, 'cs_locations');
            $cs_post_loc_region = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_city = get_post_meta($post_id, 'cs_post_loc_city', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_city, 'cs_locations');
            $cs_post_loc_city = isset($selected_spec->name) ? $selected_spec->name : '';
        } else {
            $cs_post_loc_address = get_the_author_meta('cs_post_loc_address', $post_id);
            $cs_post_loc_country = get_the_author_meta('cs_post_loc_country', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_country, 'cs_locations');
            $cs_post_loc_country = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_region = get_the_author_meta('cs_post_loc_region', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_region, 'cs_locations');
            $cs_post_loc_region = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_city = get_the_author_meta('cs_post_loc_city', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_city, 'cs_locations');
            $cs_post_loc_city = isset($selected_spec->name) ? $selected_spec->name : '';
        }
        $complete_address = $cs_post_loc_city != '' ? $cs_post_loc_city . ', ' : '';
        $complete_address .= $cs_post_loc_country != '' ? $cs_post_loc_country . ' ' : '';
        return $complete_address;
    }

}

/**
 * Start Function how to get User Address details
 */
if ( ! function_exists('get_user_address_string_for_detail') ) {

    function get_user_address_string_for_detail($post_id, $type = 'post') {
        $job_address = '';
        if ( $type == 'post' ) {
            $cs_post_loc_address = get_post_meta($post_id, 'cs_post_loc_address', true);
            $cs_post_loc_country = get_post_meta($post_id, 'cs_post_loc_country', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_country, 'cs_locations');
            $cs_post_loc_country = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_region = get_post_meta($post_id, 'cs_post_loc_region', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_region, 'cs_locations');
            $cs_post_loc_region = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_city = get_post_meta($post_id, 'cs_post_loc_city', true);
            $selected_spec = get_term_by('slug', $cs_post_loc_city, 'cs_locations');
            $cs_post_loc_city = isset($selected_spec->name) ? $selected_spec->name : '';
        } else {
            $cs_post_loc_address = get_the_author_meta('cs_post_loc_address', $post_id);
            $cs_post_loc_country = get_the_author_meta('cs_post_loc_country', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_country, 'cs_locations');
            $cs_post_loc_country = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_region = get_the_author_meta('cs_post_loc_region', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_region, 'cs_locations');
            $cs_post_loc_region = isset($selected_spec->name) ? $selected_spec->name : '';
            $cs_post_loc_city = get_the_author_meta('cs_post_loc_city', $post_id);
            $selected_spec = get_term_by('slug', $cs_post_loc_city, 'cs_locations');
            $cs_post_loc_city = isset($selected_spec->name) ? $selected_spec->name : '';
        }
        if ( $cs_post_loc_address != '' ) {
            $job_address .= $cs_post_loc_address . " ";
        }
        return $job_address;
    }

}

/**
 * @get specialism headings
 */
if ( ! function_exists('get_specialism_headings') ) {

    function get_specialism_headings($specialisms) {
        $return_str = '';
        if ( count($specialisms) > 0 ) {
            if ( isset($specialisms[0]) )
                $specialisms_str = $specialisms[0];
            if ( strpos($specialisms_str, ',') !== FALSE ) {
                $specialisms = explode(",", $specialisms_str);
            }
            $i = 1;
            foreach ( $specialisms as $single_specialism_title ) {
                $selected_spec_data = get_term_by('slug', $single_specialism_title, 'specialisms');
                if ( isset($selected_spec_data) )
                    $return_str .= isset($selected_spec_data->name) ? ($selected_spec_data->name) : '';
                if ( $i != count($specialisms) )
                    $return_str .= ", ";
                else
                    $return_str .= " ";
                $i ++;
            }
        }
        $return_str .= esc_html__("Job(s)", "jobhunt");
        return $return_str;
    }

}

/**
 * Start Function how to get using servers and servers protocols
 */
if ( ! function_exists('cs_server_protocol') ) {

    function cs_server_protocol() {
        if ( is_ssl() ) {
            return 'https://';
        }
        return 'http://';
    }

}

/**
 * start get Multiple Parameters function 
 */
if ( ! function_exists('getMultipleParameters') ) {

    function getMultipleParameters($query_string = '') {
        if ( $query_string == '' )
            $query_string = $_SERVER['QUERY_STRING'];
        $params = explode('&', $query_string);
        foreach ( $params as $param ) {
            $k = $param;
            $v = '';
            if ( strpos($param, '=') ) {
                list($name, $value) = explode('=', $param);
                $k = rawurldecode($name);
                $v = rawurldecode($value);
            }
            if ( isset($query[$k]) ) {
                if ( is_array($query[$k]) ) {
                    $query[$k][] = $v;
                } else {
                    $query[$k][] = array( $query[$k], $v );
                }
            } else {
                $query[$k][] = $v;
            }
        }
        return $query;
    }

}

/**
 * Start Function how to arrang jobs in shorlist
 */
if ( ! function_exists('cs_job_shortlist_load') ) {

    function cs_job_shortlist_load() {
        candidate_header_wishlist();
        die();
    }

    add_action("wp_ajax_cs_job_shortlist_load", "cs_job_shortlist_load");
    add_action("wp_ajax_nopriv_cs_job_shortlist_load", "cs_job_shortlist_load");
}

/**
 * Start Function how to Set Geo Location
 */
if ( ! function_exists('cs_set_geo_loc') ) {

    function cs_set_geo_loc() {
        $cs_geo_loc = isset($_POST['geo_loc']) ? $_POST['geo_loc'] : '';
        if ( isset($_COOKIE['cs_geo_loc']) ) {
            unset($_COOKIE['cs_geo_loc']);
            setcookie('cs_geo_loc', null, -1, '/');
        }
        if ( isset($_COOKIE['cs_geo_switch']) ) {
            unset($_COOKIE['cs_geo_switch']);
            setcookie('cs_geo_switch', null, -1, '/');
        }
        setcookie('cs_geo_loc', $cs_geo_loc, current_time('timestamp') + 86400, '/');
        setcookie('cs_geo_switch', 'on', current_time('timestamp') + 86400, '/');
    }

    add_action("wp_ajax_cs_set_geo_loc", "cs_set_geo_loc");
    add_action("wp_ajax_nopriv_cs_set_geo_loc", "cs_set_geo_loc");
}

/**
 * Start Function how to UnSet Geo Location
 */
if ( ! function_exists('cs_unset_geo_loc') ) {

    function cs_unset_geo_loc() {
        if ( isset($_COOKIE['cs_geo_loc']) ) {
            unset($_COOKIE['cs_geo_loc']);
            setcookie('cs_geo_loc', null, -1, '/');
        }
        if ( isset($_COOKIE['cs_geo_switch']) ) {
            unset($_COOKIE['cs_geo_switch']);
            setcookie('cs_geo_switch', null, -1, '/');
        }
        setcookie('cs_geo_loc', '', current_time('timestamp') + 86400, '/');
        setcookie('cs_geo_switch', 'off', current_time('timestamp') + 86400, '/');
        die;
    }

    add_action("wp_ajax_cs_unset_geo_loc", "cs_unset_geo_loc");
    add_action("wp_ajax_nopriv_cs_unset_geo_loc", "cs_unset_geo_loc");
}

/**
 * @set sort filter
 */
if ( ! function_exists('cs_set_sort_filter') ) {

    function cs_set_sort_filter() {
        $json = array();
        if ( session_id() == '' ) {
            session_start();
        }
        $field_name = $_REQUEST['field_name'];
        $field_name_value = $_REQUEST['field_name_value'];
        $_SESSION[$field_name] = $field_name_value;
        set_transient($field_name, $field_name_value, HOUR_IN_SECONDS);
        $json['type'] = 'success';
        echo json_encode($json);
        die();
    }

    add_action("wp_ajax_cs_set_sort_filter", "cs_set_sort_filter");
    add_action("wp_ajax_nopriv_cs_set_sort_filter", "cs_set_sort_filter");
}

/**
 * Start Function how to check if Image Exists
 */
if ( ! function_exists('cs_image_exist') ) {

    function cs_image_exist($sFilePath) {
        $path = site_url();
        add_filter('upload_dir', 'cs_user_images_custom_directory');
        $img_formats = array( "png", "jpg", "jpeg", "gif", "tiff" ); //Etc. . . 
        $path_info = pathinfo($sFilePath);
        $upload_dir = wp_upload_dir();
        remove_filter('upload_dir', 'cs_user_images_custom_directory');
        if ( isset($path_info['extension']) && in_array(strtolower($path_info['extension']), $img_formats) ) {
            if ( ! filter_var($sFilePath, FILTER_VALIDATE_URL) === false ) {
                $cs_file_response = wp_remote_get($sFilePath);
                if ( ! is_wp_error($cs_file_response) ) {
                    if ( is_array($cs_file_response) && isset($cs_file_response['headers']['content-type']) && strpos($cs_file_response['headers']['content-type'], 'image') !== false ) {
                        return true;
                    }
                } else {
                    $imge_url = explode(site_url() . '/', $sFilePath);
                    $image_name = end($imge_url);
                    $upload_dir = get_home_path();
                    if ( file_exists($upload_dir . $image_name) ) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}

/**
 * @get query whereclase by array
 */
if ( ! function_exists('cs_get_query_whereclase_by_array') ) {

    function cs_get_query_whereclase_by_array($array, $user_meta = false) {
        $id = '';
        $flag_id = 0;
        if ( isset($array) && is_array($array) ) {
            foreach ( $array as $var => $val ) {
                $string = ' ';
                $string .= ' AND (';
                if ( isset($val['key']) || isset($val['value']) ) {
                    $string .= get_meta_condition($val);
                } else {  // if inner array 
                    if ( isset($val) && is_array($val) ) {
                        foreach ( $val as $inner_var => $inner_val ) {
                            $inner_relation = isset($inner_val['relation']) ? $inner_val['relation'] : 'and';
                            $second_string = '';
                            if ( isset($inner_val) && is_array($inner_val) ) {
                                $string .= "( ";
                                $inner_arr_count = is_array($inner_val) ? count($inner_val) : '';
                                $inner_flag = 1;
                                foreach ( $inner_val as $inner_val_var => $inner_val_value ) {
                                    if ( is_array($inner_val_value) ) {
                                        $string .= "( ";
                                        $string .= get_meta_condition($inner_val_value);
                                        $string .= ' )';
                                        if ( $inner_flag != $inner_arr_count )
                                            $string .= ' ' . $inner_relation . ' ';
                                    }
                                    $inner_flag ++;
                                }
                                $string .= ' )';
                            }
                        }
                    }
                }
                $string .= " ) ";
                $id_condtion = '';
                if ( isset($id) && $flag_id != 0 ) {
                    $id = implode(",", $id);
                    if ( empty($id) ) {
                        $id = 0;
                    }
                    if ( $user_meta == true ) {
                        $id_condtion = ' AND user_id IN (' . $id . ')';
                    } else {
                        $id_condtion = ' AND post_id IN (' . $id . ')';
                    }
                }
                if ( $user_meta == true ) {
                    $id = cs_get_user_id_by_whereclase($string . $id_condtion);
                } else {
                    $id = cs_get_post_id_by_whereclase($string . $id_condtion);
                }
                $flag_id = 1;
            }
        }
        return $id;
    }

}

/**
 * Start Function how to get Meta using Conditions
 */
if ( ! function_exists('get_meta_condition') ) {

    function get_meta_condition($val) {
        $string = '';
        $meta_key = isset($val['key']) ? $val['key'] : '';
        $compare = isset($val['compare']) ? $val['compare'] : '=';
        $meta_value = isset($val['value']) ? $val['value'] : '';
        $string .= " meta_key='" . $meta_key . "' AND ";
        $type = isset($val['type']) ? $val['type'] : '';
        if ( $compare == 'BETWEEN' || $compare == 'between' || $compare == 'Between' ) {
            $meta_val1 = '';
            $meta_val2 = '';
            if ( isset($meta_value) && is_array($meta_value) ) {
                $meta_val1 = isset($meta_value[0]) ? $meta_value[0] : '';
                $meta_val2 = isset($meta_value[1]) ? $meta_value[1] : '';
            }
            if ( $type != '' && strtolower($type) == 'numeric' ) {
                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND " . $meta_val2 . " ";
            } else {
                $string .= " meta_value BETWEEN '" . $meta_val1 . "' AND '" . $meta_val2 . "' ";
            }
        } elseif ( $compare == 'like' || $compare == 'LIKE' || $compare == 'Like' ) {
            $string .= " meta_value LIKE '%" . $meta_value . "%' ";
        } else {
            if ( $type != '' && strtolower($type) == 'numeric' ) {
                $string .= " meta_value" . $compare . " " . $meta_value . " ";
            } else {
                $string .= " meta_value" . $compare . "'" . $meta_value . "' ";
            }
        }
        return $string;
    }

}

/**
 * Start Function how to get post id using whereclase Query
 */
if ( ! function_exists('cs_get_post_id_by_whereclase') ) {

    function cs_get_post_id_by_whereclase($whereclase) {
        global $wpdb;
        $qry = "SELECT post_id FROM $wpdb->postmeta WHERE 1=1 " . $whereclase;
        return $posts = $wpdb->get_col($qry);
    }

}

if ( ! function_exists('cs_get_user_id_by_whereclase') ) {

    function cs_get_user_id_by_whereclase($whereclase) {
        global $wpdb;
        $qry = "SELECT user_id FROM $wpdb->usermeta WHERE 1=1 " . $whereclase;
        return $posts = $wpdb->get_col($qry);
    }

}

/**
 * @array_flatten
 */
if ( ! function_exists('array_flatten') ) {

    function array_flatten($array) {
        $return = array();
        foreach ( $array as $key => $value ) {
            if ( is_array($value) ) {
                $return = array_merge($return, array_flatten($value));
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }

}

/**
 * Start Function how to remove Dupplicate variable value
 */
if ( ! function_exists('remove_dupplicate_var_val') ) {

    function remove_dupplicate_var_val($qry_str) {
        $old_string = $qry_str;
        $qStr = str_replace("?", "", $qry_str);
        $query = explode('&', $qStr);
        $params = array();
        if ( isset($query) && ! empty($query) ) {
            foreach ( $query as $param ) {
                if ( ! empty($param) ) {
                    $param_array = explode('=', $param);
                    $name = isset($param_array[0]) ? $param_array[0] : '';
                    $value = isset($param_array[1]) ? $param_array[1] : '';
                    $new_str = $name . "=" . $value;
                    // count matches
                    $count_str = substr_count($old_string, $new_str);
                    $count_str = $count_str - 1;
                    if ( $count_str > 0 ) {
                        $old_string = cs_str_replace_limit($new_str, "", $old_string, $count_str);
                    }
                    $old_string = str_replace("&&", "&", $old_string);
                }
            }
        }
        $old_string = str_replace("?&", "?", $old_string);
        return $old_string;
    }

}

/**
 * @str replace limit
 */
if ( ! function_exists('cs_str_replace_limit') ) {

    function cs_str_replace_limit($search, $replace, $string, $limit = 1) {
        if ( is_bool($pos = (strpos($string, $search))) )
            return $string;
        $search_len = strlen($search);
        for ( $i = 0; $i < $limit; $i ++  ) {
            $string = substr_replace($string, $replace, $pos, $search_len);
            if ( is_bool($pos = (strpos($string, $search))) )
                break;
        }
        return $string;
    }

}

/**
 * Start Function how to allow the user for adding special characters
 */
if ( ! function_exists('cs_allow_special_char') ) {

    function cs_allow_special_char($input = '') {
        $output = $input;
        return $output;
    }

}

/* Thumb size On Blogs Detail */

add_image_size('cs_media_1', 870, 489, true);

/* Thumb size On Related Blogs On Detail, blogs on listing, Candidate Detail Portfolio */

add_image_size('cs_media_2', 270, 203, true);

/* Thumb size On Blogs On slider, blogs on listing, Candidate Detail Portfolio */

add_image_size('cs_media_3', 236, 168, true);

add_image_size('cs_media_4', 200, 200, true);

/* Thumb size On BEmployer Listing, Employer Listing View 2,Candidate Detail ,User Resume, company profile */

add_image_size('cs_media_5', 180, 135, true);

/* Thumb size On Candidate ,Candidate , Listing 2, Employer Detail,Related Jobs */

add_image_size('cs_media_6', 150, 113, true);

add_image_size('cs_media_7', 120, 90, true);



/**
 * Start Function how to share the posts
 */
if ( ! function_exists('cs_social_share') ) {

    function cs_social_share($echo = true) {
        global $cs_plugin_options;
        $cs_plugin_options = get_option('cs_plugin_options');
        $twitter = '';
        $facebook = '';
        $google_plus = '';
        $tumblr = '';
        $dribbble = '';
        $instagram = '';
        $share = '';
        $stumbleupon = '';
        $youtube = '';
        $pinterst = '';
        if ( isset($cs_plugin_options['cs_twitter_share']) ) {
            $twitter = $cs_plugin_options['cs_twitter_share'];
        }
        if ( isset($cs_plugin_options['cs_facebook_share']) ) {
            $facebook = $cs_plugin_options['cs_facebook_share'];
        }
        if ( isset($cs_plugin_options['cs_google_plus_share']) ) {
            $google_plus = $cs_plugin_options['cs_google_plus_share'];
        }
        if ( isset($cs_plugin_options['cs_tumblr_share']) ) {
            $tumblr = $cs_plugin_options['cs_tumblr_share'];
        }
        if ( isset($cs_plugin_options['cs_dribbble_share']) ) {
            $dribbble = $cs_plugin_options['cs_dribbble_share'];
        }
        if ( isset($cs_plugin_options['cs_instagram_share']) ) {
            $instagram = $cs_plugin_options['cs_instagram_share'];
        }
        if ( isset($cs_plugin_options['cs_share_share']) ) {
            $share = $cs_plugin_options['cs_share_share'];
        }
        if ( isset($cs_plugin_options['cs_stumbleupon_share']) ) {
            $stumbleupon = $cs_plugin_options['cs_stumbleupon_share'];
        }
        if ( isset($cs_plugin_options['cs_youtube_share']) ) {
            $youtube = $cs_plugin_options['cs_youtube_share'];
        }
        if ( isset($cs_plugin_options['cs_pintrest_share']) ) {
            $pinterst = $cs_plugin_options['cs_pintrest_share'];
        }
        cs_addthis_script_init_method();
        $html = '';
        if ( $twitter == 'on' or $facebook == 'on' or $google_plus == 'on' or $pinterst == 'on' or $tumblr == 'on' or $dribbble == 'on' or $instagram == 'on' or $share == 'on' or $stumbleupon == 'on' or $youtube == 'on' ) {
            if ( isset($facebook) && $facebook == 'on' ) {
                $html .= '<li><a class="addthis_button_facebook" data-original-title="' . esc_html__('Facebook', 'jobhunt') . '"><i class="icon-facebook2"></i></a></li>';
            }
            if ( isset($twitter) && $twitter == 'on' ) {
                $html .= '<li><a class="addthis_button_twitter" data-original-title="' . esc_html__('twitter', 'jobhunt') . '"><i class="icon-twitter2"></i></a></li>';
            }
            if ( isset($google_plus) && $google_plus == 'on' ) {
                $html .= '<li><a class="addthis_button_google" data-original-title="' . esc_html__('google-plus', 'jobhunt') . '"><i class="icon-googleplus7"></i></a></li>';
            }
            if ( isset($tumblr) && $tumblr == 'on' ) {
                $html .= '<li><a class="addthis_button_tumblr" data-original-title="' . esc_html__('Tumblr', 'jobhunt') . '"><i class="icon-tumblr5"></i></a></li>';
            }
            if ( isset($dribbble) && $dribbble == 'on' ) {
                $html .= '<li><a class="addthis_button_dribbble" data-original-title="' . esc_html__('Dribbble', 'jobhunt') . '"><i class="icon-dribbble7"></i></a></li>';
            }
            if ( isset($instagram) && $instagram == 'on' ) {
                $html .= '<li><a class="addthis_button_instagram" data-original-title="' . esc_html__('Instagram', 'jobhunt') . '"><i class="icon-instagram4"></i></a></li>';
            }
            if ( isset($stumbleupon) && $stumbleupon == 'on' ) {
                $html .= '<li><a class="addthis_button_stumbleupon" data-original-title="' . esc_html__('stumbleupon', 'jobhunt') . '"><i class="icon-stumbleupon4"></i></a></li>';
            }
            if ( isset($youtube) && $youtube == 'on' ) {

                $html .= '<li><a class="addthis_button_youtube" data-original-title="' . esc_html__('Youtube', 'jobhunt') . '"><i class="icon-youtube"></i></a></li>';
            }
            if ( isset($pinterst) && $pinterst == 'on' ) {

                $html .= '<li><a class="addthis_button_youtube" data-original-title="' . esc_html__('Pinterest', 'jobhunt') . '"><i class="icon-pinterest"></i></a></li>';
            }
            if ( isset($share) && $share == 'on' ) {

                $html .= '<li><a class="cs-more addthis_button_compact at300m"></a></li>';
            }
            $html .= '</ul>';
        }
        if ( $echo ) {
            echo balanceTags($html, true);
        } else {
            return balanceTags($html, true);
        }
    }

}

/**
 * Start Function how to share the posts
 */
if ( ! function_exists('cs_social_more') ) {

    function cs_social_more() {
        global $cs_plugin_options;
        $cs_plugin_options = get_option('cs_plugin_options');
        $twitter = '';
        $facebook = '';
        $google_plus = '';
        $tumblr = '';
        $dribbble = '';
        $instagram = '';
        $share = '';
        $stumbleupon = '';
        $youtube = '';
        $pinterst = '';
        if ( isset($cs_plugin_options['cs_twitter_share']) ) {
            $twitter = $cs_plugin_options['cs_twitter_share'];
        }
        if ( isset($cs_plugin_options['cs_facebook_share']) ) {
            $facebook = $cs_plugin_options['cs_facebook_share'];
        }
        if ( isset($cs_plugin_options['cs_google_plus_share']) ) {
            $google_plus = $cs_plugin_options['cs_google_plus_share'];
        }
        if ( isset($cs_plugin_options['cs_tumblr_share']) ) {
            $tumblr = $cs_plugin_options['cs_tumblr_share'];
        }
        if ( isset($cs_plugin_options['cs_dribbble_share']) ) {
            $dribbble = $cs_plugin_options['cs_dribbble_share'];
        }
        if ( isset($cs_plugin_options['cs_instagram_share']) ) {
            $instagram = $cs_plugin_options['cs_instagram_share'];
        }
        if ( isset($cs_plugin_options['cs_share_share']) ) {
            $share = $cs_plugin_options['cs_share_share'];
        }
        if ( isset($cs_plugin_options['cs_stumbleupon_share']) ) {
            $stumbleupon = $cs_plugin_options['cs_stumbleupon_share'];
        }
        if ( isset($cs_plugin_options['cs_youtube_share']) ) {
            $youtube = $cs_plugin_options['cs_youtube_share'];
        }
        if ( isset($cs_plugin_options['cs_pintrest_share']) ) {
            $pinterst = $cs_plugin_options['cs_pintrest_share'];
        }
        cs_addthis_script_init_method();
        $html = '';
        if ( isset($share) && $share == 'on' ) {
            $html .= '<a class="addthis_button_compact share-btn">' . esc_html__('Share Job', 'jobhunt') . '</a>';
        }
        echo balanceTags($html, true);
    }

}

/**
 * Start Function how to add tool tip text 
 */
if ( ! function_exists('cs_tooltip_helptext') ) {

    function cs_tooltip_helptext($popover_text = '', $return_html = true) {
        $popover_link = '';
        if ( isset($popover_text) && $popover_text != '' ) {
            $popover_link = '<a class="cs-help cs" data-toggle="popover" data-placement="right" data-trigger="hover" data-content="' . $popover_text . '"><i class="icon-help"></i></a>';
        }
        if ( $return_html == true ) {
            return $popover_link;
        } else {
            echo $popover_link;
        }
    }

}

/**
 * Start Function how to add tool tip text without icon only tooltip string
 */
if ( ! function_exists('cs_tooltip_helptext_string') ) {

    function cs_tooltip_helptext_string($popover_text = '', $return_html = true, $class = '') {
        $popover_link = '';
        if ( isset($popover_text) && $popover_text != '' ) {
            $popover_link = ' class="cs-help cs ' . $class . '" data-toggle="popover" data-placement="right" data-trigger="hover" data-content="' . $popover_text . '" ';
        }
        if ( $return_html == true ) {
            return $popover_link;
        } else {
            echo $popover_link;
        }
    }

}

// Fontawsome icon box for Theme Options
if ( ! function_exists('cs_iconlist_plugin_options') ) {

    function cs_iconlist_plugin_options($icon_value = '', $id = '', $name = '') {
        global $cs_form_fields2;
        ob_start();
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var e9_element = $('#e9_element_<?php echo cs_allow_special_char($id); ?>').fontIconPicker({
                    theme: 'fip-bootstrap'

                });
                // Add the event on the button
                $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').on('click', function (e) {
                    e.preventDefault();
                    // Show processing message
                    $(this).prop('disabled', true).html('<i class="icon-cog demo-animate-spin"></i> <?php esc_html_e('Please wait...', 'jobhunt'); ?>');
                    $.ajax({
                        url: '<?php echo wp_jobhunt::plugin_url() . 'assets/icomoon/js/selection.json' ?>',
                        type: 'GET',
                        dataType: 'json'

                    })
                            .done(function (response) {
                                // Get the class prefix
                                var classPrefix = response.preferences.fontPref.prefix,
                                        icomoon_json_icons = [],
                                        icomoon_json_search = [];

                                $.each(response.icons, function (i, v) {
                                    icomoon_json_icons.push(classPrefix + v.properties.name);
                                    if (v.icon && v.icon.tags && v.icon.tags.length) {
                                        icomoon_json_search.push(v.properties.name + ' ' + v.icon.tags.join(' '));
                                    } else {
                                        icomoon_json_search.push(v.properties.name);
                                    }
                                });
                                // Set new fonts on fontIconPicker
                                e9_element.setIcons(icomoon_json_icons, icomoon_json_search);
                                // Show success message and disable
                                $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-success').text('<?php esc_html_e('Successfully loaded icons', 'jobhunt'); ?>').prop('disabled', true);

                            })
                            .fail(function () {
                                // Show error message and enable

                                $('#e9_buttons_<?php echo cs_allow_special_char($id); ?> button').removeClass('btn-primary').addClass('btn-danger').text('<?php esc_html_e('Error: Try Again?', 'jobhunt'); ?>').prop('disabled', false);
                            });
                    e.stopPropagation();

                });
                jQuery("#e9_buttons_<?php echo cs_allow_special_char($id); ?> button").click();
            });
        </script>

        <?php
        $cs_opt_array = array(
            'id' => '',
            'std' => cs_allow_special_char($icon_value),
            'cust_id' => "e9_element_" . cs_allow_special_char($id),
            'cust_name' => cs_allow_special_char($name) . "[]",
            'classes' => '',
            'extra_atr' => '',
        );

        $cs_form_fields2->cs_form_text_render($cs_opt_array);
        ?>
        <span id="e9_buttons_<?php echo cs_allow_special_char($id); ?>" style="display:none">
            <button autocomplete="off" type="button" class="btn btn-primary"><?php esc_html_e('Load from IcoMoon selection.json', 'jobhunt') ?></button>
        </span>
        <?php
        $fontawesome = ob_get_clean();
        return $fontawesome;
    }

}

/*
 * start information messages
 */
if ( ! function_exists('cs_info_messages_listing') ) {

    function cs_info_messages_listing($message = '', $return = true, $classes = '', $before = '', $after = '') {
        global $post;
        if ( $message == '' ) {
            $message = esc_html__('There is no record in list', 'jobhunt');
        }
        $output = '';
        $class_str = '';
        if ( $classes != '' ) {
            $class_str .= ' class="' . $classes . '"';
        }
        $before_str = '';
        if ( $before != '' ) {
            $before_str .= $before;
        }
        $after_str = '';
        if ( $after != '' ) {
            $after_str .= $after;
        }
        $output .= $before_str;
        $output .= '<span' . $class_str . '>';
        $output .= esc_html__($message, 'jobhunt');
        $output .= '</span>';
        $output .= $after_str;
        if ( $return == true ) {
            return force_balance_tags($output);
        } else {
            echo force_balance_tags($output);
        }
    }

}

/* define it global */

$umlaut_chars['in'] = array( chr(196), chr(228), chr(214), chr(246), chr(220), chr(252), chr(223) );
$umlaut_chars['ecto'] = array( 'Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß' );
$umlaut_chars['html'] = array( '&Auml;', '&auml;', '&Ouml;', '&ouml;', '&Uuml;', '&uuml;', '&szlig;' );
$umlaut_chars['feed'] = array( '&#196;', '&#228;', '&#214;', '&#246;', '&#220;', '&#252;', '&#223;' );
$umlaut_chars['utf8'] = array( utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'), utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß') );
$umlaut_chars['perma'] = array( 'Ae', 'ae', 'Oe', 'oe', 'Ue', 'ue', 'ss' );

/* sanitizes the titles to get qualified german permalinks with  correct transliteration */

function de_DE_umlaut_permalinks($title) {
    global $umlaut_chars;
    if ( seems_utf8($title) ) {
        $invalid_latin_chars = array( chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe', chr(197) . chr(160) => 'S', chr(197) . chr(189) => 'Z', chr(197) . chr(161) => 's', chr(197) . chr(190) => 'z', chr(226) . chr(130) . chr(172) => 'E' );
        $title = utf8_decode(strtr($title, $invalid_latin_chars));
    }
    $title = str_replace($umlaut_chars['ecto'], $umlaut_chars['perma'], $title);
    $title = str_replace($umlaut_chars['in'], $umlaut_chars['perma'], $title);
    $title = str_replace($umlaut_chars['html'], $umlaut_chars['perma'], $title);
    $title = sanitize_title_with_dashes($title);
    return $title;
}

add_filter('sanitize_title', 'de_DE_umlaut_permalinks');

if ( ! function_exists('wp_new_user_notification') ) :

    function wp_new_user_notification($user_id, $plaintext_pass = ' ') {
        $user = new WP_User($user_id);
        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
        if ( empty($plaintext_pass) ) {
            return;
        }
        do_action('jobhunt_new_user_notification_site_owner', $user_login, $user_email);
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        wp_set_password($random_password, $user_id);
        $reg_user = get_user_by('ID', $user_id);
        do_action('jobhunt_employer_register', $reg_user, $random_password);
    }

endif;


if ( ! function_exists('cs_get_loginuser_role') ) :

    function cs_get_loginuser_role() {
        global $current_user;
        $cs_user_role = '';
        if ( is_user_logged_in() ) {
            wp_get_current_user();
            $user_roles = isset($current_user->roles) ? $current_user->roles : '';
            $cs_user_role = 'other';
            if ( ($user_roles != '' && in_array("cs_employer", $user_roles) ) ) {
                $cs_user_role = 'cs_employer';
            } elseif ( ($user_roles != '' && in_array("cs_candidate", $user_roles) ) ) {
                $cs_user_role = 'cs_candidate';
            }
        }
        return $cs_user_role;
    }

endif;

//change author/username base to users/userID
add_action('init', 'change_author_permalinks');

function change_author_permalinks() {
    global $wp_rewrite, $cs_plugin_options;

    $general_slug = isset($cs_plugin_options['cs_author_page_slug']) && $cs_plugin_options['cs_author_page_slug'] != '' ? $cs_plugin_options['cs_author_page_slug'] : 'user';
    $candidate_slug = isset($cs_plugin_options['cs_candidate_page_slug']) && $cs_plugin_options['cs_candidate_page_slug'] != '' ? $cs_plugin_options['cs_candidate_page_slug'] : 'candidate';
    $employer_slug = isset($cs_plugin_options['cs_employer_page_slug']) && $cs_plugin_options['cs_employer_page_slug'] != '' ? $cs_plugin_options['cs_employer_page_slug'] : 'employer';
    $cs_author_levels = array( $general_slug, $candidate_slug, $employer_slug );

    add_rewrite_tag('%author_level%', '(' . implode('|', $cs_author_levels) . ')');
    $wp_rewrite->author_base = '%author_level%';
    $wp_rewrite->flush_rules();
}

add_filter('author_rewrite_rules', 'cs_author_rewrite_rules');

function cs_author_rewrite_rules($author_rewrite_rules) {
    foreach ( $author_rewrite_rules as $pattern => $substitution ) {
        if ( FALSE === strpos($substitution, 'author_name') ) {
            unset($author_rewrite_rules[$pattern]);
        }
    }
    return $author_rewrite_rules;
}

add_filter('author_link', 'cs_cus_author_link', 10, 2);

function cs_cus_author_link($link, $author_id) {
    global $cs_plugin_options;

    $user_obj = get_user_by('ID', $author_id);
    $author_level = isset($cs_plugin_options['cs_author_page_slug']) && $cs_plugin_options['cs_author_page_slug'] != '' ? $cs_plugin_options['cs_author_page_slug'] : 'user';
    $candidate_slug = isset($cs_plugin_options['cs_candidate_page_slug']) && $cs_plugin_options['cs_candidate_page_slug'] != '' ? $cs_plugin_options['cs_candidate_page_slug'] : 'candidate';
    $employer_slug = isset($cs_plugin_options['cs_employer_page_slug']) && $cs_plugin_options['cs_employer_page_slug'] != '' ? $cs_plugin_options['cs_employer_page_slug'] : 'employer';

    if ( isset($user_obj->roles) && is_array($user_obj->roles) && in_array('cs_candidate', $user_obj->roles) ) {
        $author_level = $candidate_slug;
    } else if ( isset($user_obj->roles) && is_array($user_obj->roles) && in_array('cs_employer', $user_obj->roles) ) {
        $author_level = $employer_slug;
    }
    $link = str_replace('%author_level%', $author_level, $link);
    return $link;
}

add_filter('query_vars', 'users_query_vars');

function users_query_vars($vars) {
    global $cs_plugin_options;
    // add lid to the valid list of variables
    $author_slug = isset($cs_plugin_options['cs_author_page_slug']) && $cs_plugin_options['cs_author_page_slug'] != '' ? $cs_plugin_options['cs_author_page_slug'] : 'user';
    $candidate_slug = isset($cs_plugin_options['cs_candidate_page_slug']) && $cs_plugin_options['cs_candidate_page_slug'] != '' ? $cs_plugin_options['cs_candidate_page_slug'] : 'candidate';
    $employer_slug = isset($cs_plugin_options['cs_employer_page_slug']) && $cs_plugin_options['cs_employer_page_slug'] != '' ? $cs_plugin_options['cs_employer_page_slug'] : 'employer';

    $new_vars = array( $author_slug, $candidate_slug, $employer_slug );
    $vars = $new_vars + $vars;
    return $vars;
}

add_filter('query_vars', 'location_query_vars');

function location_query_vars($query_vars) {
    $query_vars['location'] = 'location';
    return $query_vars;
}

add_action('init', 'custom_rewrite_rule', 10, 0);

function custom_rewrite_rule() {
    add_rewrite_rule('(vacature)/([^/\?]*)', 'index.php?pagename=vacature&specialisms=$matches[2]', 'top');
    add_rewrite_rule('(employer-simple)/(.+)$', 'index.php?pagename=employer-simple&location=$matches[2]', 'top');
}

/*
 * @Shortcode Name: Start function for Map shortcode/element front end view
 * @retrun
 */

if ( ! function_exists('cs_job_map') ) {

    function cs_job_map($atts, $content = "") {
        global $cs_plugin_options;
        $defaults = array(
            'column_size' => '1/1',
            'cs_map_section_title' => '',
            'map_title' => '',
            'map_height' => '',
            'map_lat' => '51.507351',
            'map_lon' => '-0.127758',
            'map_zoom' => '',
            'map_type' => '',
            'map_info' => '',
            'map_info_width' => '200',
            'map_info_height' => '200',
            'map_marker_icon' => '',
            'map_show_marker' => 'true',
            'map_controls' => '',
            'map_draggable' => '',
            'map_scrollwheel' => '',
            'map_conactus_content' => '',
            'map_border' => '',
            'map_border_color' => '',
            'cs_map_style' => '',
            'cs_map_class' => '',
            'cs_map_directions' => 'off'
        );
        extract(shortcode_atts($defaults, $atts));
        if ( $map_info_width == '' || $map_info_height == '' ) {
            $map_info_width = '300';
            $map_info_height = '150';
        }
        if ( isset($map_height) && $map_height == '' ) {
            $map_height = '500';
        }
        $map_dynmaic_no = rand(6548, 9999999);
        if ( $map_show_marker == "true" ) {
            $map_show_marker = " var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        title: '',
                        icon: '" . $map_marker_icon . "',
                        shadow: ''
                    });";
        } else {
            $map_show_marker = "var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        title: '',
                        icon: '',
                        shadow: ''
                    });";
        }
        $border = '';
        if ( isset($map_border) && $map_border == 'yes' && $map_border_color != '' ) {
            $border = 'border:1px solid ' . $map_border_color . '; ';
        }
        $map_type = isset($map_type) ? $map_type : '';
        $map_dynmaic_no = cs_generate_random_string('10');
        $html = '';
        $html .= '<div ' . $cs_map_class . ' style="animation-duration:">';
        $html .= '<div class="clear"></div>';
        $html .= '<div class="cs-map-section" style="' . $border . ';">';
        $html .= '<div class="cs-map">';
        $html .= '<div class="cs-map-content">';
        $html .= '<div class="mapcode iframe mapsection gmapwrapp" id="map_canvas' . $map_dynmaic_no . '" style="height:' . $map_height . 'px;"> </div>';

        if ( $cs_map_directions == 'off' ) {
            $html .= '<div id="cs-directions-panel"></div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= "<script type='text/javascript'>
                    jQuery(document).ready(function() {
                   
		    var panorama;
                    function initialize() {
                    var myLatlng = new google.maps.LatLng(" . $map_lat . ", " . $map_lon . ");
                    var mapOptions = {
                        zoom: " . $map_zoom . ",
                        scrollwheel: " . $map_scrollwheel . ",
                        draggable: " . $map_draggable . ",
                        streetViewControl: false,
                        center: myLatlng,
                       
                        disableDefaultUI: true,
                        };";
        if ( $cs_map_directions == 'on' ) {
            $html .= "var directionsDisplay;
                      var directionsService = new google.maps.DirectionsService();
                      directionsDisplay = new google.maps.DirectionsRenderer();";
        }
        $html .= "var map = new google.maps.Map(document.getElementById('map_canvas" . $map_dynmaic_no . "'), mapOptions);";

        if ( $cs_map_directions == 'on' ) {
            $html .= "directionsDisplay.setMap(map);
                        directionsDisplay.setPanel(document.getElementById('cs-directions-panel'));

                        function cs_calc_route() {
                                var myLatlng = new google.maps.LatLng(" . $map_lat . ", " . $map_lon . ");
                                var start = myLatlng;
                                var end = document.getElementById('cs_end_direction').value;
                                var mode = document.getElementById('cs_chng_dir_mode').value;
                                var request = {
                                        origin:start,
                                        destination:end,
                                        travelMode: google.maps.TravelMode[mode]
                                };
                                directionsService.route(request, function(response, status) {
                                        if (status == google.maps.DirectionsStatus.OK) {
                                                directionsDisplay.setDirections(response);
                                        }
                                });
                        }
                        document.getElementById('cs_search_direction').addEventListener('click', function() {
                                cs_calc_route();
                        });";
        }
        $html .= "
                        var style = '" . $cs_map_style . "';
                        if (style != '') {
                            var styles = cs_map_select_style(style);
                            if (styles != '') {
                                var styledMap = new google.maps.StyledMapType(styles,
                                        {name: 'Styled Map'});
                                map.mapTypes.set('map_style', styledMap);
                                map.setMapTypeId('map_style');
                            }
                        }
                        var infowindow = new google.maps.InfoWindow({
                            content: '" . $map_info . "',
                            maxWidth: " . $map_info_width . ",
                            maxHeight: " . $map_info_height . ",
                        });
                        " . $map_show_marker . "
                            if (infowindow.content != ''){
                              infowindow.open(map, marker);
                               map.panBy(1,-60);
                               google.maps.event.addListener(marker, 'click', function(event) {
                                infowindow.open(map, marker);
                               });
                            }
                            panorama = map.getStreetView();
                            panorama.setPosition(myLatlng);
                            panorama.setPov(({
                              heading: 265,
                              pitch: 0
                            }));
                    }			
                        function cs_toggle_street_view(btn) {
                          var toggle = panorama.getVisible();
                          if (toggle == false) {
                                if(btn == 'streetview'){
                                  panorama.setVisible(true);
                                }
                          } else {
                                if(btn == 'mapview'){
                                  panorama.setVisible(false);
                                }
                          }
                        }
                google.maps.event.addDomListener(window, 'load', initialize);
                });
                </script>";

        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }

}

/*
 * TinyMCE EDITOR "Biographical Info" USER PROFILE
 * */
if ( ! function_exists('cs_biographical_info_tinymce') ) {

    function cs_biographical_info_tinymce() {
        if ( basename($_SERVER['PHP_SELF']) == 'profile.php' || basename($_SERVER['PHP_SELF']) == 'user-edit.php' && function_exists('wp_editor') ) {
            wp_admin_css();
            wp_enqueue_script('utils');
            wp_enqueue_script('editor');
            do_action('admin_print_scripts');
            do_action("admin_print_styles-post-php");
            do_action('admin_print_styles');
            remove_all_filters('mce_external_plugins');
            add_filter('teeny_mce_before_init', create_function('$a', '
		$a["skin"] = "wp_theme";
		$a["height"] = "200";
		$a["width"] = "240";
		$a["onpageload"] = "";
		$a["mode"] = "exact";
		$a["elements"] = "description";
		$a["theme_advanced_buttons1"] = "formatselect, forecolor, bold, italic, pastetext, pasteword, bullist, numlist, link, unlink, outdent, indent, charmap, removeformat, spellchecker, fullscreen, wp_adv";
		$a["theme_advanced_buttons2"] = "underline, justifyleft, justifycenter, justifyright, justifyfull, forecolor, pastetext, undo, redo, charmap, wp_help";
		$a["theme_advanced_blockformats"] = "p,h2,h3,h4,h5,h6";
		$a["theme_advanced_disable"] = "strikethrough";
		return $a;'));
            //wp_editor('biography');
        }
    }

    add_action('admin_head', 'cs_biographical_info_tinymce');
}

function cs_jobhunt_encrypt($data) {
    $encrypt_data = base64_encode(htmlentities($data, ENT_COMPAT, 'ISO-8859-15'));
    return $encrypt_data;
}

function cs_jobhunt_decrypt($data) {
    $decrypt_data = html_entity_decode(base64_decode($data), ENT_COMPAT, 'ISO-8859-15');
    return $decrypt_data;
}

/*
  array column function for old php versions
 */
if ( ! function_exists('array_column') ) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null) {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ( $argc < 2 ) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if ( ! is_array($params[0]) ) {
            trigger_error(
                    'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING
            );
            return null;
        }
        if ( ! is_int($params[1]) && ! is_float($params[1]) && ! is_string($params[1]) && $params[1] !== null && ! (is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if ( isset($params[2]) && ! is_int($params[2]) && ! is_float($params[2]) && ! is_string($params[2]) && ! (is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if ( isset($params[2]) ) {
            if ( is_float($params[2]) || is_int($params[2]) ) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ( $paramsInput as $row ) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ( $paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row) ) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ( $paramsColumnKey === null ) {
                $valueSet = true;
                $value = $row;
            } elseif ( is_array($row) && array_key_exists($paramsColumnKey, $row) ) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ( $valueSet ) {
                if ( $keySet ) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }

}

if ( ! function_exists('jobcareer_custom_column_class') ) {

    function jobcareer_custom_column_class($column_size) {
        $coloumn_class = '';
        if ( isset($column_size) && $column_size <> '' ) {
            list($top, $bottom) = explode('/', $column_size);
            $width = $top / $bottom * 100;
            $width = (int) $width;
            $coloumn_class = '';
            if ( round($width) == '16' || round($width) < 16 ) {
                $coloumn_class = 'col-lg-2 col-md-2 col-sm-6 col-xs-12';
            } elseif ( round($width) == '25' || (round($width) < 25 && round($width) > 16) ) {
                $coloumn_class = 'col-lg-3 col-md-3 col-sm-6 col-xs-12';
            } elseif ( round($width) == '33' || (round($width) < 33 && round($width) > 25) ) {
                $coloumn_class = 'col-lg-4 col-md-4 col-sm-6 col-xs-12';
            } elseif ( round($width) == '50' || (round($width) < 50 && round($width) > 33) ) {
                $coloumn_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
            } elseif ( round($width) == '67' || (round($width) < 67 && round($width) > 50) ) {
                $coloumn_class = 'col-lg-8 col-md-12 col-sm-12 col-xs-12';
            } elseif ( round($width) == '75' || (round($width) < 75 && round($width) > 67) ) {
                $coloumn_class = 'col-md-9 col-lg-9 col-sm-12 col-xs-12';
            } elseif ( round($width) == '100' ) {
                $coloumn_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
            } else {
                $coloumn_class = '';
            }
        }
        return esc_html($coloumn_class);
    }

}

if ( ! function_exists('cs_allow_large_joins') ) {

    function cs_allow_large_joins() {
        global $wpdb;
        $wpdb->query('SET SQL_BIG_SELECTS=1');
    }

    add_action('init', 'cs_allow_large_joins');
}

/**
 * @Section element Size(s)
 *
 *
 */
if ( ! function_exists('jobcareer_element_size_data_array_index') ) {

    function jobcareer_element_size_data_array_index($size) {
        if ( $size == "" or $size == 100 ) {
            return 0;
        } else if ( $size == 75 ) {
            return 1;
        } else if ( $size == 67 ) {
            return 2;
        } else if ( $size == 50 ) {
            return 3;
        } else if ( $size == 33 ) {
            return 4;
        } else if ( $size == 25 ) {
            return 5;
        }
    }

}

//=====================================================================
// Column Size Dropdown Function Start
//=====================================================================
if ( ! function_exists('jobcareer_shortcode_element_size') ) {

    function jobcareer_shortcode_element_size($column_size = '') {
        global $cs_html_fields;
        $cs_opt_array = array(
            'name' => esc_html__('Size', 'jobhunt'),
            'desc' => '',
            'hint_text' => esc_html__('Select column width. This width will be calculated depend page width', 'jobhunt'),
            'echo' => true,
            'field_params' => array(
                'std' => $column_size,
                'cust_id' => 'column_size',
                'cust_type' => 'button',
                'classes' => 'column_size chosen-select',
                'cust_name' => 'column_size[]',
                'extra_atr' => '',
                'options' => array(
                    '1/1' => esc_html__('1 Column', 'jobhunt'),
                    '1/2' => esc_html__('2 Columns', 'jobhunt'),
                    '1/3' => esc_html__('3 Columns', 'jobhunt'),
                    '1/4' => esc_html__('4 Columns', 'jobhunt'),
                    '1/6' => esc_html__('6 Columns', 'jobhunt'),
                ),
                'return' => true,
            ),
        );

        $cs_html_fields->cs_select_field($cs_opt_array);
        ?>


        <?php
    }

}


/*
 * On Update Plugin / Theme calling web service
 */

if ( class_exists('cs_framework') ) {

    if ( ! function_exists('cs_plugin_db_structure_updater_callback') ) {

        function cs_plugin_db_structure_updater_callback() {
            $remote_api_url = REMOTE_API_URL;
            $envato_purchase_code_verification = get_option('item_purchase_code_verification');
            $selected_demo = isset($_POST['theme_demo']) ? $_POST['theme_demo'] : '';
            $envato_email = isset($_POST['envato_email']) ? $_POST['envato_email'] : '';
            $envato_purchase_code_verification['selected_demo'] = $selected_demo;
            $envato_purchase_code_verification['envato_email_address'] = $envato_email;
            update_option('item_purchase_code_verification', $envato_purchase_code_verification);
            $theme_obj = wp_get_theme();
            $demo_data = array(
                'theme_puchase_code' => $envato_purchase_code_verification['item_puchase_code'],
                'theme_name' => $theme_obj->get('Name'),
                'theme_id' => $envato_purchase_code_verification['item_id'],
                'user_email' => $envato_email,
                'theme_demo' => $selected_demo,
                'theme_version' => $theme_obj->get('Version'),
                'site_url' => site_url(),
                'supported_until' => isset($envato_purchase_code_verification['supported_until']) ? $envato_purchase_code_verification['supported_until'] : '',
                'action' => 'add_to_active_themes',
            );
            $url = $remote_api_url;
            $response = wp_remote_post($url, array( 'body' => $demo_data ));
            check_theme_is_active();
        }

        add_action('cs_plugin_db_structure_updater', 'cs_plugin_db_structure_updater_callback', 10);
    }
}


/*
 * job apply btn for 3 columns
 */

if ( ! function_exists('jobhunt_apply_job_btn_3_column_callback') ) {

    function jobhunt_apply_job_btn_3_column_callback($job_id, $class_apply) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : '';
        if ( ! empty($cs_job_apply_method) ) {
            do_action('jobhunt_applyjob_without_login', 'loggedin');
        } else {
            ?>
            <a class="active btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_id); ?>', this)" >
                <i class="icon-briefcase4"></i>
                <?php
                $apply_job = esc_html__('Apply for this job', 'jobhunt');
                $apply_job = apply_filters('job_asifbadat_apply_buttons_text_change', $apply_job);
                echo $apply_job;
                ?>
            </a>
            <?php
        }
    }

    add_action('jobhunt_apply_job_btn_3_column', 'jobhunt_apply_job_btn_3_column_callback', 10, 2);
}

/*
 * job apply btn for 2 columns
 */

if ( ! function_exists('jobhunt_apply_job_btn_2_column_callback') ) {

    function jobhunt_apply_job_btn_2_column_callback($job_id, $class_apply) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : '';
        if ( ! empty($cs_job_apply_method) ) {
            do_action('jobhunt_applyjob_without_login', 'loggedin');
        } else {
            ?>
            <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_id); ?>', this)" >
                <?php
                $apply_job = esc_html__('Apply for this job', 'jobhunt');
                $apply_job = apply_filters('job_asifbadat_apply_buttons_text_change', $apply_job);
                echo $apply_job;
                ?>
            </a>
            <?php
        }
    }

    add_action('jobhunt_apply_job_btn_2_column', 'jobhunt_apply_job_btn_2_column_callback', 10, 2);
}

/*
 * job apply btn for classic view
 */

if ( ! function_exists('jobhunt_apply_job_btn_classic_view_callback') ) {

    function jobhunt_apply_job_btn_classic_view_callback($job_id, $class_apply) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : '';
        if ( ! empty($cs_job_apply_method) ) {
            do_action('jobhunt_applyjob_without_login', 'loggedin');
        } else {
            ?>
            <a class="like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_id); ?>', this)" >
                <span><i class="icon-thumbsup"></i></span>
                <?php
                $apply_job = esc_html__('Apply for this job', 'jobhunt');
                $apply_job = apply_filters('job_asifbadat_apply_buttons_text_change', $apply_job);
                echo $apply_job;
                ?>
            </a>
            <?php
        }
    }

    add_action('jobhunt_apply_job_btn_classic_view', 'jobhunt_apply_job_btn_classic_view_callback', 10, 2);
}

/*
 * job apply btn for fancy view
 */

if ( ! function_exists('jobhunt_apply_job_btn_fancy_view_callback') ) {

    function jobhunt_apply_job_btn_fancy_view_callback($job_id, $class_apply) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : '';
        if ( ! empty($cs_job_apply_method) ) {
            do_action('jobhunt_applyjob_without_login', 'loggedin');
        } else {
            ?>
            <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_id); ?>', this)" >
                <i class="icon-briefcase4"></i><?php
                $apply_job = esc_html__('Apply for this job', 'jobhunt');
                $apply_job = apply_filters('job_asifbadat_apply_buttons_text_change', $apply_job);
                echo $apply_job;
                ?>
            </a>
            <?php
        }
    }

    add_action('jobhunt_apply_job_btn_fancy_view', 'jobhunt_apply_job_btn_fancy_view_callback', 10, 2);
}

/*
 * job apply btn for map view
 */

if ( ! function_exists('jobhunt_apply_job_btn_map_view_callback') ) {

    function jobhunt_apply_job_btn_map_view_callback($job_id, $class_apply) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : '';
        if ( ! empty($cs_job_apply_method) ) {
            do_action('jobhunt_applyjob_without_login', 'loggedin');
        } else {
            ?>

            <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_id); ?>', this)" >
                <?php
                $apply_job = esc_html__('Apply for this job', 'jobhunt');
                $apply_job = apply_filters('job_asifbadat_apply_buttons_text_change', $apply_job);
                echo $apply_job;
                ?>
            </a>
            <?php
        }
    }

    add_action('jobhunt_apply_job_btn_map_view', 'jobhunt_apply_job_btn_map_view_callback', 10, 2);
}

/*
 * Location Fields hook for frontend
 */

if ( ! function_exists('jobhunt_frontend_location_fields_callback') ) {

    function jobhunt_frontend_location_fields_callback($uid, $field_postfix, $current_user) {
        CS_FUNCTIONS()->cs_frontend_location_fields($uid, $field_postfix, $current_user);
    }

    add_action('jobhunt_frontend_location_fields', 'jobhunt_frontend_location_fields_callback', 10, 3);
}


if ( ! function_exists('jobhunt_applyjob_without_login_callback') ) {

    function jobhunt_applyjob_without_login_callback($view) {
        global $job_post, $cs_plugin_options, $cs_form_fields2;

        $cs_without_login_switch = isset($cs_plugin_options['cs_without_login_switch']) && ! empty($cs_plugin_options['cs_without_login_switch']) ? $cs_plugin_options['cs_without_login_switch'] : '';

        /* check without login apply job method */
        $cs_job_apply_method = isset($cs_plugin_options['cs_job_apply_method']) && ! empty($cs_plugin_options['cs_job_apply_method']) ? $cs_plugin_options['cs_job_apply_method'] : 'apply_cv';
        $user_ID = get_current_user_id();
        $job_post_id = get_the_ID();
        $user_fullname = '';
        $user_email = '';
        $user_phone = '';
        $user_cover_letter = '';
        $cs_candidate_cv = '';
        if ( $user_ID != '' ) {
            $user_info = get_userdata($user_ID);
            $user_fullname = $user_info->display_name;
            $user_email = $user_info->user_email;
            $user_phone = get_user_meta($user_ID, 'cs_phone_number', true);
            $user_cover_letter = get_user_meta($user_ID, 'cs_cover_letter', true);
            $cs_candidate_cv = get_user_meta($user_ID, "cs_candidate_cv", true);
        }

        $class_apply = '';
        if ( isset($_SESSION['apply_job_id']) ) {
            $class_apply = ' applyauto';
            unset($_SESSION['apply_job_id']);
        }
        if ( $view == 'loggedin' ) {
            ?>
            <a href="javascript:void(0);" class="loggedin-cv-apply btn large like applied_icon<?php echo $class_apply; ?>" id="apply-btn-<?php echo esc_html($job_post_id); ?>"  data-job-id="<?php echo absint($job_post_id) ?>" data-target="#cs-apply-job-<?php echo absint($job_post_id) ?>" data-toggle="modal" href="javascript:void(0);">
                <span><i class="icon-briefcase4"></i></span><?php
                if ( is_user_logged_in() ) {
                    esc_html_e('Apply for this job', 'jobhunt');
                } else {
                    esc_html_e('Apply Now Without Login', 'jobhunt');
                }
                ?>
            </a>
        <?php }
        ?>
        <?php if ( $view != 'loggedin' ) { ?>
            <div class="w-apply-job" id="without-login-switch" style="display:none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('apply for job', 'jobhunt') ?></h4>
                <div class="cs-profile-contact-detail cs-contact-modal" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" id="logout">
                    <?php
                    if ( $cs_job_apply_method == 'apply_cv' ) {
                        ?>
                        <form id="apply-job-<?php echo absint($job_post_id); ?>" class="apply-job" action="#" method="post" enctype="multipart/form-data" asif>
                            <?php
                            $error_class = '';
                            $error_class = 'error-msg';
                            ?>
                            <div class="apply-job-response <?php echo esc_html($error_class); ?>">
                            </div>
                            <div class="input-filed">
                                <label><?php _e('FUll Name', 'jobhunt'); ?><span class="required">*</span></label>
                                <?php
                                $cs_opt_array = array(
                                    'std' => $user_fullname,
                                    'cust_id' => 'fullname_' . $job_post_id,
                                    'cust_name' => 'fullname',
                                    'classes' => 'cs-required',
                                        // 'extra_atr' => 'onkeyup="check_number_field_validation(\''. $job_post_id .'\', this);"',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </div>
                            <div class="input-filed">
                                <label><?php _e('Email', 'jobhunt'); ?><span class="required">*</span></label>
                                <?php
                                $cs_opt_array = array(
                                    'std' => $user_email,
                                    'cust_id' => 'email_' . $job_post_id,
                                    'cust_name' => 'email',
                                    //'extra_atr' => 'onkeyup="check_number_field_validation(\''. $job_post_id .'\', this);"',
                                    'classes' => 'cs-required',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </div>
                            <div class="input-filed">
                                <label><?php _e('Phone Number', 'jobhunt'); ?><span class="required">*</span></label>
                                <?php
                                $cs_opt_array = array(
                                    'std' => $user_phone,
                                    'cust_id' => 'phone_num' . $job_post_id,
                                    'cust_name' => 'phone',
                                    'extra_atr' => 'onkeyup="check_number_field_validation(\'' . $job_post_id . '\', this);"',
                                    'classes' => 'cs-required',
                                );
                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </div>
                            <div class="input-filed">
                                <label><?php _e('Cover Letter', 'jobhunt'); ?><span class="required">*</span></label>
                                <?php
                                $cs_opt_array = array(
                                    'std' => strip_tags($user_cover_letter),
                                    'cust_id' => 'cover_letter_' . $job_post_id,
                                    'cust_name' => 'cover_letter',
                                    'extra_atr' => 'rows="5" placeholder="' . __('Write here...', 'jobhunt') . '" onkeyup="check_character_length(\'' . $job_post_id . '\');"',
                                    'classes' => 'cs-required',
                                );
                                $cs_form_fields2->cs_form_textarea_render($cs_opt_array);
                                ?>
                                <div class="length cover-letter-length">
                                    <div class="characters-holder">
                                        <span><?php echo esc_html__('Min characters', 'jobhunt'); ?>: 10</span>
                                        <span><?php echo esc_html__('Max characters', 'jobhunt'); ?>: 500</span>
                                    </div>
                                    <div class="remaining-characters" style="display:none;"><span>500</span> <?php echo esc_html__('characters remaining', 'jobhunt'); ?></div>
                                </div>
                            </div>
                            <div class="input-filed">
                                <div class="cs-img-detail resume-upload">
                                    <div class="inner-title">
                                        <label><?php _e('Your CV', 'jobhunt'); ?><span class="required">*</span></label>

                                    </div>
                                    <div class="upload-btn-div">
                                        <div class="dragareamain" style="padding-bottom:0px;">
                                            <script type="text/ecmascript">
                                                jQuery(document).ready(function(){
                                                jQuery('.cs-uploadimg').change( function(e) {
                                                var img = URL.createObjectURL(e.target.files[0]);
                                                //var img = URL.createObjectURL(e.target.files[0]['type']);
                                                jQuery('#cs_candidate_cv').attr('value', img);
                                                });
                                                });
                                            </script>

                                            <div class="fileUpload uplaod-btn btn csborder-color cs-color">
                                                <span class="cs-color"><?php esc_html_e('Browse', 'jobhunt'); ?></span>
                                                <label class="browse-icon">
                                                    <?php
                                                    $cs_opt_array = array(
                                                        'std' => esc_html__('Browse', 'jobhunt'),
                                                        'cust_id' => 'media_upload',
                                                        'cust_name' => 'media_upload',
                                                        'cust_type' => 'file',
                                                        'force_std' => true,
                                                        'extra_atr' => ' onchange="checkName(this, \'cs_candidate_cv\', \'button_action\')"',
                                                        'classes' => 'upload cs-uploadimg cs-color csborder-color cs-required',
                                                    );
                                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                    ?>
                                                </label>
                                            </div>

                                            <div id="selecteduser-cv">
                                                <?php
                                                //if (isset($cs_candidate_cv) and $cs_candidate_cv <> '' && (!isset($cs_candidate_cv['error']))) {
                                                $cs_opt_array = array(
                                                    'std' => $cs_candidate_cv,
                                                    'cust_id' => 'cs_candidate_cv',
                                                    'cust_name' => 'cs_candidate_cv',
                                                    'cust_type' => 'hidden',
                                                );
                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                ?>
                                                <div class="alert alert-dismissible user-resume" id="cs_candidate_cv_box">
                                                    <div>
                                                        <?php
                                                        if ( isset($cs_candidate_cv) && $cs_candidate_cv != '' ) {
                                                            if ( cs_check_coverletter_exist($cs_candidate_cv) ) {
                                                                $uploads = wp_upload_dir();
                                                                echo '<a target="_blank" href="' . esc_url($cs_candidate_cv) . '">';
                                                                // uploaded file
                                                                $parts = preg_split('~_(?=[^_]*$)~', basename($cs_candidate_cv));
                                                                echo esc_html($parts[0]); // outputs "one_two_three"
                                                                echo '</a>';
                                                                ?>
                                                                <div class="gal-edit-opts close"><a href="javascript:cs_del_cover_letter('cs_candidate_cv')" class="delete">
                                                                        <span aria-hidden="true">×</span></a>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                esc_html_e("File not Available", "jobhunt");
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php //}        ?>				
                                            </div>
                                        </div>
                                        <span class="cs-status-msg-cv-upload"><?php esc_html_e('Suitable files are .doc,docx,rft,pdf & .pdf', 'jobhunt'); ?></span>              
                                    </div>
                                </div>
                            </div>

                            <div class="submit-btn input-button-loader" id="apply_job_<?php echo $job_post_id; ?>">
                                <?php if ( is_user_logged_in() ) { ?>
                                    <a class="btn large like applied_icon<?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post_id); ?>', this)" >
                                        <span>
                                            <i class="icon-briefcase4"></i>
                                        </span><?php
                                        $apply_job = esc_html__('Apply Now', 'jobhunt');
                                        echo $apply_job;
                                        ?>
                                    </a>
                                    <?php
                                    //$apply_job_action = 'onclick="cs_addjobs_left_to_applied(\'' . esc_url(admin_url('admin-ajax.php')) . '\', ' . $job_post_id . ')"';
                                } else {
                                    ?>
                                    <a class="btn-without-login<?php echo $class_apply; ?>" onclick="jobhunt_add_proposal('<?php echo (admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post_id); ?>', this)" >
                                        <span>
                                            <i class="icon-briefcase4"></i>
                                        </span><?php
                                        $apply_job = esc_html__('Apply Now', 'jobhunt');
                                        echo $apply_job;
                                        ?>
                                    </a>
                                    <?php
                                    $cs_opt_array = array(
                                        'std' => $job_post_id,
                                        'cust_id' => 'post_id_' . $job_post_id,
                                        'cust_name' => 'post_id',
                                        'cust_type' => 'hidden',
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    $cs_opt_array = array(
                                        'std' => 'cs_add_applied_job_withoutlogin_to_usermeta',
                                        'cust_id' => 'action' . $job_post_id,
                                        'cust_name' => 'action',
                                        'cust_type' => 'hidden',
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                }
                                ?>
                                <a class="cs-bgcolor cs-wlogin-switch">Login Here</a>
                                <div class="apply-loader"></div>
                            </div>
                        </form>
                    <?php } ?>
                    <?php
                    if ( $cs_job_apply_method == 'apply_external_link' ) {

                        $extenal_message = esc_html__('Oops ! There is no external link to apply for this job', 'jobhunt');
                        $cs_job_external_url = get_post_meta($job_post_id, "cs_external_url_id", true);
                        $url_flag = false;
                        if ( isset($cs_job_external_url) && ! empty($cs_job_external_url) ) {
                            $url_flag = true;
                            $extenal_message = esc_html__('Click "Link" button to apply Job via External Url', 'jobhunt');
                        }
                        ?>
                        <div class="input-filed external-apply">
                            <label><?php echo ($extenal_message); ?></label>
                            <?php
                            if ( $url_flag ) {
                                ?>
                                <a class="external_link" target="_blank" href="<?php echo esc_url($cs_job_external_url); ?>"><?php esc_html_e('Link', 'jobhunt'); ?></a>
                                <?php
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>



            </div>
        <?php } ?>
        <?php if ( $view == 'loggedin' ) { ?>
            <div class="modal fade w-apply-job" id="cs-apply-job-<?php echo absint($job_post_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><?php esc_html_e('apply for job', 'jobhunt') ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="cs-profile-contact-detail cs-contact-modal" data-adminurl="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                                <?php
                                if ( $cs_job_apply_method == 'apply_cv' ) {
                                    ?>
                                    <form id="apply-job-<?php echo absint($job_post_id); ?>" class="apply-job" action="#" method="post" enctype="multipart/form-data">
                                        <?php
                                        $error_class = '';
                                        $error_class = 'error-msg';
                                        ?>
                                        <div class="apply-job-response <?php echo esc_html($error_class); ?>">


                                        </div>
                                        <div class="input-filed">
                                            <label><?php _e('FUll Name', 'jobhunt'); ?><span class="required">*</span></label>
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => $user_fullname,
                                                'cust_id' => 'fullname_' . $job_post_id,
                                                'cust_name' => 'fullname',
                                                'classes' => 'cs-required',
                                                    // 'extra_atr' => 'onkeyup="check_number_field_validation(\''. $job_post_id .'\', this);"',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                        <div class="input-filed">
                                            <label><?php _e('Email', 'jobhunt'); ?><span class="required">*</span></label>
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => $user_email,
                                                'cust_id' => 'email_' . $job_post_id,
                                                'cust_name' => 'email',
                                                //'extra_atr' => 'onkeyup="check_number_field_validation(\''. $job_post_id .'\', this);"',
                                                'classes' => 'cs-required',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                        <div class="input-filed">
                                            <label><?php _e('Phone Number', 'jobhunt'); ?><span class="required">*</span></label>
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => $user_phone,
                                                'cust_id' => 'phone_num' . $job_post_id,
                                                'cust_name' => 'phone',
                                                'extra_atr' => 'onkeyup="check_number_field_validation(\'' . $job_post_id . '\', this);"',
                                                'classes' => 'cs-required',
                                            );
                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            ?>
                                        </div>
                                        <div class="input-filed">
                                            <label><?php _e('Cover Letter', 'jobhunt'); ?><span class="required">*</span></label>
                                            <?php
                                            $cs_opt_array = array(
                                                'std' => strip_tags($user_cover_letter),
                                                'cust_id' => 'cover_letter_' . $job_post_id,
                                                'cust_name' => 'cover_letter',
                                                'extra_atr' => 'rows="5" placeholder="' . __('Write here...', 'jobhunt') . '" onkeyup="check_character_length(\'' . $job_post_id . '\');"',
                                                'classes' => 'cs-required',
                                            );
                                            $cs_form_fields2->cs_form_textarea_render($cs_opt_array);
                                            ?>
                                            <div class="length cover-letter-length">
                                                <div class="characters-holder">
                                                    <span><?php echo esc_html__('Min characters', 'jobhunt'); ?>: 10</span>
                                                    <span><?php echo esc_html__('Max characters', 'jobhunt'); ?>: 500</span>
                                                </div>
                                                <div class="remaining-characters" style="display:none;"><span>500</span> <?php echo esc_html__('characters remaining', 'jobhunt'); ?></div>
                                            </div>
                                        </div>
                                        <div class="input-filed">
                                            <div class="cs-img-detail resume-upload">
                                                <div class="inner-title">
                                                    <label><?php _e('Your CV', 'jobhunt'); ?><span class="required">*</span></label>
                                                </div>
                                                <div class="upload-btn-div">
                                                    <div class="dragareamain" style="padding-bottom:0px;">
                                                        <script type="text/ecmascript">
                                                            jQuery(document).ready(function(){
                                                            jQuery('.cs-uploadimg').change( function(e) {
                                                            var img = URL.createObjectURL(e.target.files[0]);
                                                            //var img = URL.createObjectURL(e.target.files[0]['type']);
                                                            jQuery('#cs_candidate_cv').attr('value', img);
                                                            });
                                                            });
                                                        </script>

                                                        <div class="fileUpload uplaod-btn btn csborder-color cs-color">
                                                            <span class="cs-color"><?php esc_html_e('Browse', 'jobhunt'); ?></span>
                                                            <label class="browse-icon">
                                                                <?php
                                                                $cs_opt_array = array(
                                                                    'std' => esc_html__('Browse', 'jobhunt'),
                                                                    'cust_id' => 'media_upload',
                                                                    'cust_name' => 'media_upload',
                                                                    'cust_type' => 'file',
                                                                    'force_std' => true,
                                                                    'extra_atr' => ' onchange="checkName(this, \'cs_candidate_cv\', \'button_action\')"',
                                                                    'classes' => 'upload cs-uploadimg cs-color csborder-color',
                                                                );
                                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                                ?>
                                                            </label>
                                                        </div>

                                                        <div id="selecteduser-cv">
                                                            <?php
                                                            //if (isset($cs_candidate_cv) and $cs_candidate_cv <> '' && (!isset($cs_candidate_cv['error']))) {
                                                            $cs_opt_array = array(
                                                                'std' => $cs_candidate_cv,
                                                                'cust_id' => 'cs_candidate_cv',
                                                                'cust_name' => 'cs_candidate_cv',
                                                                'cust_type' => 'hidden',
                                                            );
                                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                            ?>
                                                            <div class="alert alert-dismissible user-resume" id="cs_candidate_cv_box">
                                                                <div>
                                                                    <?php
                                                                    if ( isset($cs_candidate_cv) && $cs_candidate_cv != '' ) {
                                                                        if ( cs_check_coverletter_exist($cs_candidate_cv) ) {
                                                                            $uploads = wp_upload_dir();
                                                                            echo '<a target="_blank" href="' . esc_url($cs_candidate_cv) . '">';
                                                                            // uploaded file
                                                                            $parts = preg_split('~_(?=[^_]*$)~', basename($cs_candidate_cv));
                                                                            echo esc_html($parts[0]); // outputs "one_two_three"
                                                                            echo '</a>';
                                                                            ?>
                                                                            <div class="gal-edit-opts close"><a href="javascript:cs_del_cover_letter('cs_candidate_cv')" class="delete">
                                                                                    <span aria-hidden="true">×</span></a>
                                                                            </div>
                                                                            <?php
                                                                        } else {
                                                                            esc_html_e("File not Available", "jobhunt");
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <?php //}        ?>				
                                                        </div>
                                                    </div>
                                                    <span class="cs-status-msg-cv-upload"><?php esc_html_e('Suitable files are .doc,docx,rft,pdf & .pdf', 'jobhunt'); ?></span>              
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submit-btn input-button-loader" id="apply_job_<?php echo $job_post_id; ?>">
                                            <?php if ( is_user_logged_in() ) { ?>
                                                <a class="btn large like applied_icon<?php echo $class_apply; ?>" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post_id); ?>', this)" >
                                                    <span>
                                                        <i class="icon-briefcase4"></i>
                                                    </span><?php
                                                    $apply_job = esc_html__('Apply Now', 'jobhunt');
                                                    echo $apply_job;
                                                    ?>
                                                </a>
                                                <?php
                                            } else {
                                                ?>
                                                <a class="btn large like applied_icon <?php echo $class_apply; ?>" onclick="jobhunt_add_proposal('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($job_post_id); ?>', this)" >
                                                    <span>
                                                        <i class="icon-briefcase4"></i>
                                                    </span><?php
                                                    $apply_job = esc_html__('Apply Now', 'jobhunt');
                                                    echo $apply_job;
                                                    ?>
                                                </a>
                                                <?php
                                                $cs_opt_array = array(
                                                    'std' => $job_post_id,
                                                    'cust_id' => 'post_id_' . $job_post_id,
                                                    'cust_name' => 'post_id',
                                                    'cust_type' => 'hidden',
                                                );
                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                $cs_opt_array = array(
                                                    'std' => 'cs_add_applied_job_withoutlogin_to_usermeta',
                                                    'cust_id' => 'action' . $job_post_id,
                                                    'cust_name' => 'action',
                                                    'cust_type' => 'hidden',
                                                );
                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            }
                                            ?>
                                            <div class="apply-loader"></div>
                                        </div>
                                    </form>
                                <?php } ?>
                                <?php
                                if ( $cs_job_apply_method == 'apply_external_link' ) {

                                    $cs_job_external_url = get_post_meta($job_post_id, "cs_external_url_id", true);
                                    ?>
                                    <div class="input-filed">
                                        <h5><?php esc_html_e('Apply Via External Link', 'jobhunt'); ?></h5>
                                        <label><?php _e('Click The bellow Link to Apply', 'jobhunt'); ?></label>
                                        <a class="external_link" target="_blank" href="<?php echo esc_url($cs_job_external_url); ?>">
                                            link
                                        </a>


                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php
    }

    add_action('jobhunt_applyjob_without_login', 'jobhunt_applyjob_without_login_callback', 10, 1);
}


if ( ! function_exists('jobhunt_signup_direct_login_callback') ) {

    function jobhunt_signup_direct_login_callback($json = array(), $login_data = array()) {
        global $cs_plugin_options;


        $cs_password_option = isset($cs_plugin_options['cs_user_password_switchs']) ? $cs_plugin_options['cs_user_password_switchs'] : '';
        if ( $cs_password_option != 'on' ) {
            return $json;
        }


        $cs_danger_html = '<div class="alert alert-danger"><button class="close" type="button" data-dismiss="alert" aria-hidden="true">&times;</button><p><i class="icon-warning4"></i>';
        $cs_success_html = '<div class="alert alert-success"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">&times;</button><p><i class="icon-checkmark6"></i>';
        $cs_msg_html = '</p></div>';


        $status = wp_signon($login_data, is_ssl());
        if ( is_wp_error($status) ) {
            echo json_encode(array( 'loggedin' => false, 'message' => $cs_danger_html . esc_html__('Wrong username or password.', 'jobhunt') . $cs_msg_html ));
        } else {
            $user_roles = isset($status->roles) ? $status->roles : '';
            $uid = $status->ID;
            $cs_user_name = $_POST['user_login'];
            $cs_login_user = get_user_by('login', $cs_user_name);
            $cs_page_id = '';
            $default_url = '';
            if ( ($user_roles != '' && in_array("cs_employer", $user_roles) ) ) {
                $cs_page_id = isset($cs_plugin_options['cs_emp_dashboard']) ? $cs_plugin_options['cs_emp_dashboard'] : $default_url;
            } elseif ( ($user_roles != '' && in_array("cs_candidate", $user_roles) ) ) {
                $cs_page_id = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : $default_url;
            }
            // update user last activity
            update_user_meta($uid, 'cs_user_last_activity_date', strtotime(current_time('d-m-Y H:i:s')));
            $cs_redirect_url = '';
            if ( $cs_page_id != '' ) {
                $cs_redirect_url = get_permalink($cs_page_id);
            } else {
                $cs_redirect_url = $default_url;  // home URL if page not set
            }
            $json = array(
                'redirecturl' => $cs_redirect_url,
                'type' => 'success',
                'loggedin' => true,
                'message' => $cs_success_html . esc_html__('Login Successfully...', 'jobhunt') . $cs_msg_html );
        }

        return $json;
    }

    add_filter('jobhunt_signup_direct_login', 'jobhunt_signup_direct_login_callback', 10, 2);
}


