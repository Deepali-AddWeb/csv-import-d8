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

  static function add($name, $message, $fid, $file_path) {
    db_insert('csv_import')->fields(array(
      'name' => $name,
      'content_type' => $message,
      'csv_file_fid' => $fid,
      'csv_file_location' => $file_path,
    ))->execute();
  }

  static function delete($id) {
    db_delete('csv_import')->condition('id', $id)->execute();
  }

}