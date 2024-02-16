<?php

/**
 * @file
 * A form to collect RSVP details from participants.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    // If the node was loaded, get node id.
    if(!is_null($node)) {
    $nid = $node->id();
    }
    else {
      // If node id couldn't be loaded, set to default 0.
      $nid = 0;
    }

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email Address'),
      '#size' => 25,
      '#description' => t('We will send updates to the email address you provide.'),
      '#required' => true
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP')
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submitted_email = $form_state->getValue('email');
    $this->messenger()->addMessage(t('The form is working. You entered @entry.',['@entry' => $submitted_email]));
  }
}
