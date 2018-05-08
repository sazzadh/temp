<?php
/**
 * Jobs list view
 *
 */
global $wpdb, $cs_plugin_options;
$main_col = '';
if ($a['cs_job_searchbox'] == 'yes') {
    $main_col = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
}
$cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
$loops = new WP_Query($args);
$loop = $loops;
$loop = apply_filters('jobhunt_featured_jobs_frontend', $loop, $args);
$found_posts = $loops->found_posts;
$found_posts = apply_filters('jobhunt_feature_count_post', $found_posts);
$count_post = $found_posts;
?>
<div class="hiring-holder <?php echo cs_allow_special_char($main_col); ?>">
    <?php
    include plugin_dir_path(__FILE__) . '../jobs-search-keywords.php';

    if ((isset($a['cs_job_title']) && $a['cs_job_title'] != '') || (isset($a['cs_job_top_search']) && $a['cs_job_top_search'] != "None" && $a['cs_job_top_search'] <> '')) {
        echo ' <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <div class="row">';
        // section title
        if (isset($a['cs_job_title']) && $a['cs_job_title'] != '') {
            echo '<div class="cs-element-title"><h2>' . $a['cs_job_title'] . '</h2>';
            if (isset($a['cs_job_sub_title']) && $a['cs_job_sub_title'] != '') {
                echo '<span>' . $a['cs_job_sub_title'] . '</span>';
            }
            echo '</div>';
        }
        // sub title with total rec 
        if (isset($a['cs_job_top_search']) && $a['cs_job_top_search'] != "None" && $a['cs_job_top_search'] <> '') {

            if (isset($a['cs_job_top_search']) and $a['cs_job_top_search'] == "total_records") {
                echo '<h2>';
                ?><span class="result-count"><?php if (isset($count_post) && $count_post != '') echo esc_html($count_post) . " "; ?></span><?php
                if (isset($specialisms) && is_array($specialisms)) {
                    echo get_specialism_headings($specialisms);
                } else {

                    echo esc_html__("Jobs & Vacancies", "jobhunt");
                }
                echo "</h2>";
            } elseif (isset($a['cs_job_top_search']) and $a['cs_job_top_search'] == "Filters") {
                include plugin_dir_path(__FILE__) . '../jobs-sort-filters.php';
            }
        }
        echo '</div></div>';
    }
    ?>
    <ul class="jobs-listing joblist-simple">
        <?php
        // getting if record not found
        if ($loop->have_posts()) {
            $flag = 1;
            while ($loop->have_posts()) : $loop->the_post();
                global $post;
                $cs_job_id = $post; 
                $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                $cs_jobs_thumb_url = '';
                // get employer images at run time
                $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                $cs_user_data = get_userdata($cs_job_employer);
                $cs_comp_name = isset($cs_user_data->display_name) ? $cs_user_data->display_name : '';
                $employer_img = get_the_author_meta('user_img', $cs_job_employer);

                if ($employer_img != '') {
                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                }
                $cs_jobs_thumb_url = apply_filters('digitalmarketing_job_image',$cs_jobs_thumb_url,$cs_job_id);
                
                if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                }
                $cs_job_featured = get_post_meta($cs_job_id, 'cs_job_featured', true);
                // get all job types
                $all_specialisms = get_the_terms($cs_job_id, 'specialisms');

                $specialisms_values = '';
                $specialisms_class = '';
                $specialism_flag = 1;
                if ($all_specialisms != '') {
                    foreach ($all_specialisms as $specialismsitem) {
                        $specialisms_values .= $specialismsitem->name;
                        $specialisms_class .= $specialismsitem->slug;
                        if ($specialism_flag != count($all_specialisms)) {
                            $specialisms_values .= ", ";
                            $specialisms_class .= " ";
                        }
                        $specialism_flag ++;
                    }
                }

                $all_job_type = get_the_terms($cs_job_id, 'job_type');
                $job_type_values = '';
                $job_type_class = '';
                $job_type_flag = 1;
                if ($all_job_type != '') {
                    foreach ($all_job_type as $job_type) {

                        $t_id_main = $job_type->term_id;
                        $job_type_color_arr = get_option("job_type_color_$t_id_main");
                        $job_type_color = '';
                        if (isset($job_type_color_arr['text'])) {
                            $job_type_color = $job_type_color_arr['text'];
                        }
                        $cs_link = ' href="javascript:void(0);"';
                        if ($cs_search_result_page != '') {
                            $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                        }
                        $job_type_values .= '<a ' . force_balance_tags($cs_link) . ' class="jobtype-btn" style="border:solid 1px ' . $job_type_color . ';color:' . $job_type_color . ';">' . $job_type->name . '</a>';

                        if ($job_type_flag != count($all_specialisms)) {
                            $job_type_values .= " ";
                            $job_type_class .= " ";
                        }
                        $job_type_flag ++;
                    }
                }
                $cs_jobs_address = apply_filters( 'jobhunt_job_address_frontend', $cs_jobs_address, $cs_job_id);
                ?>
                <li class="col-lg-12 col-md-12 col-xs-12 col-xs-12">
                    <div class="jobs-content">
                        <?php if ($cs_jobs_thumb_url <> '') { ?>
                            <div class="cs-media">
                                <figure><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"><img alt="images" src="<?php echo esc_url($cs_jobs_thumb_url); ?>"></a></figure>
				 <?php } if ( isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on' ) { ?>
                                            <span title="" data-placement="top" data-toggle="tooltip" class="feature-post" data-original-title="<?php esc_html_e('Featured', 'jobhunt') ?>"><i class=" icon-star-o"></i></span><?php } ?>
                            </div>

                        <div class="cs-text">
                            <div class="cs-post-title"><h3><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"> <?php echo esc_html(get_the_title($cs_job_id)) ?></a></h3></div>
                            <?php
                            $active_plugin = false;
                            $active_plugin = apply_filters('jobhunt_email_template_depedency', $active_plugin);
                            if ( ! $active_plugin ) {
                                if ( isset($cs_comp_name) && $cs_comp_name != '' ) { ?><span class="author-name"> @ <?php echo force_balance_tags($cs_comp_name); ?></span><?php }
                            }else{
                                $job_company_name = apply_filters('jobhunt_feature_company_name', '', $cs_job_id,'detail');
                                echo force_balance_tags($job_company_name);
                            }
                            ?>
                        <?php echo force_balance_tags($job_type_values) ?>
                            <?php if ($cs_jobs_address <> '') { ?>
                                <div class="post-options">
                                    <span class="cs-location"><i class="icon-location6"></i><?php echo esc_html($cs_jobs_address); ?> </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </li>


                <?php
                $flag ++;
            endwhile;
            wp_reset_postdata();
        } else {
            echo '<li class="ln-no-match">';
            echo '<div class="massage-notfound">
            <div class="massage-title">
             <h6><i class="icon-warning4"></i><strong> ' . esc_html__('Sorry !', 'jobhunt') . '</strong>&nbsp; ' . esc_html__("There are no listings matching your search.", 'jobhunt') . ' </h6>
            </div>
             <ul>
                <li>' . esc_html__("Please re-check the spelling of your keyword", 'jobhunt') . ' </li>
                <li>' . esc_html__("Try broadening your search by using general terms", 'jobhunt') . '</li>
                <li>' . esc_html__("Try adjusting the filters applied by you", 'jobhunt') . '</li>
             </ul>
          </div>';
            echo '</li>';
        }
        ?> 
    </ul>

    <?php
    //==Pagination Start
    if (($found_posts > $cs_blog_num_post && $cs_blog_num_post > 0) && $a['cs_job_show_pagination'] == "pagination") {
        echo '<nav>';
        echo cs_pagination($found_posts, $cs_blog_num_post, $qrystr, 'Show Pagination', 'page_job');
        echo '</nav>';
    }//==Pagination End 
    ?>
</div>