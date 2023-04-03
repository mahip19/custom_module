<?php
/**
 * @file
 * Contains \Drupal\user_data_form\Form\user_register.
 */
namespace Drupal\user_data_form\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class user_register extends FormBase{
    /**
     * 
     * {@inheritdoc}
     */
    public function getFormId(){
        return 'user_register_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state)
    {
      
        $conn = Database::getConnection();
        $rec = array();
        if (isset($_GET['num'])){
            $q = $conn->select('user_info', 'm')
                        ->condition('id', $_GET['num'])
                        ->fields('m');
            $rec = $q->execute()->fetchAssoc();
        }


        $form['name'] = array(
            '#type' => 'textfield',
            '#title' =>$this->t("Enter name"),
            '#required' => TRUE,
            '#default_value' => (isset($rec['name']) && $_GET['num']) ? $rec['name']:'',
        );
        $form['rollno'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Enter enrollment number:"),
            '#required' => TRUE,
            '#default_value' => (isset($rec['rollno']) && $_GET['num']) ? $rec['rollno']:'',
        );  
        $form['phone'] = array(
            '#type' => 'tel',
            '#title' => $this->t("Enter contact number:"),
            '#required' => TRUE,
            '#default_value' => (isset($rec['phone']) && $_GET['num']) ? $rec['phone']:'',
        );
        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t("Enter email:"),
            '#required' => TRUE,
            '#default_value' => (isset($rec['email']) && $_GET['num']) ? $rec['email']:'',
        );
        $form['age'] = array(
            '#type' => 'textfield',
            '#title' => $this->t("Enter age"),
            '#required' => TRUE,
            '#default_value' => (isset($rec['age']) && $_GET['num']) ? $rec['age']:'',
        );
        // $form['gender'] = array(
        //     '#type' => 'select',
        //     '#title' => $this->t("Select gender"),
        //     '#options'=> array(
        //         'Male' => $this->t("Male"),
        //         'Female' => $this->t("Female"),
        //         'Other' => $this->t("Other"),
        //     )
        //     // '#required' => TRUE,
        // );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Register'),
             '#button_type' => 'primary'
        );
        return $form;
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {   
        if (strlen($form_state->getValue('rollno')) != 12){
            $form_state->setErrorByName('rollno', $this->t('Please enter valid enrollment number'));
        }
        
        if (strlen($form_state->getValue('phone')) != 10){
            $form_state->setErrorByName('phone', $this->t('Please enter valid contact number'));
        }
    }


    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        \Drupal::messenger()->addMessage($this->t('Registered Successfully. Form values are: '));
        foreach ($form_state->getValues() as $key => $value) {
            # code..
            \Drupal::messenger()->addMessage($key . ': ' . $value);
        }

        $conn = Database::getConnection();
        $field = $form_state->getValues();
        $name = $field['name'];
        $phone = $field['phone'];
        $email = $field['email'];
        $rollno = $field['rollno'];
        $age = $field['age'];

        if (isset($_GET['num'])){
            $field = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'age' => $age,
                'rollno' => $rollno,
            );
            $q = \Drupal::database();
            $q->update('user_info')
              ->fields($field)
              ->condition('id', $_GET['num'])
              ->execute();
        //   drupal_set_message("succesfully updated");
            \Drupal::messenger()->addMessage("successfully updated");

            $form_state->setRedirect('mydata.display_table_controller_display');
        }else {
            $field = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'age' => $age,
                'rollno' => $rollno,
            );
            $query = \Drupal::database();
           $query ->insert('user_info')
               ->fields($field)
               ->execute();
        //    drupal_set_message("succesfully saved");
        \Drupal::messenger()->addMessage("successfully updated");
        //    $response = new RedirectResponse("/mydata/hello/table");
        //    $response->send();
        }

    }

}