<?php

namespace Drupal\dynamic_url\Controller;

use Drupal\Core\Controller\ControllerBase;


/**
 * Class displayVocabController.
 *
 * @package Drupal\dynamic_url\Controller
 */
class displayVocabController extends ControllerBase
{
    /**
     * Summary of display
     * @param mixed $id
     * @param mixed $vid
     * @param mixed $sort_ord
     * @return void
     */

    //  sorts multidimentional array by key
    public function sortByKey($arr, $key)
    {
        $key_arr = array_column($arr, $key);
        $_GET['sort'] == "asc" ? array_multisort($key_arr, SORT_ASC, $arr) : array_multisort($key_arr, SORT_DESC, $arr);
        return $arr;
    }

    // returns Table Header array
    public function getTableHeader($name)
    {
        $table_header = array(
            'tid' => 'Term ID',
            'name' => $name,
            'description' => ("Description"),
            'vid' => "Vocabulary Name",

        );
        return $table_header;
    }

    //  returns error page
    public function errorHandler()
    {
        return [
            '#type' => 'markup',
            '#markup' => $this->t('No records found'),
        ];
    }


    public function display($vid, $id, $sort_ord)
    {
        // $q = \Drupal::request()->query->get('values');
        // \Drupal::messenger()->addMessage(gettype($q));


        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple();

        // showing specific vocab term
        if ($id != NULL && $vid != NULL) {

            foreach ($terms as $term) {
                if ($term->vid->target_id == $vid && $term->tid->value == $id) {
                    $term_res[] = array(
                        'tid' => $term->tid->value,
                        'name' => $term->name->value,
                        'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                        'vid' => $term->vid->target_id,
                    );
                }
            }
            // TABLE HEADER ARRAY
            $table_headers = $this->getTableHeader($vid);

            // return if no records found
            if (!$term_res) {
                return $this->errorHandler();
            }

            // shows vocab title for table
            $res['vocabTitle'] = [
                '#type' => 'markup',
                '#markup' => '<h2><strong>' . $vid . '</strong></h2>',
            ];

            // returns result table
            $res['table'] = [
                '#type' => 'table',
                '#title' => 'Taxonomy',
                '#header' => $table_headers,
                '#rows' => $term_res,

            ];
            return $res;


            // shows all terms of given vocab
        } else if ($vid != NULL) {
            foreach ($terms as $term) {
                if ($term->vid->target_id != $vid) continue;
                $term_res[] = array(
                    'tid' => $term->tid->value,
                    'name' => $term->name->value,
                    'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                    'vid' => $term->vid->target_id,
                );
            }

            // sorting data by name
            if ($_GET['sort'] != NULL) $term_res = $this->sortByKey($term_res, 'name');

            // table header array

            $table_headers = $this->getTableHeader($vid);


            if (!$term_res) {
                return $this->errorHandler();
            }


            $res['vocabTitle'] = [
                '#type' => 'markup',
                '#markup' => '<h2><strong>' . $vid . '</strong></h2>',
            ];
            $res['table'] = [
                '#type' => 'table',
                '#title' => 'Taxonomy',
                '#header' => $table_headers,
                '#rows' => $term_res,

            ];
            return $res;
        }

        // shows all terms
        else {
            $vocabs = []; //result 
            $types_of_vocab = 0; //number of unique vocabs
            $vocabNames = []; // list of vocab names
            $new_table_headers = []; //table headers

            foreach ($terms as $term) {
                $current_vocab = $term->vid->target_id;

                // append new vocab into vocabNames
                if (!in_array($current_vocab, $vocabNames)) {
                    $types_of_vocab += 1;
                    $vocabs[$current_vocab] = array();
                    array_push($vocabNames, $current_vocab);


                    $new_table_headers[] = $this->getTableHeader($current_vocab);
                }

                // if vocab already present, insert term into that specific vocab
                $vocabs[$current_vocab][] = array(
                    'tid' => $term->tid->value,
                    'name' => $term->name->value,
                    'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                    'vid' => $term->vid->target_id,
                );
            }

            // unset($vocabNames[0]);

            // checks if query contains 'sort'
            if ($_GET['sort'] != NULL) {

                // sorting vocabNames and tableHeaders 
                // using builtin sort function because both these are 1D array
                $_GET['sort'] == 'asc' ? sort($vocabNames) : rsort($vocabNames);
                $_GET['sort'] == 'asc' ? sort($new_table_headers) : rsort($new_table_headers);
            }

            if (!$vocabs) {
                return $this->errorHandler();
            }

            $res[] = array(); //final result array for showing tables
            $count = 0;
            for ($i = 0; $i < sizeof($vocabNames); $i++) {
                // if (gettype($vocabNames[$i]) == 'array') continue;

                // getting current term data
                $row = $vocabs[$vocabNames[$i]];

                // creates table title
                $res[$i + 2] = array(
                    '#type' => 'markup',
                    '#markup' => '<h2><strong>' . $vocabNames[$i] . '</strong></h2>',
                );

                // creates result table
                $res[$i + 10] = array(
                    '#type' => 'table',
                    '#title' => 'title',
                    '#header' => $new_table_headers[$i],
                    '#rows' => $_GET['sort'] == NULL ? $row : $this->sortByKey($row, 'name'),
                );
                $count++;
            }

            // FOR TESTING
            $res['test'] = array(
                '#type' => 'markup',
                '#markup' => sizeof($new_table_headers),
            );

            return $res;
        }
    }
}
