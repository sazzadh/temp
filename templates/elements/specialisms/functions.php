<?php

/*
 *
 * @Shortcode Name : Job specialisms
 * @retrun
 *
 */

if ( ! function_exists( 'cs_job_specialisms_count' ) ) {

	function cs_job_specialisms_count( $id ) {
//		global $wpdb;

//		$qry = "SELECT * FROM $wpdb->term_relationships 
//		LEFT JOIN $wpdb->posts ON $wpdb->term_relationships.object_id=$wpdb->posts.ID 
//		WHERE 1=1 
//		AND $wpdb->posts.post_status='publish' 
//		AND $wpdb->posts.post_type='jobs' 
//		AND $wpdb->term_relationships.term_taxonomy_id=$id";
//		$get_all_job = $wpdb->get_col( $qry );
//
//		return absint( sizeof( $get_all_job ) );

		$jobs_count_arg = array(
			'post_type' => 'jobs',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'specialisms',
					'field'    => 'id',
					'terms'    => $id,
				)
			),
		);
		
		$jobs_count_arg = apply_filters('job_hunt_jobs_listing_parameters', $jobs_count_arg);
		
		$jobs_count_qry = new WP_Query( $jobs_count_arg );
		return $jobs_count_qry->found_posts;
		wp_reset_postdata();
	}

}

/*
 *
 * Start Function how to create shortcode of job_specialisms
 *
 */
if ( ! function_exists( 'cs_job_specialisms_shortcode' ) ) {

	function cs_job_specialisms_shortcode( $atts, $content = "" ) {
		global $post, $wpdb, $current_user, $cs_plugin_options;

		$cs_search_result_page = isset( $cs_plugin_options['cs_search_result_page'] ) ? $cs_plugin_options['cs_search_result_page'] : '';

		$defaults = array(
			'job_specialisms_title' => '',
			'job_specialisms_title_align' => 'left',
			'job_specialisms_subtitle_switch' => 'yes',
			'job_specialisms_img' => '',
			'spec_cats' => '',
			'specialisms_columns' => '4',
			'specialisms_view' => 'classic',
			'job_specialisms_view_all_link' => '',
		);
		extract( shortcode_atts( $defaults, $atts ) );

		$job_specialisms_title = isset( $job_specialisms_title ) ? $job_specialisms_title : '';
		$job_specialisms_img = isset( $job_specialisms_img ) ? $job_specialisms_img : '';
		$spec_cats = isset( $spec_cats ) ? $spec_cats : '';
		$specialisms_columns = isset( $specialisms_columns ) ? $specialisms_columns : '1';
		$job_specialisms_subtitle_switch = isset( $job_specialisms_subtitle_switch ) ? $job_specialisms_subtitle_switch : '1';
		$specialisms_view = isset( $specialisms_view ) ? $specialisms_view : '';
		$job_specialisms_view_all_link = isset( $job_specialisms_view_all_link ) ? $job_specialisms_view_all_link : '';
		$grid_columns = '3';
		if ( $specialisms_columns <> '' ) {
			$grid_columns = 12 / $specialisms_columns;
		} else {
			$grid_columns = 3;
		}

		$cs_html = '';
		$cs_plugin_options = get_option( 'cs_plugin_options' );
		if ( class_exists( 'cs_employer_functions' ) ) {
			$cs_emp_funs = new cs_employer_functions();
		}

		$spec_cats = explode( ',', $spec_cats );

		$cs_spec_cats = '';
		$spec_counter = 1;
		foreach ( $spec_cats as $cs_cat ) {

			if ( $spec_counter == 1 ) {
				$cs_spec_cats .= "'" . $cs_cat . "'";
			} else {
				$cs_spec_cats .= ",'" . $cs_cat . "'";
			}
			$spec_counter ++;
		}

		$get_today_job = array();

		if ( $cs_spec_cats != '' ) {
			$qry = "SELECT * FROM $wpdb->terms 
                    LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id
                    WHERE 1=1 
                    AND $wpdb->term_taxonomy.taxonomy='specialisms'
                    AND {$wpdb->terms}.slug IN(" . $cs_spec_cats . ")";
			$get_terms = $wpdb->get_col( $qry );
			if ( is_array( $get_terms ) && sizeof( $get_terms ) > 0 ) {
				$get_terms = implode( ',', $get_terms );
				$qry = "SELECT * FROM $wpdb->term_relationships 
                        LEFT JOIN $wpdb->posts ON $wpdb->term_relationships.object_id=$wpdb->posts.ID 
                        WHERE 1=1 
                        AND $wpdb->posts.post_status='publish' 
                        AND $wpdb->posts.post_type='jobs' 
                        AND SUBSTR($wpdb->posts.post_date,1,10)='" . current_time( 'Y-m-d' ) . "' 
                        AND $wpdb->term_relationships.term_taxonomy_id IN ($get_terms)";
				$get_today_job = $wpdb->get_col( $qry );
			}
		}

		$cs_li_html = '';
		$cs_total_jobs = 0;

		if ( is_array( $spec_cats ) && sizeof( $spec_cats ) > 0 ) {

			if ( $specialisms_view == 'grid' ) {
				$cs_li_html .= '<ul class="cs-category-list category-medium">';
				foreach ( $spec_cats as $cs_cat ) {

					$cs_term = get_term_by( 'slug', $cs_cat, 'specialisms' );
					if ( is_object( $cs_term ) ) {
						$term_count = cs_job_specialisms_count( $cs_term->term_id );
						$cs_spec_link = '';
						if ( $cs_search_result_page != '' ) {
							$cs_spec_link = ' href="' . esc_url_raw( get_page_link( $cs_search_result_page ) . '?&amp;specialisms=' . $cs_term->slug ) . '"';
						}

						$cat_meta = get_term_meta( $cs_term->term_id, 'spec_meta_data', true );
						$cat_img = isset( $cat_meta['img'] ) ? $cat_meta['img'] : '';

						$total_jobs_count = '';
						if ( isset( $job_specialisms_subtitle_switch ) && $job_specialisms_subtitle_switch == 'yes' ) {
							$term_count = cs_job_specialisms_count( $cs_term->term_id );
							$postition_text = ($term_count > 1) ? esc_html__( 'open positions', 'jobhunt' ) : esc_html__( 'open position', 'jobhunt' );
							$total_jobs_count = '<span>(' . $term_count . ' ' . $postition_text . ')</span>';
						}

						$cs_li_html .= '<li class="col-lg-' . esc_html( $grid_columns ) . ' col-md-' . esc_html( $grid_columns ) . ' col-sm-6 col-xs-12">';
						$cs_li_html .= '<div class="category-holder">';
						if ( $cat_img != '' ) {
							$cs_li_html .= '<div class="cs-media">';
							$cs_li_html .= '<figure>';
							$cs_li_html .= '<a' . $cs_spec_link . '>';
							$cs_li_html .= '<img src="' . esc_url( $cat_img ) . '" alt="">';
							$cs_li_html .= '</a>';
							$cs_li_html .= '</figure>';
							$cs_li_html .= '</div>';
						}
						$cs_li_html .= '<div class="cs-text">';
						$cs_li_html .= '<a' . $cs_spec_link . '>';
						$cs_li_html .= '<strong>' . $cs_term->name . '</strong>';
						$cs_li_html .= $total_jobs_count;
						$cs_li_html .= '</a>';
						$cs_li_html .= '</div>';
						$cs_li_html .= '</div>';
						$cs_li_html .= '</li>';

						$cs_total_jobs += $term_count;
					}
				}
				$cs_li_html .= '</ul>';
			} else if ( $specialisms_view == 'classic' ) {
				$cs_li_html .= '
                <div class="col-lg-' . esc_html( $grid_columns ) . ' col-md-' . esc_html( $grid_columns ) . ' col-sm-6 col-xs-12">
                <div class="cs-category">
                <ul>';
				$cs_spec_counter = 1;
				$div_start_flag = 1;
				foreach ( $spec_cats as $cs_cat ) {
					if ( $div_start_flag == 0 ) {
						$cs_li_html .= '<div class="col-lg-' . esc_html( $grid_columns ) . ' col-md-' . esc_html( $grid_columns ) . ' col-sm-6 col-xs-12"><div class="cs-category"><ul>';
						$div_start_flag = 1;
					}

					$cs_term = get_term_by( 'slug', $cs_cat, 'specialisms' );
					if ( is_object( $cs_term ) ) {

						$term_count = cs_job_specialisms_count( $cs_term->term_id );

						$cs_spec_link = '';
						if ( $cs_search_result_page != '' ) {
							$cs_spec_link = ' href="' . esc_url_raw( get_page_link( $cs_search_result_page ) . '?&amp;specialisms=' . $cs_term->slug ) . '"';
						}

						$total_jobs_count = '';
						if ( isset( $job_specialisms_subtitle_switch ) && $job_specialisms_subtitle_switch == 'yes' ) {
							$term_count = cs_job_specialisms_count( $cs_term->term_id );
							$total_jobs_count = ' <span> ' . $term_count . '</span> ';
						}

						$cs_li_html .= '<li><a' . $cs_spec_link . '>' . $cs_term->name . ' <span>' . $total_jobs_count . '</span></a></li>';
						if ( fmod( $cs_spec_counter, 7 ) == 0 ) {
							$div_start_flag = 0;
							$cs_li_html .= '</ul></div></div>';
						}

						$cs_total_jobs += $term_count;
						$cs_spec_counter ++;
					}
				}
				if ( $div_start_flag == 1 ) {
					$cs_li_html .= '
                    </ul>
                    </div>
                    </div>';
				}
			} else if ( $specialisms_view == 'fancy' ) {
				$cs_li_html .= '
                <ul class="cs-category-list category-fancy">';
				$cs_spec_counter = 1;
				$div_start_flag = 1;
				foreach ( $spec_cats as $cs_cat ) {

					$cs_term = get_term_by( 'slug', $cs_cat, 'specialisms' );
					if ( is_object( $cs_term ) ) {

						$term_count = cs_job_specialisms_count( $cs_term->term_id );

						$cs_spec_link = '';
						if ( $cs_search_result_page != '' ) {
							$cs_spec_link = ' href="' . esc_url_raw( get_page_link( $cs_search_result_page ) . '?&amp;specialisms=' . cs_allow_special_char( $cs_term->slug ) ) . '"';
						}

						$cat_meta = get_term_meta( $cs_term->term_id, 'spec_meta_data', true );
						$cat_img = isset( $cat_meta['img'] ) ? $cat_meta['img'] : '';

						$total_jobs_count = '';
						if ( isset( $job_specialisms_subtitle_switch ) && $job_specialisms_subtitle_switch == 'yes' ) {
							$term_count = cs_job_specialisms_count( $cs_term->term_id );
							$total_jobs_count = ' <span>' . cs_allow_special_char( $term_count ) . ' ' . esc_html__( 'Jobs', 'jobhunt' ) . '</span> ';
						}

						$cs_li_html .= '<li class="col-lg-' . esc_html( $grid_columns ) . ' col-md-' . esc_html( $grid_columns ) . ' col-sm-6 col-xs-12"><div class="category-holder">';
						if ( $cat_img != '' ) {
							$cs_li_html .= '<div class="cs-media">
                                    <figure>
                                        <a ' . cs_allow_special_char( $cs_spec_link ) . '><img src="' . esc_url( $cat_img ) . '" alt=""></a>
                                    </figure>
                                </div>';
						}
						$cs_li_html .= '<div class="cs-text">
                                    <a ' . cs_allow_special_char( $cs_spec_link ) . '><strong>' . cs_allow_special_char( $cs_term->name ) . '</strong>' . cs_allow_special_char( $total_jobs_count ) . '</a>
                                </div>
                            </div></li>';

						$cs_total_jobs += $term_count;
						$cs_spec_counter ++;
					}
				}
				if ( $div_start_flag == 1 ) {
					$cs_li_html .= '
                    </ul>';
				}
			} else {
				$cs_li_html .= '<ul class="spatialism-sec">';
				$cs_spec_counter = 1;
				$div_start_flag = 1;
				foreach ( $spec_cats as $cs_cat ) {
					$cs_term = get_term_by( 'slug', $cs_cat, 'specialisms' );
					if ( is_object( $cs_term ) ) {
						$term_count = cs_job_specialisms_count( $cs_term->term_id );
						$cs_spec_link = '';
						if ( $cs_search_result_page != '' ) {
							$cs_spec_link = ' href="' . esc_url_raw( get_page_link( $cs_search_result_page ) . '?&amp;specialisms=' . $cs_term->slug ) . '"';
						}

						$total_jobs_count = '';
						if ( isset( $job_specialisms_subtitle_switch ) && $job_specialisms_subtitle_switch == 'yes' ) {
							$term_count = cs_job_specialisms_count( $cs_term->term_id );
							$postition_text = ($term_count > 1) ? esc_html__( 'open positions', 'jobhunt' ) : esc_html__( 'open position', 'jobhunt' );
							$total_jobs_count = '<span>(' . $term_count . ' ' . $postition_text . ')</span>';
						}

						$postition_text = ($term_count > 1) ? esc_html__( 'open positions', 'jobhunt' ) : esc_html__( 'open position', 'jobhunt' );
						$cs_li_html .= '<li class="col-lg-' . esc_html( $grid_columns ) . ' col-md-' . esc_html( $grid_columns ) . ' col-sm-6 col-xs-12"><a' . $cs_spec_link . '>' . $cs_term->name . $total_jobs_count . '</a></li>';
						$cs_total_jobs += $term_count;
						$cs_spec_counter ++;
					}
				}
				$cs_li_html .= '</ul>';
			}
		}

		$specialisms_label = esc_html__( 'View all Specialisms', 'jobhunt' );
		$specialisms_label = apply_filters( 'jobhunt_replace_view_all_specialisms', $specialisms_label );

		$cs_html .= '
        <div class="row">
          <div class="cs-spatialism-sec-all">';
		$cs_html_count = '';
		if ( $job_specialisms_subtitle_switch == 'yes' ) {

			$cs_html_count .= '<span>' . sprintf( esc_html__( '%s jobs live - %s added today.', 'jobhunt' ), absint( $cs_total_jobs ), sizeof( $get_today_job ) ) . '</span>';
		}
		$cs_html .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
		$cs_html .= '<div class="cs-element-title ' . $job_specialisms_title_align . '">';
		$cs_html .= '<h2>' . $job_specialisms_title . '</h2>';
		$cs_html .= $cs_html_count;
		$cs_html .= do_shortcode( $content );
		if ( $job_specialisms_view_all_link != '' ) {
			$cs_html .= '<div class="button-style">';
			$cs_html .= '<a href="' . esc_url( $job_specialisms_view_all_link ) . '" class="category-btn cs-color">' . $specialisms_label . '</a>';
			$cs_html .= '</div>';
		}
		$cs_html .= '</div>';
		$cs_html .= '</div>';

		$cs_html .= $cs_li_html;
		$cs_html .= '
            </div>
        </div>';

		return do_shortcode( $cs_html );
	}

	add_shortcode( 'cs_job_specialisms', 'cs_job_specialisms_shortcode' );
}
/* *
 * End Function how to create 
 * shortcode of job_specialisms
 * */