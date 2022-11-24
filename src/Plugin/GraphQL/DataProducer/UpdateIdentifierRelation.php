<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rescat_graphql\Helper\UpdateHelper;

/**
 * Update a Identifier relation entity.
 *
 * @DataProducer(
 *   id = "update_identifier_relation",
 *   name = @Translation("Update Identifier Relation"),
 *   description = @Translation("Update a Identifier Relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Identifier Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Identifier Relation data")
 *     )
 *   }
 * )
 */
class UpdateIdentifierRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;
    private $helper;

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
     * Update Identifier Relation constructor.
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
        $this->helper = new \Drupal\rescat_graphql\Helper\UpdateHelper();
    }

    /**
     * Creates an Identifier relation.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The updated Identifier relation.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {

        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $pKey = $this->getKeyFromNode((int) $data['node_id'], (int) $data['paragraph_id']);
            $paragraph = Paragraph::load($data['paragraph_id']);
            $this->changeParagraph($paragraph, $data);
            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to Update Dataset Relation.');
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
        $nodeValues = ($node->get('field_identifier_relations')->getValue()) ? $node->get('field_identifier_relations')->getValue() : [];
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
     * Change the relation inside the paragraph
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $pKey
     * @param int $relation_target_id
     * @throws \Exception
     */
    public function changeParagraph(\Drupal\paragraphs\Entity\Paragraph &$paragraph, array $data) {

        if (count($paragraph->get('field_identifier_label')->getValue()) > 0) {
            if (!$this->helper->updateSimpleField($paragraph, 'field_identifier_label', $data['identifier_label'])) {
                throw new \Exception('Paragraph relation field saving error. (label)');
            }
        }
        
        if (count($paragraph->get('field_identifier_value')->getValue()) > 0) {
            if (!$this->helper->updateSimpleField($paragraph, 'field_identifier_value', $data['identifier_value'])) {
                throw new \Exception('Paragraph relation field saving error. (value)');
            }
        }

        if (count($paragraph->get('field_identifier_service')->getValue()) > 0) {
            if (!$this->helper->updateTerm($paragraph, 'field_identifier_service', $data['identifier_service_id'])) {
                throw new \Exception('Paragraph relation field saving error. (service)');
            }
        }
    }

    

}
