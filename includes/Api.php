<?php

namespace MonadicCustomApi;

use MonadicCustomApi\Api\Contact;
use MonadicCustomApi\Api\Home;
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
    new Contact();
  }
}
