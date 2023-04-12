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
     * @param mixed $term_id
     * @param mixed $vocab_name
     * @param mixed $sort_ord
     * @return void
     */

    //  sorts multidimentional array by key
    public function sortByKey($arr, $key, $sort_order)
    {
        $key_arr = array_column($arr, $key);
        $sort_order == 'asc' ? array_multisort($key_arr, SORT_ASC, $arr) : array_multisort($key_arr, SORT_DESC, $arr);
        return $arr;
    }

    // returns Table Header array
    public function getTableHeader($name)
    {
        $table_header = array(
            'term_id' => 'Term ID',
            'name' => $name,
            'description' => ("Description"),
            'vocab_name' => "Vocabulary Name",

        );
        return $table_header;
    }

    //  returns error page
    public function errorHandler()
    {
        return [
            '#type' => 'markup',
            '#markup' => 'No records found',
        ];
    }


    /**
     * Summary of isSortValid
     * @param mixed $vocab_name
     * @param mixed $term_id
     * @param mixed $sort_ord
     * @return string
     */
    public function isSortValid($vocab_name, $term_id, $sort_ord)
    {

        $arguments = [$vocab_name, $term_id, $sort_ord];

        if (in_array('asc', $arguments)) {
            return 'asc';
        }
        if (in_array('desc', $arguments)) {
            return 'desc';
        }
        return '';
    }


    public function get_term_with_term_id($term_id, $vocab_name, $terms)
    {
        foreach ($terms as $term) {
            if ($term->vid->target_id == $vocab_name && $term->tid->value == $term_id) {
                $terms_list[] = array(
                    'term_id' => $term->tid->value,
                    'name' => $term->name->value,
                    'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                    'vocab_name' => $term->vid->target_id,
                );
            }
        }
        return $terms_list;
    }

    public function get_term_with_vocab_name($vocab_name, $terms)
    {
        foreach ($terms as $term) {
            if ($term->vid->target_id != $vocab_name) continue;
            $terms_list[] = array(
                'term_id' => $term->tid->value,
                'name' => $term->name->value,
                'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                'vocab_name' => $term->vid->target_id,
            );
        }
        return $terms_list;
    }

    public function get_all_terms_with_vocabNames($terms, &$vocabs, &$vocabNames, &$new_table_headers)
    {
        foreach ($terms as $term) {
            $current_vocab = $term->vid->target_id;

            // append new vocab into vocabNames
            if (!in_array($current_vocab, $vocabNames)) {
                $vocabs[$current_vocab] = array();
                array_push($vocabNames, $current_vocab);
                $new_table_headers[] = $this->getTableHeader($current_vocab);
            }

            // if vocab already present, insert term into that specific vocab
            $vocabs[$current_vocab][] = array(
                'term_id' => $term->tid->value,
                'name' => $term->name->value,
                'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                'vocab_name' => $term->vid->target_id,
            );
        }
    }


    /**
     * Summary of display
     * @param mixed $vocab_name
     * @param mixed $term_id
     * @param mixed $sort_ord
     * @return array
     */
    public function display($vocab_name, $term_id, $sort_ord)
    {

        $sort_order = $this->isSortValid($vocab_name, $term_id, $sort_ord);

        $do_sort = $sort_order == '' ? false : true;

        $sort_options = ['asc', 'desc'];

        /**
         * /vocabulary/car/asc
         * here, vocab_name => car,
         *       term_id => asc
         * 
         * so, term_id needs to be of null value
         */
        if (in_array($vocab_name, $sort_options)) $vocab_name = NULL;
        if (in_array($term_id, $sort_options)) $term_id = NULL;

        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple();

        // showing specific vocab term
        if ($term_id != NULL && $vocab_name != NULL) {

            // get list of terms
            $terms_list = $this->get_term_with_term_id($term_id, $vocab_name, $terms);

            // TABLE HEADER ARRAY
            $table_headers = $this->getTableHeader($vocab_name);

            // return if no records found
            if (!$terms_list) {
                return $this->errorHandler();
            }

            // shows vocab title for table
            $res['vocabTitle'] = [
                '#type' => 'markup',
                '#markup' => '<h2><strong>' . $vocab_name . '</strong></h2>',
            ];

            $res['table'] = [
                '#type' => 'table',
                '#title' => 'Taxonomy',
                '#header' => $table_headers,
                '#rows' => $terms_list,
            ];


            // shows all terms of given vocab
        } else if ($vocab_name != NULL) {

            // get list of terms
            $terms_list = $this->get_term_with_vocab_name($vocab_name, $terms);

            // sorting data by name
            // @todo try using clean urls
            if ($do_sort) $terms_list = $this->sortByKey($terms_list, 'name', $sort_order);
            // if ($_GET['sort'] != NULL) $terms_list = $this->sortByKey($terms_list, 'name');

            // table header array

            $table_headers = $this->getTableHeader($vocab_name);

            if (!$terms_list) {
                return $this->errorHandler();
            }

            $res['vocabTitle'] = [
                '#type' => 'markup',
                '#markup' => '<h2><strong>' . $vocab_name . '</strong></h2>',
            ];
            $res['table'] = [
                '#type' => 'table',
                '#title' => 'Taxonomy',
                '#header' => $table_headers,
                '#rows' => $terms_list,
            ];
        }

        // shows all terms
        else {
            $vocabs = []; //result 
            $vocabNames = []; // list of vocab names
            $new_table_headers = []; //table headers

            $this->get_all_terms_with_vocabNames($terms, $vocabs, $vocabNames, $new_table_headers);

            // checks if query contains 'sort'
            if ($do_sort) {
                // sorting vocabNames and tableHeaders 
                // using builtin sort function because both these are 1D array
                $sort_order == "asc" ? sort($vocabNames) : rsort($vocabNames);
                $sort_order == "asc" ? sort($new_table_headers) : rsort($new_table_headers);
            }

            if (!$vocabs) {
                return $this->errorHandler();
            }

            // $res[] = array(); //final result array for showing tables
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
                    '#rows' => !$do_sort ? $row : $this->sortByKey($row, 'name', $sort_order),
                );
            }

            // FOR TESTING
            $res['test'] = array(
                '#type' => 'markup',
                '#markup' => $sort_order,
            );
        }
        return $res;
    }
}
