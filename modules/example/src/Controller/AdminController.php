<?php
/**
 * @file
 * @author Rakesh James
 * Contains \Drupal\example\Controller\ExampleController.
 * Please place this file under your example(module_root_folder)/src/Controller/
 */
namespace Drupal\example\Controller;
use Drupal\example\CsvImportStorage;
/**
 * Provides route responses for the Example module.
 */
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
      'name' => t('Submitter name'),
      'message' => t('Message'),
      'operations' => t('Delete'),
    );
    $rows = array();
    $rows = array("id1", "submitter", "message1", "delete");
    foreach(CsvImportStorage::getAll() as $id=>$content) {
      // Row with attributes on the row and some of its cells.
      $rows[] = array(
        'data' => array($id, $content->name, $content->message,'delete')
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