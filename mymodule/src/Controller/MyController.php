<?php 

    // namespace is like referring a file under directory

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class MyController extends ControllerBase {

  public function myCustomPath() {
    // Your controller logic goes here.
    return [
      '#type' => 'markup',
      '#markup' => $this->t('This is from my custom path'),
    ];
    // return ['This is from my custom path'];
  }

}
    



