<?php

namespace MonadicCustomApi;

use MonadicCustomApi\Api\Contact;
use MonadicCustomApi\Api\FeaturePost;
use MonadicCustomApi\Api\Home;
use MonadicCustomApi\Api\Page;
use MonadicCustomApi\Api\Posts;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Api
{
  public function __construct()
  {
    new Posts();
    new Home();
    new FeaturePost();
    new Page();
    new Contact();
  }
}
