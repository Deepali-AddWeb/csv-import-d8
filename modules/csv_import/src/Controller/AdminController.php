<?php

namespace Drupal\csv_import\Controller;
use Drupal\csv_import\CsvImportStorage;

class AdminController {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function content() {

    $add_link = array('<p><a href= "add new import">add new import</a></p>');
    // Table header
    $header = array(
      'id' => t('Id'),
      'name' => t('Name'),
      'content type' => t('Content Type'),
      'operations' => t('Action'),
    );
    $rows = array();
    foreach(CsvImportStorage::getAll() as $id=>$content) {
      // Row with attributes on the row and some of its cells.
      $rows[] = array(
        'data' => array($id, $content->name, $content->content_type ,'Manage fields Import Delete')
      );
    }

    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => array(
        'id' => 'bd-contact-table',
      ),
    );

    return $table;
  }
}