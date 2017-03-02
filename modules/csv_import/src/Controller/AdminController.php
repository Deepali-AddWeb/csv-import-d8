<?php

namespace Drupal\csv_import\Controller;
use Drupal\csv_import\CsvImportStorage;
Use Drupal\Core\Routing;
use Drupal\Core\Routing\TrustedRedirectResponse;

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
    $n = 1;
    foreach(CsvImportStorage::getAll() as $id=>$content) {
      // Row with attributes on the row and some of its cells.
      $rows[] = array(
        'data' => array($n, $content->name, $content->content_type ,'Manage fields Import Delete')
      );
      $n++;
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

  public function list_importer_fields() {
    global $base_path;
    global $base_root;
    $header = array(
      'id' => t('Id'),
      'name' => t('Source'),
      'content type' => t('Destination'),
      'operations' => t('Action'),
    );
    $rows = array();
    $parameters = \Drupal::routeMatch()->getParameters();
    $result = CsvImportStorage::getimporter_fields($parameters->get('id'));
    $n = 1;
    foreach(CsvImportStorage::getimporter_fields($parameters->get('id')) as $id=>$content) {
      // Row with attributes on the row and some of its cells.
      $rows[] = array(
        'data' => array($n, $content->source ,$content->destination, t('<a href="'.$base_root.$base_path.'admin/config/csv_import/list/'.$content->importer_id.'/edit/'.$content->id.'">Edit</a> <a href="'.$base_root.$base_path.'admin/config/csv_import/list/'.$content->importer_id.'/delete/'.$content->id.'">Delete</a>'))
      );
      $n++;
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
  public function delete_importer_fields() {
    global $base_root;
    global $base_path;
    $parameters = \Drupal::routeMatch()->getParameters();
    CsvImportStorage::deletefields($parameters->get('field_id'));
     return new TrustedRedirectResponse($base_root.$base_path.'admin/config/csv_import/list/'.$parameters->get('id'));
  }
}