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

class Page
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_route']);
  }

  public function register_route()
  {
    register_rest_route('mca/v1', '/page', [
      'methods'  => WP_REST_Server::READABLE,
      'callback' => [$this, 'get_about_page_data'],
      'permission_callback' => [$this, 'get_item_permission_check'],
    ]);
  }

  public function get_about_page_data(WP_REST_Request $request)
  {
    $page = $request->get_param('page');
    $args = [
      'post_type' => 'page',
      'post_status' => 'publish'
    ];

    if (!empty($page)) {
      $args['name'] = $page;
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
        'slug' => get_post_field('post_name', get_the_ID()),
        'content' => get_the_content(),
        'featured_image' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
        'author' => get_the_author(),
        'date' => get_the_date(),
        'short_description' => get_field('about_short_description', get_the_ID())
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
