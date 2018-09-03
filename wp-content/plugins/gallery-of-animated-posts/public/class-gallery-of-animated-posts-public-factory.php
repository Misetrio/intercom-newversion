<?php

/**
 * Handles (i.e. factories) the shortcode.
 *
 * Defines the plugin name, version, hooks for enqueue styles and scripts, and registers the shortcode.
 *
 * @package    Gallery_Of_Animated_Posts
 * @subpackage Gallery_Of_Animated_Posts/public
 * @author     Marcus Hogh <hogh@lenscapades.com>
 */
class Gallery_Of_Animated_Posts_Public_Factory {

	/**
	 * The query submitted to wp_query.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $query    The query submitted to wp_query.
	 */
	private $query;

	/**
	 * The query object produced by WP_Query.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $the_query    The query object produced by WP_Query.
	 */
	private $the_query;

	/**
	 * Get HTML code for gallery.
	 *
	 * @since    1.0.0
	 * @param    array                $atts            The collection of attributes that is being passed with the shortcode.
	 */
	public function get( $atts ) {

		$this->get_shortcode_attributes( $atts );

		$this->do_the_query();

		return $this->get_HTML();
	}

	/**
	 * Parse shortcode attributes.
	 *
	 * @since    1.0.0
	 * @param    array                $atts            The collection of attributes that is being passed with the shortcode.
	 */
	public function get_shortcode_attributes( $atts ) {

		$keys = array(
			'query_flag' => false,
			'query' => array(
				'cat'			=> 'int',
				'category_name'		=> 'string',
				'category__and'		=> 'array',
				'category__in'		=> 'array',
				'category__not_in'	=> 'array',
				'tag' 			=> 'string',
				'tag_id' 		=> 'int',
				'tag__and'		=> 'array',
				'tag__in'		=> 'array',
				'tag__not_in'		=> 'array',
				'tag_slug__and'		=> 'array',
				'tag_slug__in'		=> 'array',
			),
		);

		if ( is_array( $atts ) ) {

			foreach ( $atts as $key => $value ) {

				if ( true !== $keys['query_flag'] ) {

					$keys['query_flag'] = $this->get_shortcode_query( $key, $value, $keys['query'] );
				}				
			}
		}
	}

	/**
	 * Parse shortcode query attributes and set $query variable.
	 *
	 * @since    1.0.0
	 * @param    string               $key             Attribute key.
	 * @param    string               $value           Attribute value associated with $key.
	 * @param    array                $keys            Collection of admissible attribute types and attributes.
	 */
	private function get_shortcode_query( $key, $value, $keys ) {

		$this->query = array( 
					0 => 'tag', 
					1 => '',
				);

		foreach ( $keys as $query_key => $query_type  ) {

			if ( $key === $query_key ) {

				if ( 'array' === $query_type ) {

					$this->query = array(
						0	=> $query_key,
						1	=> explode( ',', $value ),
					);
				} else {

					$this->query = array(
						0	=> $query_key,
						1	=> $value,
					);
				}

				return true;
			}
		}

		return false;

	}

	/**
	 * Executes the query and sets $the_query variable.
	 *
	 * @since    1.0.0
	 */
	private function do_the_query() {
						
		if ( 'option1' == gridsby_sanitize_index_content( get_theme_mod( 'gridsby_pagination_option', 'option1' ) ) ) :
							
    			if ( 'option1' == gridsby_sanitize_index_content( get_theme_mod( 'gridsby_post_time_method' ) ) ) :  
							
    				$args = array( 
					'post_type' => 'post', 
					'posts_per_page' => -1, 
					'order' => 'ASC', 
					$this->query[0] => $this->query[1],
    				);
								
			else :
							
				$args = array( 
					'post_type' => 'post', 
					'posts_per_page' => -1, 
					$this->query[0] => $this->query[1],
	    			); 
							
			endif;
								
		else : 
							
			if ( 'option1' == gridsby_sanitize_index_content( get_theme_mod( 'gridsby_post_time_method' ) ) ) :  
							
				$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

				$args = array( 
					'post_type' => 'post', 
					'paged' => $paged,
					'posts_per_page' => intval( get_theme_mod( 'gridsby_pagi_photos_length', '15' )),
					'order' => 'ASC',
					$this->query[0] => $this->query[1],
				);
								
			else :
							
				$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

				$args = array( 
					'post_type' => 'post', 
					'paged' => $paged,
					'posts_per_page' => intval( get_theme_mod( 'gridsby_pagi_photos_length', '15' )),  
					$this->query[0] => $this->query[1],
				); 
							
			endif; 
							
		endif; 
						
		// the query
		$this->the_query = new WP_Query( $args ); 
	}

	/**
	 * Return HTML code for gallery.
	 *
	 * @since    1.0.0
	 */
	public function get_HTML() {

		$html_gallery = '';
		$html_animated_content = '';

		if ( $this->the_query->have_posts() ) :
	
			while ( $this->the_query->have_posts() ) : 

				$this->the_query->the_post();
										
       				if ( has_post_format( 'image' )) { 
							
					$postID = get_the_ID();

					if ( 'option1' == gridsby_sanitize_index_content( get_theme_mod( 'gridsby_post_link_method' ) ) ) :

						$html_gallery .= '<figure class="gallery-image">'
							. get_the_post_thumbnail( $postID, 'gridsby-gallery-thumb') 
                            				. '</figure><!-- gallery-image -->'; 

						$html_gallery .= "\n\n\t\t\t\t";
                            		
						$html_animated_content .= '<div>'
                                    			. '<div class="lightbox-content">'
							. '<div class="dummy-img">'
							. get_the_post_thumbnail( $postID, 'gridsby-gallery-full')
                                            		. '</div>' . "\n\n"
                                            		. '<h2 class="dummy-title">'
							. get_the_title( $postID )
                                                	. '<div class="share-button share-button-left"></div>'
                                            		. '</h2>' . "\n\n"
							. nl2br(get_the_content($postID))
							//. nl2br(the_content($postID))
                                        		. '</div>'
							. '</div>';                       

						$html_animated_content .= "\n\n\t\t\t\t";
                                
                           		else :

						$html_gallery .= '<a href="' . the_permalink() . '">'
                           				. '<figure class="gallery-image">'
							. get_the_post_thumbnail( $postID, 'gridsby-gallery-thumb') 
                            				. '</figure><!-- gallery-image -->'
                                			. '</a>';

						$html_gallery .= "\n\n\t\t\t\t";
                                
					endif;  

				}
                	
			endwhile; 
	
		endif; 

$html= <<<HTML
            	<section class="grid3d horizontal" id="grid3d">
			<div class="grid-wrap">
                    		<div id="gallery-container" class="gridsby infinite-scroll">
					$html_gallery
				</div><!-- gallery-container --> 
                	</div><!-- grid-wrap -->
			<div class="content">
                		$html_animated_content
				<span class="loading"></span>
				<span class="icon close-content">
                    		<i class="fa fa-close"></i>
                    		</span>
                	</div><!-- content -->
		</section><!-- grid3d --> 
HTML;


		if ( 'option1' == gridsby_sanitize_index_content( get_theme_mod( 'gridsby_pagination_option', 'option1' ) ) ) :
						
		else:
				
			if (function_exists("pagination")) {  
                
				$html .= '<div class="grid grid-pad"> '
                			. '<div class="col-1-1">'
    					. pagination( $this->the_query->max_num_pages )   
                        		. '</div>' 
                    			. '</div>';
			} 
						
		endif;

		return $html;
	}
}
