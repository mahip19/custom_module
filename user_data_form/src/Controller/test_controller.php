<?php 

    // namespace is like referring a file under directory

namespace Drupal\user_data_form\Controller;

use Drupal\Core\Controller\ControllerBase;

class test_controller extends ControllerBase {

  public function myCustomPath() {
    // Your controller logic goes here.
    return [
      '#type' => 'markup',
      '#markup' => $this->t('This is test controller'),
    ];
    // return ['This is from my custom path'];
  }

}
    



