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
 * Update a person relation entity.
 *
 * @DataProducer(
 *   id = "update_person_relation",
 *   name = @Translation("Update Person Relation"),
 *   description = @Translation("Update a person Relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Person Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Person Relation data")
 *     )
 *   }
 * )
 */
class UpdatePersonRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Update Person Relation constructor.
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
     * change the person relation id
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $newRelationID
     * @return bool
     */
    private function changeRelation(\Drupal\paragraphs\Entity\Paragraph &$paragraph, int $key, int $newRelationID): bool {
        $relations = $paragraph->get('field_relation');
        if (isset($relations[$key])) {
            $relations[$key]->target_id = $newRelationID;
            $paragraph->field_relation = $relations;
            try {
                $paragraph->save();
            } catch (\Exception $exc) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    
    /**
     * Change the institution value
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $key
     * @param int $newInstitutionID
     * @return bool
     */
    private function changeInstitution(\Drupal\paragraphs\Entity\Paragraph &$paragraph, int $key, int $newInstitutionID): bool {
        $relations = $paragraph->get('field_institution');
        if (isset($relations[$key])) {
            $relations[$key]->target_id = $newInstitutionID;
            $paragraph->field_institution = $relations;
            try {
                $paragraph->save();
            } catch (\Exception $exc) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 
     * @param array $data
     * @return type
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
       
        if (in_array('authenticated', $userRoles)) {
            $pKey = $this->getKeyFromNode((int) $data['parent_id'], (int) $data['paragraph_id']);

            //check the pragraph and change the value
            $paragraph = Paragraph::load($data['paragraph_id']);
            $this->changeParagraph($paragraph, $pKey, $data['relation_id'], $data['institution_id']);
            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to Update Person Relation.');
    }
    
    /**
     * Get the key from the node
     * @param int $dataset_id
     * @param int $paragraph_id
     * @return int
     * @throws \Exception
     */
    private function getKeyFromNode(int $nid, int $paragraph_id): int {
        $node = Node::load($nid);
        $pKey = null;
        // check the node has the paragraph
        $nodeValues = ($node->get('field_person_relations')->getValue()) ? $node->get('field_person_relations')->getValue() : [];
       
        if (count($nodeValues) === 0) {
            throw new \Exception('This node has no person relation.');
        }

        foreach ($nodeValues as $k => $v) {
            if ((int)$v['target_id'] === (int)$paragraph_id) {
                $pKey = $k;
            }
        }
        
        if ($pKey === null) {
            throw new \Exception('This node has no person relation with this id.');
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
    public function changeParagraph(\Drupal\paragraphs\Entity\Paragraph &$paragraph, int $pKey, int $relation_target_id, int $institution_id) {
        if (count($paragraph->get('field_person')->getValue()) > 0) {
            if (!$this->changeRelation($paragraph, $pKey, $relation_target_id)) {
                throw new \Exception('Paragraph relation field saving error - relation change.');
            }
            
            if (!$this->changeInstitution($paragraph, $pKey, $institution_id)) {
                throw new \Exception('Paragraph relation field saving error - institution change.');
            }
        } else {
            throw new \Exception('This paragraph relation has no project relation.');
        }
    }
    
}
