<?php

namespace Drupal\example\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity;
class AddForm extends FormBase {

  public function getFormID() {
    return 'example.csv_import_add';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    print('<pre style="color:red;">');
    print_r(EntityTypeBundleInfoInterface::getAllBundleInfo());
    print('</pre>');
    exit;
    $form['example_select'] = array(
      '#type' => 'select',
      '#title' => '',
      //'#options' => array(''=>,,
    );

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => entity_get_bundles($entity_type = NULL),
    );
    $form['message'] = array(
      '#type' => 'textarea',
      '#title' => t('Message'),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Add'),
    );
    return $form;
  }

  function validateForm(array &$form, FormStateInterface $form_state) {
    /*Nothing to validate on this form*/
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state['values']['name'];
    $message = $form_state['values']['message'];
 /*   BdContactStorage::add(check_plain($name), check_plain($message));
    
    watchdog('bd_contact', 'BD Contact message from %name has been submitted.', array('%name' => $name));
    drupal_set_message(t('Your message has been submitted'));
    $form_state['redirect'] = 'admin/content/bd_contact';*/
    return;
  }

}