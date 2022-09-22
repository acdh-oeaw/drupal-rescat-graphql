<?php
namespace Drupal\rescat_graphql\Helper;

class UpdateHelper {
    
    
    public function updateProperty(\Drupal\node\Entity\Node &$node, array &$data, string $property, string $value = "") {
        if(!empty($value) && isset($data[$value])) {
            if(isset($node->{$property})) {
                $node->{$property} = $data[$value];
            }
        }
    }
    
    public function updateMultiLevelProperty(\Drupal\node\Entity\Node &$node, array &$data, string $property, string $level, string $value = "") {
        if(!empty($value) && isset($data[$value])) {
            if(isset($node->{$property}->{$level})) {
                $node->{$property}->{$level} = $data[$value];
            }
        }
    }
    
    public function updateBody(\Drupal\node\Entity\Node &$node, array &$data, string $value = "") {
       if(!empty($value) && isset($data[$value])) {
            $node->body = array("value" => $data[$value]);
        }
    }
}