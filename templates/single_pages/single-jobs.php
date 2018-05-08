<?php
/**
 * The template for Job Detail 
 */
global $post, $current_user, $cs_plugin_options, $cs_form_fields2;
if (is_single()) {
    cs_set_post_views($post->ID);
}
get_header();

?>
<div class="main-section">
    <div class="content-area" id="primary">
        <main class="site-main" id="main">
            <article class="post-1 post type-post status-publish format-standard hentry category-uncategorized">
                <!-- alert for complete theme -->
                <div class="cs_alerts" ></div>
                <?php
                $cs_page_view = get_post_meta($post->ID, 'cs_job_style', true);
                if ($cs_page_view == '') {
                    if (isset($cs_plugin_options['cs_job_detail_style']) && $cs_plugin_options['cs_job_detail_style'] != '') {
                        $cs_page_view = $cs_plugin_options['cs_job_detail_style'];
                    } else {
                        $cs_page_view = '3_columns';
                    }
                }
                do_action ( 'jobhunt_before_single_job_view_render' );
                if ($cs_page_view == 'map_view') {
                    require_once 'job_views/map-view.php';
                } else if ($cs_page_view == 'fancy') {
                    require_once 'job_views/fancy.php';
                } else if ($cs_page_view == '2_columns') {
                    require_once 'job_views/2-columns.php';
                } else if ($cs_page_view == 'classic') {
                    require_once 'job_views/classic.php';
                } else if ($cs_page_view == '3_columns') {
                    require_once 'job_views/3-columns.php';
                }
                do_action ( 'liamdemoncuit_job_detail_view_call', $cs_page_view);
                ?>
            </article>
        </main>
    </div>
</div>
<?php get_footer();