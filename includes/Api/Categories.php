<?php

/**
 * Get all post Categories
 */

namespace MonadicCustomApi\Api;

use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Categories
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_route']);
  }

  public function register_route()
  {
    register_rest_route('mca/v1', '/categories', [
      'methods'  => WP_REST_Server::READABLE,
      'callback' => [$this, 'get_all_categories'],
      'permission_callback' => [$this, 'get_item_permission_check'],
    ]);
  }

  public function get_all_categories(WP_REST_Request $request)
  {
    $categories = get_terms([
      'taxonomy' => 'category',
      'orderby' => 'name',
      'parent' => 0,
      'hide_empty' => true,
    ]);

    if (is_wp_error($categories)) {
      return new WP_Error('no_categories', 'No categories found', ['status' => 404]);
    }

    $formatted_categories = array_map(function ($category) {
      return [
        'id' => $category->term_id,
        'name' => $category->name,
        'slug' => $category->slug,
      ];
    }, $categories);

    return new WP_REST_Response($formatted_categories);
  }

  public function get_item_permission_check($request)
  {
    return current_user_can('manage_options');
  }
}
