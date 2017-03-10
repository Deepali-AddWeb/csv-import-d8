<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class EditImporter extends FormBase {

  public function getFormID() {
    return 'csv_import.csv_import_add';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $result = CsvImportStorage::getcontent_type_name('1');
    $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
    $contentTypesList = [];
    foreach ($contentTypes as $contentType) {
      $contentTypesList[$contentType->id()] = $contentType->label();
    }
    $form['import_name'] = array(
      '#type' => 'textfield',
      '#title' => 'Name',
    );
    $form['content_type_list'] = array(
      '#type' => 'select',
      '#title' => 'Content type',
      '#options' => $contentTypesList,
      '#disabled' => TRUE,
      '#description' => 'Content type can not be change after created',
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Update Importer'),
    );
    return $form;
  }

  function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('import_name')) < 3) {
      $form_state->setErrorByName('import_name', $this->t('pleae enter the name atleast 3 charachter'));
    }
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    CsvImportStorage::add($form_state->getValue('import_name'), $form_state->getValue('content_type_list'));
    drupal_set_message(t($form_state->getValue('import_name') . ' added successfully'));
    return;
  }

}