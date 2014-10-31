<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/
//Totally remove markup
add_filter( 'black_studio_tinymce_before_text', '__return_empty_string' );
add_filter( 'black_studio_tinymce_after_text', '__return_empty_string' );



/*
class TinyMceExcerptCustomization{
const textdomain = '';
const custom_exceprt_slug = '_custom-excerpt';
var $contexts;
 

function __construct($contexts=array('post', 'page')){
$this->contexts = $contexts;
add_action('admin_menu', array($this, 'remove_excerpt_metabox'));
add_action('add_meta_boxes', array($this, 'add_tinymce_to_excerpt_metabox'));
add_filter('wp_trim_excerpt', array($this, 'custom_trim_excerpt'), 10, 2);
add_action('save_post', array($this, 'save_box'));
}

function remove_excerpt_metabox(){
foreach($this->contexts as $context)
remove_meta_box('postexcerpt', $context, 'normal');
}

function add_tinymce_to_excerpt_metabox(){
foreach($this->contexts as $context)
add_meta_box(
'tinymce-excerpt',
__('Excerpt', self::textdomain),
array($this, 'tinymce_excerpt_box'),
$context,
'normal',
'high'
);
}

function custom_trim_excerpt($text, $raw_excerpt) {
global $post;
$custom_excerpt = get_post_meta($post->ID, self::custom_exceprt_slug, true);
if(empty($custom_excerpt)) return $text;
return $custom_excerpt;
}
 

function tinymce_excerpt_box($post){
$content = get_post_meta($post->ID, self::custom_exceprt_slug, true);
if(empty($content)) $content = '';
wp_editor(
$content,
self::custom_exceprt_slug,
array(
'wpautop'	=>	true,
'media_buttons'	=>	false,
'textarea_rows'	=>	10,
'textarea_name'	=>	self::custom_exceprt_slug
)
);
}

function save_box($post_id){
update_post_meta($post_id, self::custom_exceprt_slug, $_POST[self::custom_exceprt_slug]);
}
}
 
global $tinymce_excerpt;
$tinymce_excerpt = new TinyMceExcerptCustomization();
*/


if(!function_exists('inb_modify_breadcrumb'))
{
function inb_modify_breadcrumb($trail)
	{

	

        $parent = get_post_meta(avia_get_the_ID(), 'breadcrumb_parent', true);

		if(get_post_type() === "portfolio")
		{
			$page 	= "";
			$front 	= avia_get_option('frontpage');

			if(empty($parent) && !current_theme_supports('avia_no_session_support') && session_id() && !empty($_SESSION['avia_portfolio']))
			{
				$page = $_SESSION['avia_portfolio'];
			}
            else
            {
                $page = $parent;
            }

			if(!$page || $page == $front)
			{
				$args = array( 'post_type' => 'page', 'meta_query' => array(
						array( 'key' => '_avia_builder_shortcode_tree', 'value' => 'av_portfolio', 'compare' => 'LIKE' ) ) );

				$query = new WP_Query( $args );

				if($query->post_count == 1)
				{
					$page = $query->posts[0]->ID;
				}
				else if($query->post_count > 1)
				{
					foreach($query->posts as $entry)
					{
						if ($front != $entry->ID)
						{
							$page = $entry->ID;
							break;
						}
					}
				}
			}

			if($page)
			{
				if($page == $front)
				{
					$newtrail[0] = $trail[0];
					$newtrail['trail_end'] = $trail['trail_end'];
					$trail = $newtrail;
				}
				else
				{
					$newtrail = avia_breadcrumbs_get_parents( $page, '' );
					array_unshift($newtrail, $trail[0]);
					$newtrail['trail_end'] = $trail['trail_end'];
					$trail = $newtrail;
				}
			}
			//custom trail Portfollio
			
			
			
			//array_shift($trail);
			
			//$firstLink = preg_replace('/<a(.*)href="([^"]*)"(.*)>/','<a$1href="javascript:void(0)"$3>',$trail[0]);
			//array_shift($trail);
			//array_unshift($trail,$firstLink);
			//$trail[1] = preg_replace('/<a(.*)href="([^"]*)"(.*)>/','<a$1 class="current-active" href="$2" $3>',$trail[1]);
			//print_r($trail);
			$trail = array();
			$trail = get_breadcrumbs_for_detail_portfollio($page);
				
			//print_r($trail);
			
		}
		else if(get_post_type() === "post" && (is_category() || is_archive() || is_tag()))
		{
			
			$front = avia_get_option('frontpage');
			$blog = !empty($parent) ? $parent : avia_get_option('blogpage');

			if($front && $blog && $front != $blog)
			{
				$blog = '<a href="' . get_permalink( $blog ) . '" title="' . esc_attr( get_the_title( $blog ) ) . '">' . get_the_title( $blog ) . '</a>';
				array_splice($trail, 1, 0, array($blog));
			}
		}
		else if(get_post_type() === "post")
		{
			
			$front 			= avia_get_option('frontpage');
			$blog 			= avia_get_option('blogpage');
			
			$custom_blog 	= avia_get_option('blog_style') === 'custom' ? true : false;
			
			if(!$custom_blog)
			{
				
				if($blog == $front)
				{

					unset($trail[1]);
				}
			}
			else
			{

				if($blog != $front)
				{
					
					$blog = '<a href="' . get_permalink( $blog ) . '" title="' . esc_attr( get_the_title( $blog ) ) . '">' . get_the_title( $blog ) . '</a>';
					array_splice($trail, 1, 0, array($blog));
					
				}
			}

			//custom trail contain childs  pages	
			
				$trail = array();
				$args = array('child_of' => $blog);
				$pages = get_pages($args);

				if ($pages) :
				foreach ($pages as $post):

				    $trail[] = '<a href="'.esc_attr(get_permalink($post) ).'">'.get_the_title($post).'</a>';
				endforeach;
				endif;
			
		}
		if (get_post_type()==="page" ||  get_post_type()==="post" ){
			$currentPageID  = get_the_ID();
			$currentPage 	= get_post(get_the_ID());
			$currentPageParentID = $currentPage->post_parent;
			
			$front 			= avia_get_option('frontpage');
			$trail = array();
			$currentActivePageId = avia_get_the_ID();
			
			
			if($currentPageParentID != 0){
				//get_permalink( $currentPageParentID )
				$trail[] = '<a href="' . '#' . '" title="' . esc_attr( get_the_title( $currentPageParentID ) ) . '" >' . get_the_title( $currentPageParentID ) . '</a>';
			
				$args = array('child_of' => $currentPageParentID,'sort_column'  => 'menu_order');
				$pages = get_pages($args);

				if ($pages) :
				foreach ($pages as $post):
					$classActivePage = ($currentActivePageId == $post->ID) ? " class='current-active' " : "" ; 	
				    $trail[] = '<a href="'.esc_attr(get_permalink($post) ).'" '.$classActivePage. '>'.get_the_title($post).'</a>';
				endforeach;
				endif;
			
			}else{
				//get_permalink( $currentPageID )
				$trail[] = '<a href="' . '#' . '" title="' . esc_attr( get_the_title( $currentPageID ) ) . '" >' . get_the_title( $currentPageID ) . '</a>';
				$args = array('child_of' => $currentPageID,'sort_column'  => 'menu_order');
				$pages = get_pages($args);

				if ($pages) :
				foreach ($pages as $post):
					$classActivePage = ($currentActivePageId == $post->ID) ? " class='current-active' " : "" ; 	
				    $trail[] = '<a href="'.esc_attr(get_permalink($post) ).'" '.$classActivePage. '>'.get_the_title($post).'</a>';
				endforeach;
				endif;
			
			}
			
			
			
		}

		return $trail;
	}
}

add_action('after_setup_theme','avia_remove_portfolio_breadcrumb');

function avia_remove_portfolio_breadcrumb(){
	remove_filter('avia_breadcrumbs_trail','avia_modify_breadcrumb');
	add_filter('avia_breadcrumbs_trail','inb_modify_breadcrumb');
}

add_filter('avf_portfolio_sort_first_label','change_portfolio_sort_first_label');
function change_portfolio_sort_first_label(){
 $first = __('All','avia_framework' );
 $second = strtolower(get_current_parent_page());
 if ($second) return $first. ' ' .$second;
 return $first;
}
function get_current_parent_page(){

	if (get_post_type()==="page" ||  get_post_type()==="post" ){
			$currentPageID  = get_the_ID();
			$currentPage 	= get_post(get_the_ID());
			$currentPageParentID = $currentPage->post_parent;
			$currentActivePageId = avia_get_the_ID();
			if($currentPageParentID != 0){
				return get_the_title( $currentPageParentID); 
			}else{
				return get_the_title( $currentPageID );
			}
		}

}
function get_breadcrumbs_for_detail_portfollio($post_id){

			while ( $post_id ) {

					/* Get the post by ID. */
					$page = get_page( $post_id );
					
					/* Set the parent post's parent to the post ID. */
					if(is_object($page))
					{
						$currentPageID = $page->post_parent;
						if($currentPageID):
								$args = array('child_of' => $currentPageID,'sort_column'  => 'menu_order');
								$pages = get_pages($args);

								if ($pages) :
								foreach ($pages as $post):
									$classActivePage = ($post_id == $post->ID) ? " class='current-active' " : "" ; 	
									$secondparents[] = '<a href="'.esc_attr(get_permalink($post) ).'" '.$classActivePage. '>'.get_the_title($post).'</a>';
								endforeach;
								endif;
						
						else:
						//get_permalink( $post_id ) 
							$parents[]  = '<a href="javascript:void(0)" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';
						endif;
						$post_id = $page->post_parent;
					}
					else
					{
						$post_id = "";
					}
				}
				if ( isset( $parents ) )
				$trail = $parents ;
				if ( isset( $secondparents ) ) $trail = array_merge($trail,$secondparents);
				return $trail ;
}

