<?php

/**
 * 
 * Contact Form With GET and PUT
 */

namespace MonadicCustomApi\Api;

use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Contact
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_route']);
  }

  /**
   * Register Route init
   */
  public function register_route()
  {
    register_rest_route(
      'mca/v1',
      '/contact',
      [
        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [$this, 'get_contacts'],
          'permission_callback' => [$this, 'get_item_permission_check']
        ],
        [
          'methods' => WP_REST_Server::CREATABLE,
          'callback' => [$this, 'create_contact'],
          'args' => [
            'name' => [
              'required' => true,
              'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
              },
              'sanitize_callback' => 'sanitize_text_field',
            ],
            'email' => [
              'required' => true,
              'validate_callback' => function ($param, $request, $key) {
                return is_email($param);
              },
              'sanitize_callback' => 'sanitize_email',
            ],
            'phone' => [
              'required' => true,
              'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
              },
              'sanitize_callback' => 'sanitize_text_field',
            ],
            'message' => [
              'required' => true,
              'validate_callback' => function ($param, $request, $key) {
                return !empty($param);
              },
              'sanitize_callback' => 'sanitize_textarea_field',
            ],
          ],
          'permission_callback' => [$this, 'get_item_permission_check']
        ]
      ]
    );
  }

  /**
   * GET all Contact
   */
  public function get_contacts(WP_REST_Request $request)
  {
    $args = [
      'post_type' => 'contact',
      'post_status' => 'publish',
      'posts_per_page' => -1,
    ];

    $query = new WP_Query($args);

    if (!$query) {
      return new WP_Error('no_contact', 'No Contact Found', ['status' => 404]);
    }

    $contacts = [];

    while ($query->have_posts()) {
      $query->the_post();

      $contacts[] = [
        'name' => get_the_title(),
        'email' => get_field('email'),
        'phone' => get_field('phone'),
        'message' => get_field('message')
      ];
    }
    wp_reset_postdata();

    return new WP_REST_Response($contacts);
  }

  /**
   * Create 
   */
  public function create_contact(WP_REST_Request $request)
  {
    $name = $request->get_param('name');
    $email = $request->get_param('email');
    $phone = $request->get_param('phone');
    $message = $request->get_param('message');

    $existing_contact = new WP_Query([
      'post_type' => 'contact',
      'post_status' => ['pending', 'publish', 'draft'],
      'meta_query' => [
        'relation' => 'OR',
        [
          'key' => 'email',
          'value' => $email,
          'compare' => '='
        ],
        [
          'key' => 'phone',
          'value' => $phone,
          'compare' => '='
        ]
      ]
    ]);

    if ($existing_contact->have_posts()) {
      return new WP_REST_Response([
        'status' => 'error',
        'message' => __('Your Email or Phone no Already Exist'),
      ], 400);
    }

    $post_id = wp_insert_post([
      'post_type' => 'contact',
      'post_status' => 'pending',
      'post_title' => $name
    ]);

    if ($post_id && !is_wp_error($post_id)) {
      update_field('email', $email, $post_id);
      update_field('phone', $phone, $post_id);
      update_field('message', $message, $post_id);

      return new WP_REST_Response([
        'status' => 'success',
        'message' => __('Form Submit Successfully'),
        'post_id' => $post_id
      ], 200);
    }

    return new WP_REST_Response([
      'status' => 'error',
      'message' => __('Someting Wrong, Please Try Again')
    ], 500);
  }


  /**
   * GET all Contact
   */
  public function get_item_permission_check($request)
  {
    return current_user_can('manage_options');
  }
}
