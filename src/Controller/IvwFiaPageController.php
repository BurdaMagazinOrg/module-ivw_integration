<?php

namespace Drupal\ivw_integration\Controller;

use Drupal\Core\Controller\ControllerBase;

class IvwFiaPageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */

  public function content() {
    $build = array(
      '#type' => 'page',
      '#markup' => t('Hello World!'),
    );
    return "Hello World";
  }

}