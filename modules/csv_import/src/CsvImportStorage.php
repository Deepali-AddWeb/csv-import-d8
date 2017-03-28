<?php

/**
 * @file database query of csv module
 */
namespace Drupal\csv_import;
use Drupal\Core\Field;
use Drupal\Core\Entity;

class CsvImportStorage {
  static function get_field_type($node_type, $field_machinename) {
    foreach (\Drupal::entityManager()->getFieldDefinitions('node', $node_type) 
      as $field_name => $field_definition) {
        if ($field_machinename == $field_name) {
          return $field_definition->getType();
        }
    }
  }

  // list all importer data of module
  static function getAll() {
    $result = db_query('SELECT * FROM {csv_import}')->fetchAllAssoc('id');
    return $result;
  }

  // getting field processor info from @param field id 
  static function getprocessor($field_id) {
    $result = db_query('SELECT * FROM {csv_import_processor} WHERE 
      field_id = :field_id', array(':field_id' => $field_id))->fetchAll();
    return $result;
  }

  // getting fields from importer id
  static function getimporter_fields($id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE importer_id = :id',
       array(':id' => $id))->fetchAllAssoc('id');
    return $result;
  }

  // getting content type label name from id
  static function getcontent_type_name($id) {
    $result = db_query('SELECT * FROM {csv_import} WHERE id = :id', 
      array(':id' => $id))->fetchAll();
    return $result;
  }

  // getting field label name from field id
  static function get_field_label($field_id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE id = :id', 
      array(':id' => $field_id))->fetchAssoc('source');
    return $result;
  }

  // adding new importer in csv import table
  static function add($name, $content_type) {
    db_insert('csv_import')->fields(array(
      'name' => $name,
      'content_type' => $content_type,
    ))->execute();
  }

  // adding new importer in csv import table
  static function addimporterfields($importer_id, $source, $destination) {
    db_insert('csv_import_fields')->fields(array(
      'importer_id' => $importer_id,
      'source' => $source,
      'destination' => $destination,
    ))->execute();
  }

  // Update importer in csv import table
  static function updateimporterfields($field_id, $source, $destination) {
    db_update('csv_import_fields')->fields(array(
      'source' => $source,
      'destination' => $destination,
    ))
    ->condition('id', $field_id, '=')
    -> execute();
  }

  // Update field processor table
  static function updatefieldprocessor($field_id, $processor) {
    db_update('csv_import_processor')->fields(array(
      'processor' => $processor,
    ))
    ->condition('field_id', $field_id, '=')
    -> execute();
  }

  // delete fields of importer in csv import
  static function deletefields($id) {
    db_delete('csv_import_fields')->condition('id', $id)->execute();
  }

  // update importer table
  static function update_importer($import_name, $id) {
    db_update('csv_import')->fields(array(
      'name' => $import_name,
    ))
    ->condition('id', $id, '=')
    -> execute();
  }

  // adding field processor table
  static function addfieldprocessor($field_id, $processor, $importer_id) {
    db_insert('csv_import_processor')->fields(array(
      'importer_id' => $importer_id,
      'field_id' => $field_id,
      'processor' => $processor,
    ))->execute();
  }

}