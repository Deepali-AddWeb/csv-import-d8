<?php

namespace Drupal\csv_import\Controller;
use drupal\core;
use Drupal\csv_import\CsvImportStorage;
Use Drupal\Core\Routing;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController {
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
      $map_fields = \Drupal::l(t('Map Fields'), Url::fromUri('internal:/admin/config/csv_import/list/'.$content->id));
      $delete = \Drupal::l(t('Delete'), Url::fromUri('internal:/admin/config/csv_import/delete/'.$content->id));
      $import = \Drupal::l(t('Import'), Url::fromUri('internal:/admin/config/csv_import/import/'.$content->id));
      $rows[] = array(
        'data' => array($n, $content->name, $content->content_type ,t($map_fields.' '.$import.' '.$delete))
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
    return new RedirectResponse(\Drupal::url('csv_import'));
  }

}