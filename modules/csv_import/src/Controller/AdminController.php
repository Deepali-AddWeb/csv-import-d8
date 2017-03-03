<?php

namespace Drupal\csv_import\Controller;
use drupal\core;
use Drupal\csv_import\CsvImportStorage;
Use Drupal\Core\Routing;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

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
      $edit = \Drupal::l(t('Edit'), Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/edit/'.$content->id));
      $delete = \Drupal::l(t('Delete'), Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/delete/'.$content->id));
      // Row with attributes on the row and some of its cells.
      $rows[] = array(
        'data' => array($n, $content->source ,$content->destination, t($edit.' '.$delete))
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

  public function delete_importer() {
    $parameters = \Drupal::routeMatch()->getParameters();
    db_delete('csv_import')->condition('id', $parameters->get('id'))->execute();
    db_delete('csv_import_fields')->condition('importer_id', $parameters->get('id'))->execute();
    return new TrustedRedirectResponse('example');
  }

  public function import() {
    $result = db_query('SELECT source,destination FROM {csv_import_fields} WHERE importer_id = :id', array(':id' => $id))->fetchAll();

    $array_key_val_pair = array();

    foreach ($result as $key => $key_val_pair) {
      $array_key_val_pair[trim($key_val_pair->source)] = trim($key_val_pair->destination);
    }
    $file = fopen($base_root.$base_path.'sites/default/files/test.csv','r');

    $int_row_count = 0;
    $array_import_pair = array();

    while (($line = fgetcsv($file)) !== FALSE) {

      if ($int_row_count == 0) {
        foreach ($line as $first_line_key => $first_line_value) {
          $array_import_pair[$first_line_key] = $array_key_val_pair[$first_line_value];
        }
      }
      else {

        $array_node_import = array('type' => 'article');
        
        foreach ($array_import_pair as $key => $value) {
          $array_node_import[$value] = $line[$key];
        }

        $node = Node::create(
          $array_node_import
        );
        $node->save();
      }

      $int_row_count++;
    }
    fclose($file);
   
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello world'),
    );
  }

}