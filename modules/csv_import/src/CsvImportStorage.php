<?php

namespace Drupal\csv_import;
use Drupal\Core\Field;
use Drupal\Core\Entity;

class CsvImportStorage {
  static function get_field_type($node_type, $field_machinename) {
    foreach (\Drupal::entityManager()->getFieldDefinitions('node', $node_type) as $field_name => $field_definition) {
        if($field_machinename == $field_name) {
          return $field_definition->getType();
        }
    }
  }

  static function getAll() {
    $result = db_query('SELECT * FROM {csv_import}')->fetchAllAssoc('id');
    return $result;
  }

  static function getimporter_fields($id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE importer_id = :id', array(':id' => $id))->fetchAllAssoc('id');
    return $result;
  }

  static function getcontent_type_name($id) {
    $result = db_query('SELECT * FROM {csv_import} WHERE id = :id', array(':id' => $id))->fetchAll();
    return $result;
  }

  static function get_field_label($field_id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE id = :id', array(':id' => $field_id))->fetchAssoc('source');
    return $result;
  }

  static function add($name, $content_type) {
    db_insert('csv_import')->fields(array(
      'name' => $name,
      'content_type' => $content_type,
    ))->execute();
  }

  static function addimporterfields($importer_id, $source, $destination) {
    db_insert('csv_import_fields')->fields(array(
      'importer_id' => $importer_id,
      'source' => $source,
      'destination' => $destination,
    ))->execute();
  }

  static function updateimporterfields($field_id, $source, $destination) {
    db_update('csv_import_fields')->fields(array(
      'source' => $source,
      'destination' => $destination,
    ))
    ->condition('id', $field_id, '=')
    -> execute();
  }

  static function deleteimporter($id) {
    db_delete('csv_import')->condition('id', $id)->execute();
  }

  static function deletefields($id) {
    db_delete('csv_import_fields')->condition('id', $id)->execute();
  }

  static function update_importer($import_name, $id) {
    db_update('csv_import')->fields(array(
      'name' => $import_name,
    ))
    ->condition('id', $id, '=')
    -> execute();
  }

}