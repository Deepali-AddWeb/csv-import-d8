<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class ImportForm extends FormBase {
  public function getFormID() {
    return 'csv_import_form';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = \Drupal::routeMatch()->getParameters();
    $content_type = CsvImportStorage::getcontent_type_name($parameters->get('id'));

    $form['csv_file'] = [
      '#type' => 'markup',
      '#markup' => t('Hello world'),
    ];
    
    $form['content_type'] = array(
      '#type' => 'hidden',
      '#value' => $content_type,
    );
    $form['csv_file'] = [
       '#type' => 'managed_file',
       '#title' => t('Import file'),
       '#upload_validators' => array(
           'file_validate_extensions' => array('gif png jpg jpeg'),
           'file_validate_size' => array(25600000),
       ),
       '#upload_location' => 'public://csv',
       '#required' => TRUE,
    ]; 
    $form['csv_file'] = [
       '#type' => 'managed_file',
       '#title' => t('Import file'),
       '#upload_validators' => array(
           'file_validate_extensions' => array('gif png jpg jpeg'),
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
    if (strlen($form_state->getValue('import_name')) < 3) {
      $form_state->setErrorByName('import_name', $this->t('pleae enter the name atleast 3 charachter'));
    }
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    $image = $form_state->getValue('csv_file');
    $file = File::load($image[0]);
    $file_path = $file->getFileUri();
    $file->setPermanent();
    $file->save();

    CsvImportStorage::add($form_state->getValue('import_name'), $form_state->getValue('content_type_list'), $image[0], $file_path);
    drupal_set_message(t($form_state->getValue('import_name') . ' added successfully'));
    return;
  }

}