<?php

namespace Drupal\sample_rest_resource\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Exception;
use Psr\Log\LoggerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * 
 * 
 * Provides a resource for custom get
 * @RestResource(
 *  id = "custom_get_rest_resource",
 *  label = @Translation("Custom get REST resource"),
 *  uri_paths = {
 *      "canonical" = "/car-rest"
 *
 *  }
 * 
 * )
 * 
 * 
 * 
 */

class SampleGetRestResource extends ResourceBase
{
    /**
     * A current user instance which is logged in the session.
     * @var \Drupal\Core\Session\AccountProxyInterface
     */

    protected $loggedUser;

    /**
     * Constructs a Drupal\rest\Plugin\ResourceBase object.
     *
     * @param array $config
     *   A configuration array which contains the information about the plugin instance.
     * @param string $module_id
     *   The module_id for the plugin instance.
     * @param mixed $module_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   A currently logged user instance.
     */

    public function __construct(
        array $config,
        $module_id,
        $module_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user
    ) {
        parent::__construct($config, $module_id, $module_definition, $serializer_formats, $logger);
        $this->loggedUser =  $current_user;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $config, $module_id, $module_definition)
    {
        return new static(
            $config,
            $module_id,
            $module_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('sample_rest_resource'),
            $container->get('current_user')
        );
    }

    /**
     * Responds to GET request.
     * Returns a list of taxonomy terms.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * Throws exception expected.
     */
    public function get()
    {
        // Implementing our custom REST Resource here.
        // Use currently logged user after passing authentication and validating the access of term list.
        if (!$this->loggedUser->hasPermission('access content')) throw new AccessDeniedHttpException();

        $car_id = 'car';
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($car_id);
        foreach ($terms as $term) {
            $term_res[] = array(
                'id' => $term->tid,
                'name' => $term->name,
            );
        }

        $response = new ResourceResponse($term_res);
        $response->addCacheableDependency($term_res);
        return $response;
    }
}
