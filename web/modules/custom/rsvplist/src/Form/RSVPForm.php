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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    if(!\Drupal::service('email.validator')->isValid($email)) {
      $form_state->setErrorByName('email', $this->t('@email is invalid email address. Please provide correct email.', ['@email' => $email]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $uid = \Drupal::currentUser()->id();

      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');
      $current_time = \Drupal::time()->getRequestTime();

      $query = \Drupal::database()->insert('rsvplist');
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created'
      ]);
      $query->values([
        $uid,
        $nid,
        $email,
        $current_time
      ]);

      $query->execute();

      \Drupal::messenger()->addMessage('Thank you for your RSVP, you are on the list of events!');
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addError(t('Unable to save RSVP. Please try again'));
    }
  }
}
