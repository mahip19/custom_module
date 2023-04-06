<?php

namespace Drupal\dynamic_url\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Response;


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

    public function sortByKey($arr, $key)
    {
        $key_arr = array_column($arr, $key);
        $_GET['sort'] == "asc" ? array_multisort($key_arr, SORT_ASC, $arr) : array_multisort($key_arr, SORT_DESC, $arr);
        return $arr;
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
            $table_headers = array(
                'tid' => 'Term ID',
                'name' => 'Term Name',
                'description' => ("Description"),
                'vid' => "Vocabulary Name",
            );
            if (!$term_res) {
                return [
                    '#type' => 'markup',
                    '#markup' => $this->t('No records found'),
                ];
            }
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


            $sorted = $this->sortByKey($term_res, 'name');
            // usort($term_res, 'sortByName');
            // array_multisort($term_res, SORT_ASC);
            $table_headers = array(
                'tid' => 'Term ID',
                'name' => 'Term Name',
                'description' => ("Description"),
                'vid' => "Vocabulary Name",
            );
            if (!$term_res) {
                return [
                    '#type' => 'markup',
                    '#markup' => 'No records found',
                ];
            }
            $res['table'] = [
                '#type' => 'table',
                '#title' => 'Taxonomy',
                '#header' => $table_headers,
                '#rows' => $sorted,

            ];
            return $res;
        }

        // shows all terms
        else {
            $vocabs[] = array();
            $types_of_vocab = 0;
            $vocabNames[] = array();
            foreach ($terms as $term) {
                $current_vocab = $term->vid->target_id;
                if (!in_array($current_vocab, $vocabNames)) {
                    $types_of_vocab += 1;
                    $vocabs[$current_vocab] = array();
                    array_push($vocabNames, $current_vocab);
                }
                $vocabs[$current_vocab][] = array(
                    'tid' => $term->tid->value,
                    'name' => $term->name->value,
                    'description' => $term->description->processed == NULL ? 'No description' : $term->description->processed,
                    'vid' => $term->vid->target_id,
                );
            }
            $table_headers = array(
                'tid' => 'Term ID',
                'name' => 'Term Name',
                'description' => ("Description"),
                'vid' => "Vocabulary Name",
            );
            if (!$vocabs) {
                return [
                    '#type' => 'markup',
                    '#markup' => $this->t('No records found'),
                ];
            }

            $res[] = array();
            for ($i = 1; $i < sizeof($vocabNames); $i++) {
                $row = $vocabs[$vocabNames[$i]];
                $res[$i * 10] = array(
                    '#type' => 'markup',
                    '#markup' => '<h2><strong>' . $vocabNames[$i] . '</strong></h2>',
                );
                $res[$i + 1] = array(
                    '#type' => 'table',
                    '#title' => 'title',
                    '#header' => $table_headers,
                    '#rows' => $_GET['sort'] == NULL ? $row : $this->sortByKey($row, 'name'),
                );
            }

            $res['test'] = array(
                '#type' => 'markup',
                '#markup' => $_GET['sort'] == NULL ? "NO QUERY" : $_GET['sort'],
            );

            return $res;
        }
    }
}
