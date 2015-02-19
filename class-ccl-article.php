<?php
/*
 * version: 0.0.2
*/

class CCL_Article {
	
	public function get_article_display( $article , $args = array() ) {
		
		$html = '';
		
		if ( empty( $args['display'] ) ) $args['display'] = 'promo';
		
		switch ( $args['display'] ){
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
	
}