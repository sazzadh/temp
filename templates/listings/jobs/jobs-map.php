<?php
if ($cs_job_map == 'yes') {
    // getting job with page number
    $cs_jobs = $cs_jobs_array = array();
    if ($loop_count->have_posts()) {
        $flag = 1;
        while ($loop_count->have_posts()) : $loop_count->the_post();
            global $post;
            $cs_job_id = $post;
            $cs_can_latitude = get_post_meta($cs_job_id, "cs_post_loc_latitude", true);
            $cs_can_longitude = get_post_meta($cs_job_id, "cs_post_loc_longitude", true);
            $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true);
            $employer_img = get_the_author_meta('user_img', $cs_job_employer);
            $cs_cand_thumb_url = '';
            if ($employer_img != '') {
                $cs_cand_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
            }
            if (!cs_image_exist($cs_cand_thumb_url) || $cs_cand_thumb_url == "") {
                $cs_cand_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
            }
            $recent_exp_company = '';
            $recent_exp_title = '';
            $cs_post_loc_city = get_post_meta($cs_job_id, 'cs_post_loc_city', true);
            $cs_post_loc_country = get_post_meta($cs_job_id, 'cs_post_loc_country', true);
            $cs_jobs[] = array(
                'post_id' => $cs_job_id,
                'post_title' => get_the_title($cs_job_id),
                'permalink' => esc_url(get_permalink($cs_job_id)),
                'latitude' => $cs_can_latitude,
                'longitude' => $cs_can_longitude,
                'image_url' => $cs_cand_thumb_url,
                'position' => $recent_exp_title,
                'company' => $recent_exp_company,
                'city' => $cs_post_loc_city,
                'country' => $cs_post_loc_country,
            );
        endwhile;
        wp_reset_postdata();
    }

    $cs_jobs_array['posts'] = $cs_jobs;
    $cs_json_array = json_encode($cs_jobs_array);

    $cs_latitude = $cs_job_map_lat;
    $cs_longitude = $cs_job_map_long;
    $cs_map_zoom = $cs_job_map_zoom;
    if ($cs_job_map_zoom != '' && $cs_job_map_zoom != '' && $cs_job_map_zoom != '') {
        $rand_counter = rand(11342345, 96754534);
        wp_jobhunt::cs_googlemapcluster_scripts();

        $cs_map_cluster_icon = isset($cs_plugin_options['cs_cs_map_cluster_icon']) ? $cs_plugin_options['cs_cs_map_cluster_icon'] : wp_jobhunt::plugin_url() . 'assets/images/culster-icon.png';
        $cs_map_marker_icon = isset($cs_plugin_options['cs_cs_map_marker_icon']) ? $cs_plugin_options['cs_cs_map_marker_icon'] : wp_jobhunt::plugin_url() . 'assets/images/map-marker.png';

        $cs_map_cluster_color = isset($cs_plugin_options['cs_map_cluster_color']) ? $cs_plugin_options['cs_map_cluster_color'] : '#000000';

        $cs_map_auto_zoom = isset($cs_plugin_options['cs_map_auto_zoom']) ? $cs_plugin_options['cs_map_auto_zoom'] : '';

        $cs_map_lock = isset($cs_plugin_options['cs_map_lock']) ? $cs_plugin_options['cs_map_lock'] : '';
        ?>
        </div></div></div></div></div></div></div>
        <div id="cs-map-candidate-<?php echo esc_attr($rand_counter); ?>" class="cs-map-candidate" style="margin-top: -284px;">
            <?php
            $cs_map_lock_icon = 'icon-unlock';
            if ($cs_map_lock == 'on') {
                $cs_map_lock_icon = 'icon-lock3';
            }
            ?>
            <span class="gmaplock" id="gmaplock<?php echo esc_attr($rand_counter); ?>" style="cursor: pointer;"><i class="<?php echo cs_allow_special_char($cs_map_lock_icon); ?>"></i></span>
            <div id="cs_map_<?php echo absint($rand_counter) ?>" style="width: 100%; height: <?php echo absint($cs_job_map_height) ?>px;"></div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var dataobj = jQuery.parseJSON('<?php echo addslashes($cs_json_array) ?>');
                //console.log(dataobj);
                cs_googlecluster_map1('<?php echo esc_js($rand_counter) ?>', '<?php echo esc_js($cs_latitude); ?>', '<?php echo esc_js($cs_longitude); ?>', '<?php echo esc_url($cs_map_cluster_icon) ?>', '<?php echo esc_url($cs_map_marker_icon) ?>', dataobj, <?php echo absint($cs_map_zoom); ?>, '<?php echo esc_js($cs_map_cluster_color); ?>', '<?php echo esc_js($cs_map_auto_zoom); ?>', '<?php echo esc_js($cs_map_lock); ?>', '<?php echo esc_js($cs_job_map_style); ?>');
            });
        </script>
        <div class="main-section">
            <!-- Page Section -->
            <div class="page-section cs-page-sec-<?php echo esc_js($rand_counter) ?> parallex-bg" data-type="background">
                <!-- Container Start -->
                <div class="container "> 
                    <div class="row">
                        <div class="section-fullwidth col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <?php
                                }
                            }