<?php
namespace Drupal\csv_import;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Field;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

class ImportNode {

  // each node create function
  public static function ImportNodeExample($line, $array_import_pair, $content_type, &$context) {
    $array_node_import = array();
    $array_node_import = array('type' => $content_type);
    foreach ($array_import_pair as $key => $value) {
      
      $delimeter = CsvImportStorage::getprocessor($fieldid[$value]);

      if (!empty($delimeter && strpos($line[$key], $delimeter[0]->processor))) {
        $array_node_import[$value] = explode($delimeter[0]->processor, $line[$key]);
      }
      else {
        $array_node_import[$value] = $line[$key];
      }
      $field_type = CsvImportStorage::get_field_type($content_type, $value);

      // process image field csv data
      if ($field_type == 'image') {
        $array_image_value = array();
        $array_node_import[$value] = explode(',', $line[$key]);
        foreach($array_node_import[$value] as $key1 => $value1) {
          
          $file_destination_path = 'public://' . basename($value1);
          $file_data = file_get_contents($value1);
          $row_file = file_save_data($file_data, $file_destination_path, FILE_EXISTS_REPLACE);
          $array_image_value[] = array(
            'target_id' => $row_file->id(),
            'alt' => 'My alt',
            'title' => 'My Title'
          );
          $array_node_import[$value] = $array_image_value;
        }
      }  // end image field csv data
     
    }
    if (!empty($array_node_import['title'])) {
      $node = Node::create(
        $array_node_import
      );
      $node->save();
      $context['results'][] = "created";
    }
    else {
      drupal_set_message("node error","error");
    }
    $context['message'] = $line[0] . ' processing';

  }


  //batch finish function for node import
  function ImportNodeExampleFinishedCallback($success, $results, $operations) {
    if ($success) {
     $message = \Drupal::translation()->formatPlural(count($results), '0 Node created.', '@count Node Created.');
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}