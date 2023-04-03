<?php

namespace Drupal\mydata\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;

/**
 * Class DisplayTableController.
 *
 * @package Drupal\mydata\Controller
 */
class DisplayTableController extends ControllerBase
{

    public function getContent()
    {
        // First we'll tell the user what's going on. This content can be found
        // in the twig template file: templates/description.html.twig.
        // @todo: Set up links to create nodes and point to devel module.
        $build = [
            'description' => [
                '#theme' => 'mydata_description',
                '#description' => 'foo',
                '#attributes' => [],
            ],
        ];
        return $build;
    }

    /**
     * Display.
     *
     * @return string
     *   Return Hello string.
     */
    public function display()
    {
        /**return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: display with parameter(s): $name'),
    ];*/
        //create table header
        $header_table = array(
            'id' =>    $this->t('SrNo'),
            'name' => $this->t('Name'),
            'phone' => $this->t('Phone'),
            //'email'=>$this->t('Email'),
            'age' => $this->t('Age'),
            'gender' => $this->t('Gender'),
            //'website' => $this->t('Web site'),
            'opt' => $this->t('operations'),
            'opt1' => $this->t('operations'),
        );

        //select records from table
        $query = \Drupal::database()->select('user_data', 'm');
        $query->fields('m', ['id', 'name', 'phone', 'email', 'age', 'gender']);
        $results = $query->execute()->fetchAll();
        $rows = array();
        foreach ($results as $data) {
            $delete = Url::fromUserInput('/mydata/form/delete/' . $data->id);
            $edit   = Url::fromUserInput('/mydata/form/mydata?num=' . $data->id);

            //print the data from table
            $rows[] = array(
                'id' => $data->id,
                'name' => $data->name,
                'phone' => $data->phone,
                //'email' => $data->email,
                'age' => $data->age,
                'gender' => $data->gender,
                //'website' => $data->website,

                \Drupal\Core\Link::fromTextAndUrl('Delete', $delete),
                \Drupal\Core\Link::fromTextAndUrl('Edit', $edit),
            );
        }
        //display data in site
        $form['table'] = [
            '#type' => 'table',
            '#title' => 'Student List',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => $this->t('No users found'),
        ];
        return $form;
    }
}
