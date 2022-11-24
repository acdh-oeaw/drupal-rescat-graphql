<?php

namespace Drupal\rescat_graphql\Helper;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseRelationDelete extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;
    private $relation_fields = [
        'DeleteDatasetRelation' => 'field_dataset_relation',
        'DeleteInstitutionRelation' => 'field_institution_relations',
    ];
    private $relation_field;

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
                $configuration,
                $plugin_id,
                $plugin_definition,
                $container->get('current_user')
        );
    }

    /**
     * Delete Person Relation constructor.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param array $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Session\AccountInterface $current_user
     *   The current user.
     */
    public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->currentUser = $current_user;
    }

    private function setRelationField(string $class) {
        $this->relation_field = $this->relation_fields[$class];
    }

    /**
     * Get the key from the node
     * @param int $node_id
     * @param int $paragraph_id
     * @return int
     * @throws \Exception
     */
    private function getKeyFromNode(int $node_id, int $paragraph_id): int {
        $node = Node::load($node_id);
        $pKey = null;
        // check the node has the paragraph
        $nodeValues = ($node->get($this->relation_field)->getValue()) ? $node->get($this->relation_field)->getValue() : [];
        if (count($nodeValues) === 0) {
            throw new \Exception('This node has no Dataset relation.');
        }

        foreach ($nodeValues as $k => $v) {
            if ((int) $v['target_id'] === (int) $paragraph_id) {
                $pKey = $k;
            }
        }

        if ($pKey === null) {
            throw new \Exception('This node has no Dataset relation with this id.');
        }
        return $pKey;
    }

    /**
     * update the field values
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $newRelationID
     * @return bool
     */
    private function updateSimpleField(\Drupal\paragraphs\Entity\Paragraph &$paragraph, string $field_name, mixed $field_value): bool {

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
    private function updateTerm(\Drupal\paragraphs\Entity\Paragraph &$paragraph, string $field_name, int $new_target_id): bool {

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

}
