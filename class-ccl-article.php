<?php
/*
 * version: 0.2.15
*/

class CCL_Article {
	
	public function get_rest_article( $wp_rest_item ){
		
		$article = array();
		
		$article['type'] = ( ! empty( $wp_rest_item['type'] ) )? $wp_rest_item['type'] : '';
				
		$article['title'] = ( ! empty( $wp_rest_item['title'] ) )? $wp_rest_item['title'] : '';
			
		$article['content'] = ( ! empty( $wp_rest_item['content'] ) )? $wp_rest_item['content'] : '';
			
		$article['excerpt'] = ( ! empty( $wp_rest_item['excerpt'] ) )? $wp_rest_item['excerpt'] : '';
		
		$article['link'] = ( ! empty( $wp_rest_item['link'] ) )? $wp_rest_item['link'] : '';
		
		$article['link_start'] = '<a href="' . $article['link'] . '" >';
		
		$article['link_end'] = ( ! empty( $wp_rest_item['link'] ) )? $wp_rest_item['link'] : '';
		
		$article['author'] = ( ! empty( $wp_rest_item['author']['name'] ) )? $wp_rest_item['author']['name'] : '';
		
		$article['date'] = ( ! empty( $wp_rest_item['date'] ) )? $wp_rest_item['date'] : '';
		
		if ( ! empty( $wp_rest_item['featured_image']['attachment_meta']['sizes']['thumbnail']['url'] ) ) {
			
			$article['img'] = '<img src=" '
			 			. $wp_rest_item['featured_image']['attachment_meta']['sizes']['thumbnail']['url']
						. '" />';
						
			$article['img'] = apply_filters( 'post_thumbnail_html' , $article['img'] , false, false, 'thumbnail', array() );
			
		}; // end if
		
		return $article;
		
	}
	
	public function get_post_article( $post , $args = array() , $meta = false ){
		
		$article = array();
		
		$article['type'] = $post->post_type;
				
		$article['title'] = apply_filters( 'the_title' , $post->post_title );
			
		$article['content'] = apply_filters( 'the_content' , $post->post_content );
			
		$article['excerpt'] = apply_filters( 'the_excerpt' , $post->post_excerpt );
		
		$img_size = ( ! empty ( $args['img_size'] ) )?  $args['img_size'] : 'thumbnail';
		
		$article['img'] = get_the_post_thumbnail( $post->ID , $img_size );
		
		$article['link'] = get_permalink( $post->ID );
		
		$article['link_start'] = '<a href="' . $article['link'] . '" >';
		
		$article['link_end'] = '</a>';
		
		
		return $article;
		
	}
	
	/*
	 * @desc - Unset items items that are not used in this instance.
	 * @param object $post - Modified post object.
	 * @param array $args- Current item instance.
	*/
	public static function set_article_advanced( &$article , $args ){
		
		if ( ! empty( $args['no_link'] ) ) {
			
			$article['link_start'] = '';
			
			$article['link_end'] = '';
			 
		} // end if
		
		if ( ! empty( $args['no_title'] ) ) unset( $article['title'] );
		
		if ( ! empty( $args['no_text'] ) ) {
			
			unset( $article['content'] );
			
			unset( $article['excerpt'] );
			
		};
		
		if ( ! empty( $args['show_content'] ) ) {
			
			$article['excerpt'] = $article['content'];
			
		};
		
		if ( empty( $args['show_date'] ) ) {
			
			unset( $article['post_date'] );
			
		}; 
		
		if ( empty( $args['show_author'] ) ) {
			
			unset( $article['author'] );
			
		}; 
		 		
	} // end method cwp_post_obj_advanced
	
	public function get_article_display( $article , $args = array() ) {
		
		$this->set_article_advanced( $article , $args );
		
		$html = '';
		
		if ( empty( $args['display'] ) ) $args['display'] = 'promo';
		
		switch ( $args['display'] ){
			case 'search-result':
				$html .= $this->get_search_result_html( $article , $args );
				break;
			case 'gallery':
				$html .= $this->get_gallery_html( $article , $args );
				break;
			case 'article-accordion':
				$html .= $this->get_article_section_accordion( $article , $args );
				break;
			case 'promo':
			default:
				break;
		}; // end switch
		
		return $html;
		
	}
	
	public function get_article_section_accordion( $article, $args ){
		
		$id = 'ccl-article-accordion-' . rand( 0 , 100000 );
		
		$html = '';
		
		$open = ( ! empty( $args['open'] ) )? 'block' : 'none'; 
		
		$html .= '<ul id="' . $id . '" class="ccl-article-accordion">';
			
			$html .= '<li class="ccl-title">' . $article['title'] . '</li>';
			
			$html .= '<li class="ccl-content" style="display:' . $open . '">' . $article['content'] . '</li>';
		
		$html .= '</ul>';
		
		$html .='<script>if ( typeof jQuery !== undefined ){ jQuery( "body").on( "click" , "#' . $id . ' > .ccl-title" , function(){ var t = jQuery( this ); t.toggleClass( "active" ); t.siblings( ".ccl-content" ).slideToggle("medium"); var u = t.parent().siblings( ".ccl-article-accordion"); u.children(".ccl-content").slideUp( "medium"); u.children(".ccl-title").removeClass("active"); });};</script>';
		
		return $html;
		
	}
	
	public function get_gallery_html( $article, $args ){
		
		$has_image = '';
		
		$style = '';
		
		if ( ! empty( $article['img'] ) ) { 
			
			$has_image = ' has-image';
			
		} // end if 
		
		if ( ! empty( $args[ 'per_row' ] ) && '1' !=  $args[ 'per_row' ] ){
			
			$width = ( 100 / $args[ 'per_row' ] );
			
			$width = round( $width , 2, PHP_ROUND_HALF_DOWN );
			
			$style = 'margin: 0 0 1.5rem; 
					padding: 0; display: inline-block; 
					vertical-align: top; width: ' 
					. $width . '% ;';
			
		};// end if
		
		$html = '<ul class="cwp-item gallery '. $has_image . ' ' . $article['type'] . '" style="list-style-type: none;' . $style . '" >';
    
        if ( $has_image ) {
        
			$html .= '<li class="cwp-image" style="margin: 0 0.5rem;">';
			
				$html .= $article['link_start'] . $article['img'] . $article['link_end'];
			
			$html .= '</li>';
        
		} // end if
        
        $html .= '<li class="cwp-content" style="margin: 0 0.5rem; padding:0; list-style-type: none;">';
        
            if ( ! empty( $article['title'] ) ) {
                        
            	$html .= '<h4>' . $article['link_start'] . $article['title'] . $article['link_end'] . '</h4>';
                
			} // end if title
            
            if ( ! empty( $article['post_date'] ) || ! empty( $article['author'] ) ){
                        
                    $html .= '<div class="cwp-post-meta">';
            
                    if ( ! empty( $article['post_date'] ) ) {
                        
                           $html .= '<span class="cwp-post-date">' . $article['post_date'] . '</span>';
                            
                    } // end if post date
                        
                    if ( ! empty( $article['author'] ) ) { 
                        
                            $html .= '<span class="cwp-post-author">' . $article['author'] . '</span>';
                            
                    } // end if
                    
                    $html .= '</div>';
                
			} // end if
            
            if ( ! empty( $article['excerpt'] ) ){
                        
                    $html .= '<div class="cwp-post-excerpt">';
                        
                        $html .= $article['excerpt'];
                    
                    $html .= '</div>';
                
			} // end if excerpt
        
        $html .= '</li>';
    
    $html .= '</ul>';
		
		return $html;
		
	}
	
	
	public function get_search_result_html( $article, $args ){
		
		if( empty( $article['excerpt'] ) ){
			
			$article['excerpt'] = wp_trim_words( strip_shortcodes( $article['content'] ) , 35 );
			
		} // end if
		
		$ul_style = 'list-style-type: none;';
		
		$li_style = '';
		
		$has_image = ( empty( $article['img'] ) )? ' has_image' : '';
		
		$article['excerpt'] = wp_trim_words( strip_shortcodes( $article['excerpt'] ) , 35 );
		
		$html = '<ul class="cwp-item search-result '. $has_image . ' ' . $article['type'] . '" style="' . $ul_style . 'margin: 0; padding: 0.5 0;" >';
		
			$html .= '<li>';
			
			$html .= $article['link_start'] . $article['img'] . $article['link_end']; 
			
				$html .= '<ul class="search-result-content '. $has_image . ' ' . $article['type'] . '" style="' . $ul_style . 'margin: 0; padding: 0; width: 85%; display: inline-block; vertical-align: top;" >';
				
					$html .= '<li class="cwp-title">';
						
						$html .= '<h4>' . $article['link_start'] . $article['title'] . $article['link_end'] . '</h4>';
						
					$html .= '</li>';
					
					$html .= '<li class="cwp-meta">';
						
						$html .= $article['link_start'] . $article['link'] . $article['link_end'];
						
					$html .= '</li>';
					
					if ( ! empty( $article['excerpt'] ) ){
					
						$html .= '<li class="cwp-excerpt">';
						
							$html .= $article['excerpt'];
						
						$html .= '</li>';
					
					} // end if
				
				$html .= '</ul>';
			
			$html .= '<li>';
    
    	$html .= '</ul>';
		
		return $html;
		
	}
	
}