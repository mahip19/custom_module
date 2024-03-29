<?php

/**
 * @file
 * Contains \Drupal\userinfo\Form\UserinfoForm.
*/

namespace Drupal\make_db\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use TYPO3\PharStreamWrapper\Exception;

class UserinfoForm extends FormBase {
    /**
    * {@inheritdoc}
    */
    public function getFormId() {
        return 'userinfo_form';
    }

    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['form_info'] = array(
            '#markup' => '<h4>Saving User Info in a custom table in the database</h4><br>',
        );
        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('First Name'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => '',
        );
        $form['last_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => '',
        );
        $form['user_email'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Email-ID'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => '',
        );
        $form['user_age'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Age'),
            '#required' => TRUE,
            '#maxlength' => 20,
            '#default_value' => '',
        ];
        $form['user_city'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#required' => TRUE,
            '#maxlength' => 50,
            '#default_value' => '',
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save Info'),
            '#button_type' => 'primary',
        );
        return $form;
    }

    /**
    * {@inheritdoc}
    */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if ($form_state->getValue('first_name') == '') {
            $form_state->setErrorByName('first_name', $this->t('Please Enter First Name'));
        }
        if ($form_state->getValue('last_name') == '') {
            $form_state->setErrorByName('last_name', $this->t('Please Enter Last Name'));
        }
        if ($form_state->getValue('user_email') == '') {
            $form_state->setErrorByName('user_email', $this->t('Please Enter Email-ID'));
        }
        if ($form_state->getValue('user_age') == '') {
            $form_state->setErrorByName('user_age', $this->t('Please Enter Age'));
        }
        if ($form_state->getValue('user_city') == '') {
            $form_state->setErrorByName('user_city', $this->t('Please Enter City'));
        }
    }

    /**
    * {@inheritdoc}
    */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        try{
            $conn = Database::getConnection();
            $field = $form_state->getValues();

            $fields["first_name"] = $field['first_name'];
            $fields["last_name"] = $field['last_name'];
            $fields["user_email"] = $field['user_email'];
            $fields["user_age"] = $field['user_age'];
            $fields["user_city"] = $field['user_city'];

            $conn->insert('user_info')
            ->fields($fields)->execute();
            \Drupal::messenger()->addMessage($this->t("User info has been succesfully saved"));
        }
        catch(Exception $ex){
            \Drupal::logger('userinfo')->error($ex->getMessage());
        }
    }
}