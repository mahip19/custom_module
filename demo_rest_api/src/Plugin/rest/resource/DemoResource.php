<?php

namespace Drupal\demo_rest_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse; //to send response


// in order to configure endpoint, use plugin annotations
/*
    Provides a Demo Resource
*   @RestResource(
        id = "demo_resource",
        label = @Translation("Demo Resource"),
        uri_paths = {
            "canonical" = "/demo_rest_api/demo_resource"
        }

    )
*
*/


class DemoResponse extends ResourceBase{
    /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   *
   */
    public function get(){
        $res = ['message' => 'This is a respoonse from REST service'];
        return new ResourceResponse($res);  
    }
    
}