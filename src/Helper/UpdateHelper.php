<?php
namespace Drupal\rescat_graphql\Helper;

class UpdateHelper {
    
    
    /**
     * Update node simple property
     * @param \Drupal\node\Entity\Node $node
     * @param array $data
     * @param string $property
     * @param string $value
     */
    public function updateProperty(\Drupal\node\Entity\Node &$node, array &$data, string $property, string $value = "") {
        if(!empty($value) && isset($data[$value])) {
            error_log('itt');
            if(isset($node->{$property})) {
                error_log('itt 2');
                $node->{$property} = $data[$value];
            }
        }
    }
    
    /**
     * Update multi level node property
     * 
     * @param \Drupal\node\Entity\Node $node
     * @param array $data
     * @param string $property
     * @param string $level
     * @param string $value
     */
    public function updateMultiLevelProperty(\Drupal\node\Entity\Node &$node, array &$data, string $property, string $level, string $value = "") {
        if(!empty($value) && isset($data[$value])) {
            if(isset($node->{$property}->{$level})) {
                $node->{$property}->{$level} = $data[$value];
            }
        }
    }
    
    /**
     * Update the body with array data
     * 
     * @param \Drupal\node\Entity\Node $node
     * @param array $data
     * @param string $value
     */
    public function updateBody(\Drupal\node\Entity\Node &$node, array &$data, string $value = "") {
       if(!empty($value) && isset($data[$value])) {
            $node->body = array("value" => $data[$value]);
        }
    }
}

