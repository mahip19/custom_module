<?php

namespace Drupal\mydata\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class MydataForm.
 *
 * @package Drupal\mydata\Form
 */
class MydataForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'mydata_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $conn = Database::getConnection();
        $record = array();
        if (isset($_GET['num'])) {
            $query = $conn->select('user_data', 'm')
                ->condition('id', $_GET['num'])
                ->fields('m');
            $record = $query->execute()->fetchAssoc();
        }
        $form['candidate_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Candidate Name:'),
            '#required' => TRUE,
            //'#default_values' => array(array('id')),
            '#default_value' => (isset($record['name']) && $_GET['num']) ? $record['name'] : '',
        );
        $form['mobile_number'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Mobile Number:'),
            '#default_value' => (isset($record['phone']) && $_GET['num']) ? $record['phone'] : '',
        );
        $form['candidate_mail'] = array(
            '#type' => 'email',
            '#title' => $this->t('Email ID:'),
            '#required' => TRUE,
            '#default_value' => (isset($record['email']) && $_GET['num']) ? $record['email'] : '',
        );

        $form['candidate_age'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('AGE'),
            '#required' => TRUE,
            '#default_value' => (isset($record['age']) && $_GET['num']) ? $record['age'] : '',
        );

        $form['candidate_gender'] = array(
            '#type' => 'select',
            '#title' => ('Gender'),
            '#options' => array(
                'Female' => $this->t('Female'),
                'male' => $this->t('Male'),
                '#default_value' => (isset($record['gender']) && $_GET['num']) ? $record['gender'] : '',
            ),
        );

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => 'save',
            //'#value' => t('Submit'),
        ];
        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $name = $form_state->getValue('candidate_name');
        if (preg_match('/[^A-Za-z]/', $name)) {
            $form_state->setErrorByName('candidate_name', $this->t('your name must in characters without space'));
        }
        if (!intval($form_state->getValue('candidate_age'))) {
            $form_state->setErrorByName('candidate_age', $this->t('Age needs to be a number'));
        }
        /* $number = $form_state->getValue('candidate_age');
          if(!preg_match('/[^A-Za-z]/', $number)) {
             $form_state->setErrorByName('candidate_age', $this->t('your age must in numbers'));
          }*/
        if (strlen($form_state->getValue('mobile_number')) != 10) {
            $form_state->setErrorByName('mobile_number', $this->t('your mobile number must in 10 digits'));
            // $form_state->setErrorByName('mobile_number', $form_state->getValue('mobile_number'));
        }
        parent::validateForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $field = $form_state->getValues();
        $name = $field['candidate_name'];
        //echo "$name";
        $number = $field['mobile_number'];
        $email = $field['candidate_mail'];
        $age = $field['candidate_age'];
        $gender = $field['candidate_gender'];

        if (isset($_GET['num'])) {
            $field  = array(
                'name'   => $name,
                'phone' =>  $number,
                'email' =>  $email,
                'age' => $age,
                'gender' => $gender,

            );
            $query = \Drupal::database();
            $query->update('user_data')
                ->fields($field)
                ->condition('id', $_GET['num'])
                ->execute();
            //   drupal_set_message("succesfully updated");
            \Drupal::messenger()->addMessage("successfully updated");

            $form_state->setRedirect('mydata.display_form_controller');
        } else {
            $field  = array(
                'name'   =>  $name,
                'phone' =>  $number,
                'email' =>  $email,
                'age' => $age,
                'gender' => $gender,

            );
            $query = \Drupal::database();
            $query->insert('user_data')
                ->fields($field)
                ->execute();
            //    drupal_set_message("succesfully saved");
            \Drupal::messenger()->addMessage("successfully saved");

            // $response = new RedirectResponse("/mydata/display/table");
            $response = new RedirectResponse("http://localhost/drupal_test/mydata/display/table");
            $response->send();
        }
    }
}
