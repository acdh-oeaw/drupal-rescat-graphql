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
            if(isset($node->{$property})) {
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
    
    /**
     * update the field values
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $newRelationID
     * @return bool
     */
    public function updateSimpleField(\Drupal\paragraphs\Entity\Paragraph &$paragraph, string $field_name, mixed $field_value): bool {

        $paragraph->{$field_name} = $field_value;
        try {
            $paragraph->save();
        } catch (\Exception $exc) {
            return false;
        }
        return true;
    }
    
    /**
     * change the TERM id
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $newRelationID
     * @return bool
     */
    public function updateTerm(\Drupal\paragraphs\Entity\Paragraph &$paragraph, string $field_name, int $new_target_id): bool {
        $paragraph->{$field_name} = array(
            'target_id' => $new_target_id
        );
        
        try {
            $paragraph->save();
        } catch (\Exception $exc) {
            return false;
        }
        return true;
    }
    
    /**
     * Change the field values inside the paragraph relationships section
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $new_relation_id
     * @param string $relation_field
     * @return bool
     */
    public function changeParagraphRelationship(\Drupal\paragraphs\Entity\Paragraph &$paragraph, int $key, int $new_relation_id, string $relation_field): bool {
        $relations = $paragraph->get($relation_field);
        if (isset($relations[$key])) {
            $relations[$key]->target_id = $new_relation_id;
            $paragraph->{$relation_field} = $relations;
            try {
                $paragraph->save();
            } catch (\Exception $exc) {
                return false;
            }
            return true;
        }
        return false;
    }
}

