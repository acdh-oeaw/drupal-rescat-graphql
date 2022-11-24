<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new person relation.
 *
 * @DataProducer(
 *   id = "create_person_relation",
 *   name = @Translation("Create Person Relation"),
 *   description = @Translation("Creates a new person relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Person Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Person relation data")
 *     )
 *   }
 * )
 */
class CreatePersonRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;
    
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
     * CreatePerson relation constructor.
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

    /**
     * Creates an person.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The newly created person.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $node = Node::load($data['parent_id']);
            
            $paragraph = Paragraph::create([
                        'type' => 'person_relations',
                        'parent_id' => $data['parent_id'],
                        'parent_type' => 'node',
                        'parent_field_name' => 'field_person_relations',
                        'field_person' => array(
                            'target_id' => $data['person_id']
                        ),
                        'field_relation' => array(
                            'target_id' => $data['relation_id']
                        ),
                        'field_institution' => array(
                            'target_id' => $data['institution_id']
                        ),
                        'field_start' => $data['start'],
                        'field_end' => $data['end']
            ]);
            $paragraph->isNew();
            $paragraph->save();

            $val = $node->get('field_person_relations')->getValue();

            $newVal = array(
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
            );

            if (count($val) > 0) {
                $val[] = $newVal;
                $node->field_person_relations = $val;
            } else {
                $node->field_person_relations = $newVal;
            }

            $node->save();
         
            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to create a person relation.');    
    }
   
}
