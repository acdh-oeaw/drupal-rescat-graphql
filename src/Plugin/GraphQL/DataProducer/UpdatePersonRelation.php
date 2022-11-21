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
     * Creates an person.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The deleted person.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $node = Node::load($data['parent_id']);
           
            $nodeValues = ($node->get('field_person_relations')->getValue()) ? $node->get('field_person_relations')->getValue() : [];

            if (count($nodeValues) > 0) {
                foreach ($nodeValues as $k => $v) {
                    if (isset($v['person_id'])) {
                        $paragraph = Paragraph::load($v['person_id']);
                        if (count($paragraph->get('field_person')->getValue()) > 0) {
                            if ($this->checkPerson($paragraph->get('field_person')->getValue(), $data['person_id'])) {
                                if (!$this->changeRelation($paragraph, $k, $data['relation_id'])) {
                                    throw new \Exception('Dataset relation field saving error.');
                                }
                            }
                        }
                    }
                }
            }
            return $node;
        }
        throw new \Exception('You dont have enough permission to Update Person Relation.'); 
    }

    /**
     * check person exists in our paragraph
     * @param array $data
     * @param int $personId
     * @return bool
     */
    private function checkPerson(array $data, int $personId): bool {
        foreach ($data as $k => $v) {
            if ($v['person_id'] == $personId) {
                return true;
            }
        }
        return false;
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

}
