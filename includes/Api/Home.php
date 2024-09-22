<?php

namespace MonadicCustomApi\Api;

use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class Home
{
  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_route']);
  }

  /**
   * Register Route 
   */
  public function register_route()
  {
    register_rest_route(
      'mca/v1',
      '/homepage',
      [
        [
          'methods' => WP_REST_Server::READABLE,
          'callback' => [$this, 'get_home_slider'],
          'permission_callback' => [$this, 'get_item_permission_check']
        ],
      ]
    );
  }

  public function get_home_slider(WP_REST_Request $request)
  {

    $slidersPosts = get_field('select_post_for_slider', 46);
    $editorChoiceData = get_field('editor_choice', 46);
    $editorChoicePost = $editorChoiceData['select_editor_choice'];
    $editorChoiceTitle = $editorChoiceData['title'];
    $editorChoiceContent = $editorChoiceData['content'];

    $sliders = $request->get_param('sliders');
    $editorChoice = $request->get_param('editorChoice');

    $args = [
      'post_type' => 'post',
      'post_status' => 'publish',
      'orderby' => 'post__in',
      'order' => 'DESC'
    ];

    if ($sliders !== null) {
      $args['post__in'] = $slidersPosts;
    }

    if ($editorChoice !== null) {
      $args['post__in'] = $editorChoicePost;
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
        'category' => get_the_category(),
        'author' => get_the_author(),
        'date' => get_the_date()
      ];
    }
    wp_reset_postdata();

    if ($editorChoice !== null) {
      $response_data = [
        'title' => $editorChoiceTitle,
        'content' => $editorChoiceContent,
        'posts' => $posts,
      ];
      return new WP_REST_Response($response_data);
    } else {
      return new WP_REST_Response($posts);
    }
  }

  public function get_item_permission_check($request)
  {
    return current_user_can('manage_options');
  }
}
