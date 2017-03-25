<?php

namespace Drupal\csv_import\Controller;
use Drupal\core;
use Drupal\csv_import\CsvImportStorage;
Use Drupal\Core\Routing;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController {

  public function content() {

    $header = array(
      'id' => t('Id'),
      'name' => t('Name'),
      'content type' => t('Content Type'),
      'operations' => t('Action'),
    );
    $rows = array();
    $n = 1;
    
    foreach(CsvImportStorage::getAll() as $id=>$content) {
    
      $actions = array(
        '#type' => 'dropbutton',
        '#links' => array(
          'map fields' => array(
            'title' => 'Map Fields',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->id),
          ),
          'edit' => array(
            'title' => 'Edit',
            'url' => Url::fromUri('internal:/admin/config/csv_import/edit/'.$content->id),
          ),
          'Import' => array(
            'title' => 'Import',
            'url' => Url::fromUri('internal:/admin/config/csv_import/import/'.$content->id),
          ),
          'field processor' => array(
            'title' => 'Field processor',
            'url' => Url::fromUri('internal:/admin/config/csv_import/'.$content->id.'field_processor'),
          ),
          'delete' => array(
            'title' => 'Delete',
            'url' => Url::fromUri('internal:/admin/config/csv_import/delete/'.$content->id),
          ),
        ),
      );
      $rows[] = array(
        'data' => array($n, $content->name, $content->content_type ,drupal_render($actions))
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
      '#empty' => 'No importer added yet'
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
      $actions = array(
        '#type' => 'dropbutton',
        '#links' => array(
          'Edit' => array(
            'title' => 'Edit',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/edit/'.$content->id),
          ),
          'Delete' => array(
            'title' => 'Delete',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/delete/'.$content->id),
           ),
        ),
      );
      $rows[] = array(
        'data' => array($n, $content->source ,$content->destination,drupal_render($actions))
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
      '#empty' => 'No fields added yet'
    );
    return $table;
  }


  public function delete_processor() {
    global $base_root;
    global $base_path;
    $parameters = \Drupal::routeMatch()->getParameters();
    db_delete('csv_import_processor')->condition('field_id', $parameters->get('field_id'))->execute();
   return new TrustedRedirectResponse($base_root.$base_path.'admin/config/csv_import/list/'.$parameters->get('id').'/field_processor');
  }


  public function field_processor() {

   $header = array(
     'id' => t('Id'),
     'name' => t('Source Name'),
     'machine name' => t('Machine name'),
     'processor' => t('Processor'),
     'operations' => t('Action'),
  );
  $rows = array();
  $parameters = \Drupal::routeMatch()->getParameters();
  $result = CsvImportStorage::getimporter_fields($parameters->get('id'));
  $n = 1;
  foreach(CsvImportStorage::getimporter_fields($parameters->get('id')) as $id=>$content) {
    $result = CsvImportStorage::getprocessor($content->id);

    if (empty($result)) {
      $processor = 'No Processor added yet';
      $actions = array(
        '#type' => 'dropbutton',
        '#links' => array(
          'map fields' => array(
            'title' => 'Add processor',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/add_processor/'.$content->id),
          ),
        ),
      );
    }
    else {
      $processor = $result[0]->processor;
      $actions = array(
        '#type' => 'dropbutton',
        '#links' => array(
          'Edit Processor' => array(
            'title' => 'Edit processor',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/edit_processor/'.$content->id),
          ),
          'Delete Processor' => array(
            'title' => 'Delete processor',
            'url' => Url::fromUri('internal:/admin/config/csv_import/list/'.$content->importer_id.'/delete_processor/'.$content->id),
          ),
        ),
      );
    }

    $rows[] = array(
      'data' => array($n, $content->source, $content->destination, $processor ,drupal_render($actions))
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
    '#empty' => 'No fields mapped yet'
  );

  return $table;
  }

}