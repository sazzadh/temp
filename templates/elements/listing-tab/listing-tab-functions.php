<?php
/*
 * @Shortcode Name : Job Post
 * @retrun
 *
 */
/*
 *
 * Start Function  shortcode of job post
 *
 */
if ( ! function_exists('cs_listing_tab_shortcode') ) {

    function cs_listing_tab_shortcode($atts) {
        global $post, $current_user;
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
        extract(shortcode_atts($defaults, $atts));



        $job_active = '';
        $cand_class = '';
        $emp_class = '';
        if ( isset($listing_tab_job_tab_switch) && $listing_tab_job_tab_switch == 'yes' ) {
            $job_active = ' active';
            $cand_class = '';
            $emp_class = '';
        } elseif ( isset($listing_tab_candidate_tab_switch) && $listing_tab_candidate_tab_switch == 'yes' ) {
            $cand_class = ' active';
            $job_active = '';
            $emp_class = '';
        } elseif ( isset($listing_tab_employer_tab_switch) && $listing_tab_employer_tab_switch == 'yes' ) {
            $emp_class = ' active';
            $job_active = '';
            $cand_class = '';
        }

        ob_start();
        ?>

        <?php
        $element_title_html = '';
        if ( (isset($listing_tab_element_title) && ! empty($listing_tab_element_title)) || (isset($listing_tab_element_subtitle) && ! empty($listing_tab_element_subtitle)) ) {
            $element_title_html .= '<div class="cs-element-title">';
            if ( isset($listing_tab_element_title) && ! empty($listing_tab_element_title) ) {
                $element_title_html .= '<h2>' . $listing_tab_element_title . '</h2>';
            }
            if ( isset($listing_tab_element_subtitle) && ! empty($listing_tab_element_subtitle) ) {
                $element_title_html .= '<p>' . $listing_tab_element_title . '</p>';
            }
            $element_title_html .= '</div>';
        }

        $list_col_class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
        if ( isset($listing_tab_sidebar_select) && ! empty($listing_tab_sidebar_select) && $listing_tab_sidebar_switch == 'yes' ) {
            $list_col_class = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
        }
        ?>
        <?php
        echo force_balance_tags($element_title_html);
        if ( (isset($listing_tab_job_tab_switch) && $listing_tab_job_tab_switch == 'yes') || (isset($listing_tab_candidate_tab_switch) && $listing_tab_candidate_tab_switch == 'yes') || (isset($listing_tab_employer_tab_switch) && $listing_tab_employer_tab_switch == 'yes' ) ) {
            ?>
            <div class="tab-holder">
                <div class="jobs-tab-list">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php
                        if ( isset($listing_tab_job_tab_switch) && $listing_tab_job_tab_switch == 'yes' ) {
                            ?>
                            <li class="nav-item<?php echo ($job_active); ?>">
                                <a class="nav-link" id="jobs-tab" data-toggle="tab" href="#jobs" role="tab" aria-controls="jobs" aria-selected="true">
                                    <?php
                                    esc_html_e('Lastest Jobs', 'jobhunt');
                                    ?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                        if ( isset($listing_tab_candidate_tab_switch) && $listing_tab_candidate_tab_switch == 'yes' ) {
                            ?>
                            <li class="nav-item<?php echo ($cand_class); ?>">
                                <a class="nav-link" id="candidate-tab" data-toggle="tab" href="#candidate" role="tab" aria-controls="candidate" aria-selected="false"><?php
                                    esc_html_e('Lastest Resumes', 'jobhunt');
                                    ?>
                                </a>
                            </li>
                        <?php } ?>
                        <?php
                        if ( isset($listing_tab_employer_tab_switch) && $listing_tab_employer_tab_switch == 'yes' ) {
                            ?>
                            <li class="nav-item<?php echo ($emp_class); ?>">
                                <a class="nav-link" id="employer-tab" data-toggle="tab" href="#employer" role="tab" aria-controls="employer" aria-selected="false"><?php
                                    esc_html_e('Lastest Companies', 'jobhunt');
                                    ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <?php
                        if ( isset($listing_tab_job_tab_switch) && $listing_tab_job_tab_switch == 'yes' ) {
                            ?>
                            <div class="tab-pane fade<?php echo ($job_active); ?>" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
                                <div class="list-tab-content">
                                    <div class="row">
                                        <div class="<?php echo $list_col_class; ?>">
                                            <ul class="jobs-listing">
                                                <?php
                                                do_action('jobhunt_listing_tabs_element_jobs_content', $listing_tab_post_per_tab);
                                                ?>
                                            </ul>
                                        </div>
                                        <?php
                                        if ( is_active_sidebar($listing_tab_sidebar_select) && $listing_tab_sidebar_switch == 'yes' ) {
                                            echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
                                            dynamic_sidebar($listing_tab_sidebar_select);
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>


                        <?php
                        if ( isset($listing_tab_candidate_tab_switch) && $listing_tab_candidate_tab_switch == 'yes' ) {
                            ?>
                            <div class="tab-pane fade<?php echo ($cand_class); ?>" id="candidate" role="tabpanel" aria-labelledby="candidate-tab">
                                <div class="list-tab-content">
                                    <div class="row">
                                        <div class="<?php echo $list_col_class; ?>">
                                            <?php
                                            $login_user_is_employer_flag = 0;
                                            $login_user_is_candidate_flag = 0;
                                            $cs_emp_funs = new cs_employer_functions();
                                            if ( is_user_logged_in() ) {
                                                $user_role = cs_get_loginuser_role();
                                                if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_employer' ) {
                                                    $login_user_is_employer_flag = 1;
                                                } else if ( isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate' ) {
                                                    $login_user_is_candidate_flag = 1;
                                                }
                                            }

                                            $default_currency_sign = '';
                                            if ( isset($cs_plugin_options['cs_currency_sign']) ) {
                                                $default_currency_sign = $cs_plugin_options['cs_currency_sign'];
                                            }

                                            if ( is_user_logged_in() && ! $login_user_is_employer_flag ) {
                                                ?>
                                                <div id="cs-not-emp" style="display:none;"><?php esc_html_e('Oppsss!! You are not logged in as employer to shortlist applicant.', 'jobhunt') ?></div>
                                                <?php
                                            }
                                            ?>


                                            <ul class="cs-candidate-list cs-tab-resume">
                <?php
                do_action('jobhunt_listing_tabs_element_resumes_content', $listing_tab_post_per_tab);
                ?>
                                            </ul>
                                        </div>
                                                <?php
                                                if ( is_active_sidebar($listing_tab_sidebar_select) && $listing_tab_sidebar_switch == 'yes' ) {
                                                    echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
                                                    dynamic_sidebar($listing_tab_sidebar_select);
                                                    echo '</div>';
                                                }
                                                ?>
                                    </div>
                                </div>
                            </div>
            <?php } ?>

                        <?php
                        if ( isset($listing_tab_employer_tab_switch) && $listing_tab_employer_tab_switch == 'yes' ) {
                            ?>
                            <div class="tab-pane fade<?php echo ($emp_class); ?>" id="employer" role="tabpanel" aria-labelledby="employer-tab">
                                <div class="list-tab-content">
                                    <div class="row">
                                        <div class="<?php echo $list_col_class; ?>">
                                            <ul class="employer-listing simple cs-tab-company">
                <?php
                do_action('jobhunt_listing_tabs_element_companies_content', $listing_tab_post_per_tab);
                ?>
                                            </ul>
                                        </div>
                                                <?php
                                                if ( is_active_sidebar($listing_tab_sidebar_select) && $listing_tab_sidebar_switch == 'yes' ) {
                                                    echo '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';
                                                    dynamic_sidebar($listing_tab_sidebar_select);
                                                    echo '</div>';
                                                }
                                                ?>
                                    </div>
                                </div>	
                            </div>
            <?php } ?>


                    </div>
                </div>
            </div>
            <?php
        }
        $cs_html = ob_get_clean();
        return do_shortcode($cs_html);
    }

    add_shortcode('cs_listing_tab', 'cs_listing_tab_shortcode');
}
