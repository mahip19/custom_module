<?php

namespace Drupal\mydata\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

/**
 * Class DeleteForm
 * 
 * @package Drupal\mydata\Form
 */

class DeleteForm extends ConfirmFormBase
{
    /**
     * 
     * {@inheritdoc}
     */

    public function getFormId()
    {
        return 'delete_form';
    }

    public $cid;

    public function getQuestion()
    {
        return $this->t("Do you want to delete %cid ?", array('%cid' => $this->cid));
    }

    public function getCancelUrl()
    {
        return new url('mydata.display_form_controller');
    }

    public function getDescription()
    {
        return $this->t("Do this if you are sure.");
    }

    public function getConfirmText()
    {
        return $this->t("Delete it !");
    }

    public function getCancelText()
    {
        return $this->t("Cancel");
    }

    /**
     * {@inheritdoc}
     */

    public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL)
    {
        $this->cid = $cid;

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $q = \Drupal::database();
        $q->delete('user_data')
            ->condition('id', $this->cid)
            ->execute();

        \Drupal::messenger()->addMessage("successfully deleted");
        $form_state->setRedirect('mydata.display_form_controller');
    }
}
