<?php

/**
 * Get all posts
 */

namespace MonadicCustomApi\Api;

use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Posts
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_route']);
  }

  public function register_route()
  {
    register_rest_route('mca/v1', '/posts', [
      'methods'  => WP_REST_Server::READABLE,
      'callback' => [$this, 'get_all_posts'],
      'permission_callback' => [$this, 'get_item_permission_check'],
    ]);
  }

  public function get_all_posts(WP_REST_Request $request)
  {
    $posts_per_page = $request->get_param('perPage');
    $postId = $request->get_param('postId');
    $page = $request->get_param('page');
    $category = $request->get_param('category');

    $args = [
      'post_type' => 'post',
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'DESC',
      'paged' => !empty($page) && is_numeric($page) ? intval($page) : 1,
    ];

    if (!empty($postId) && is_numeric($postId)) {
      $args['p'] = intval($postId);
    }

    if (!empty($posts_per_page) && is_numeric($posts_per_page)) {
      $args['posts_per_page'] = intval($posts_per_page);
    }

    if (!empty($category)) {
      if (is_numeric($category)) {
        $args['cat'] = intval($category);
      } else {
        $args['category_name'] = sanitize_text_field($category);
      }
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
      return new WP_Error('no_posts', 'No posts found', ['status' => 404]);
    }

    $posts = [];

    while ($query->have_posts()) {
      $query->the_post();

      $posts[] = [
        'id' => get_the_ID(),
        'title' => get_the_title(),
        'excerpt' => get_the_excerpt(),
        'content' => get_the_content(),
        'featured_image' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
        'author' => get_the_author(),
        'date' => get_the_date(),
        'categories' => get_the_category()
      ];
    }
    wp_reset_postdata();

    return new WP_REST_Response($posts);
  }

  public function get_item_permission_check($request)
  {
    return current_user_can('manage_options');
  }
}
