<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

class ImportForm extends FormBase {
  public function getFormID() {
    return 'csv_import_form';
  }

  function buildForm(array $form, FormStateInterface $form_state) {

    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));
   
    $form['content_name'] = array(
      '#type' => 'markup',
      '#markup' => t('<h1>Import to "<i>'.$content_type.'</i>" content type</h2>'),
    );

    $form['importer_id'] = array(
      '#type' => 'hidden',
      '#value' => $parameters->get('id'),
    );

    $form['csv_file'] = [
       '#type' => 'managed_file',
       '#title' => t('Choose file'),
       '#upload_validators' => array(
           'file_validate_extensions' => array('csv'),
           'file_validate_size' => array(25600000),
       ),
       '#upload_location' => 'public://csv',
       '#required' => TRUE,
    ]; 
    
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Import'),
    );
    return $form;
  }

  function validateForm(array &$form, FormStateInterface $form_state) {
    
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));
    $image = $form_state->getValue('csv_file');
    $file = File::load($image[0]);
    $file_path = $file->getFileUri();
    $file->setPermanent();
    $file->save();
    $id = $form_state->getValue('importer_id');
    $result = db_query('SELECT source,destination FROM {csv_import_fields} WHERE importer_id = :id', array(':id' => $id))->fetchAll();

    $array_key_val_pair = array();

    foreach ($result as $key => $key_val_pair) {
      $array_key_val_pair[trim($key_val_pair->source)] = trim($key_val_pair->destination);
    }
    $file_uri = file_create_url($file_path);
    $file = fopen($file_uri,'r');

    $int_row_count = 0;
    $array_import_pair = array();

    while (($line = fgetcsv($file)) !== FALSE) {
      if ($int_row_count == 0) {
        foreach ($line as $first_line_key => $first_line_value) {
          $array_import_pair[$first_line_key] = $array_key_val_pair[$first_line_value];
        }
      }
      else {

        $array_node_import = array('type' => $content_type);
        
        foreach ($array_import_pair as $key => $value) {
          $array_node_import[$value] = $line[$key];
        }

        $node = Node::create(
          $array_node_import
        );

      }

      $int_row_count++;
    }
    fclose($file);
    drupal_set_message(($int_row_count-1).' Node Imported  successfully');

  }

}