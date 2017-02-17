<?php

namespace Drupal\example;
class CsvImportStorage {

  static function getAll() {
    $result = db_query('SELECT * FROM {example}')->fetchAllAssoc('id');
  return $result;
  }

  static function exists($id) {
    $result = db_query('SELECT 1 FROM {example} WHERE id = :id', array(':id' => $id))->fetchField();
    return (bool) $result;
  }

  static function add($name, $message) {
    db_insert('example')->fields(array(
      'name' => $name,
      'message' => $message,  
    ))->execute();
  }

  static function delete($id) {
    db_delete('example')->condition('id', $id)->execute();
  }

}