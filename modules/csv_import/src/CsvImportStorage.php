<?php

namespace Drupal\csv_import;
class CsvImportStorage {

  static function getAll() {
    $result = db_query('SELECT * FROM {csv_import}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    $result = db_query('SELECT 1 FROM {csv_import} WHERE id = :id', array(':id' => $id))->fetchField();
    return (bool) $result;
  }

  static function getimporter_fields($id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE importer_id = :id', array(':id' => $id))->fetchAllAssoc('id');
    return $result;
  }

  static function getcontent_type_name($id) {
    $result = db_query('SELECT * FROM {csv_import} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    return $result[1]->content_type;
  }

  static function get_field_label($field_id) {
    $result = db_query('SELECT * FROM {csv_import_fields} WHERE id = :id', array(':id' => $field_id))->fetchAssoc('source');
    return $result;
  }

  static function add($name, $message, $fid, $file_path) {
    db_insert('csv_import')->fields(array(
      'name' => $name,
      'content_type' => $message,
      'csv_file_fid' => $fid,
      'csv_file_location' => $file_path,
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

}