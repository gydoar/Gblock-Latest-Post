<?php

/**
 * Plugin Name:       Gblock Latest Posts
 * Description:       Gutenberg Block to show latest posts.
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Version:           0.1
 * Author:            Andrés Vega
 * Author URI:        https://andrevega.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       latest-posts
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function block_render_latest_post_block($attributes)
{
	$args = array(
		'posts_per_page' => $attributes['numberOfPosts'],
		'post_status' => 'publish',
		'order' => $attributes['order'],
		'orderby' => $attributes['orderBy'],
	);

	if (isset($attributes['categories'])) {
		$args['category__in'] = array_column($attributes['categories'], 'id');
	}

	$recent_posts = get_posts($args);

	$posts = '<ul ' . get_block_wrapper_attributes() . '>';
	foreach ($recent_posts as $post) {

		$title = get_the_title($post);
		$title = $title ? $title : __('(Not Title)', 'latest-posts');
		$permalink = get_permalink($post);
		$excerpt = get_the_excerpt($post);
		$posts .= '<li>';

		if ($attributes["displayFeaturedImage"] && has_post_thumbnail($post)) {
			$posts .= get_the_post_thumbnail($post, 'large');
		}
		$posts .= '<h5><a href="' . esc_url($permalink) . '">' . esc_html($title) . '</a></h5>';
		$posts .= '<time datetime="' . esc_attr(get_the_date('c', $post)) . '">' . esc_html(get_the_date('', $post)) . '</time>';
		if (!empty($excerpt)) :
			$posts .= '<p>' . $excerpt . '</p>';
		endif;
		$posts .= '</li>';
	}

	$posts .= '</ul>';
	return $posts;
}

function create_block_gblock_latest_posts_block_init()
{
	register_block_type(__DIR__ . '/build', array(
		'render_callback'  => 'block_render_latest_post_block'
	));
}
add_action('init', 'create_block_gblock_latest_posts_block_init');
