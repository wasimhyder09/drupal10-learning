<?php

/**
 * @file
 * Provides site administrators with a list of all RSVP List signups so they know who is attending
 * their events.
 */

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class ReportController extends ControllerBase {
  /**
   * Gets and returns all RSVP for all nodes.
   * These are returned as associative array, with each row
   * containing the username, the node title and email for RSVP.
   *
   * @return array|null
   */
  protected function load() {
    try {
      $database = \Drupal::database();
      $select_query = $database->select('rsvplist', 'r');

      // Join the table, so we can get the entry creator's name.
      $select_query->join('users_field_data', 'u', 'r.uid = u.uid');
      // Join the node table, so we can get the event's name.
      $select_query->join('node_field_data', 'n', 'r.nid = n.nid');

      // Select the specific fields for the output.
      $select_query->addField('u', 'name', 'username');
      $select_query->addField('n', 'title');
      $select_query->addField('r', 'mail');

      $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      // Return the associative array of RSVPList entries.
      return $entries;
    }
    catch (\Exception $e) {
      // Display a user-friendly error.
      \Drupal::messenger()->addStatus(t('Unable to process the database at the moment. Please try again later.'));
      return null;
    }
  }

  /**
   * Creates the RSVPList report page.
   *
   * @returns array
   * Render array for the RSVPList report output.
   */
  public function report() {
    $content = [];

    $content['message'] = [
      '#markup' => t('Below is a list of all Events RSVPs including username, email address
      and the name of the event they will be attending'),
    ];

    $headers = [
      t('Username'),
      t('Event'),
      t('Email'),
    ];

    $table_rows = $this->load();

    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $table_rows,
      '#empty' => t('No entries available')
    ];

    // Do not cache this page by setting the max-age = 0.
    $content['#cache']['max-age'] = 0;

    // Return the populated array.
    return $content;
  }
}
