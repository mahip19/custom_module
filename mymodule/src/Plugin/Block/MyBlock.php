<?php
namespace Drupal\mymodule\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'custom' Block.
 *
 * @Block(
 *   id = "my_block",
 *   admin_label = @Translation("My Block"),
 *   category = @Translation("mymodule"),
 * )
 */
class MyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('This is custom block'),
    ];
    // return 'This is custom block';
  }
}