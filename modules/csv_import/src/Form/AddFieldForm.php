<?php

namespace Drupal\csv_import\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
use Drupal\csv_import\CsvImportStorage;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

class AddFieldForm extends FormBase {
  public function getFormID() {
    return 'csv_import.csv_import_add';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    foreach (\Drupal::entityManager()->getFieldDefinitions('node', 'article') as $field_name => $field_definition) {
      if (!empty($field_definition->getTargetBundle())) {
        $bundleFields[$field_name] = $field_definition->getLabel().' ('.field_name.')';
      }
    }
    $form['source'] = array(
      '#type' => 'textfield',
      '#title' => 'Name',
    );
    $form['destination'] = array(
      '#type' => 'select',
      '#title' => 'Content type',
      '#options' => $bundleFields,
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add'),
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