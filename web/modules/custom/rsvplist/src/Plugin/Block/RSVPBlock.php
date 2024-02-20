<?php

/**
 * @file
 * Creates a Block which displays the form contained in RSVPForm.php
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides the RSVP main block.
 *
 * @block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("The RSVP Block")
 * )
 */

class RSVPBlock extends BlockBase {
  /**
   * @inheritdoc
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('\Drupal\rsvplist\Form\RSVPForm');
  }

  /**
   * @inheritdoc
   */
  public function blockAccess(AccountInterface $account) {
    // If viewing a node, get a fully loaded node object.
    $node = \Drupal::routeMatch()->getParameter('node');
    if(!is_null($node)) {
      $enabler = \Drupal::service('rsvplist.enabler');
      if($enabler->isEnabled($node)) {
        return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
      }
    }
    return AccessResult::forbidden();
  }
}
