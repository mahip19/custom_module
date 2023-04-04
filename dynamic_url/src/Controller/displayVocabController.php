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
     * @return void
     */

    public function display($vid, $id)
    {
        // \Drupal::messenger()->addMessage("target_id and vid: " . $target_id . ",  " . $vid);

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
                '#rows' => $term_res,

            ];
            return $res;
        }

        // shows all terms
        else {
            foreach ($terms as $term) {

                $term_res[] = array(
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
        }
    }
}
