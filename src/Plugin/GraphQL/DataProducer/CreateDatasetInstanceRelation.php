<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new DatasetInstance relation.
 *
 * @DataProducer(
 *   id = "create_dataset_instance_relation",
 *   name = @Translation("Create DatasetInstance Relation"),
 *   description = @Translation("Creates a new DatasetInstance relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("DatasetInstance Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("DatasetInstance relation data")
 *     )
 *   }
 * )
 */
class CreateDatasetInstanceRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * DatasetInstance Relation constructor.
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
     * Creates an DatasetInstance Relation.
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
        //if ($this->currentUser->hasPermission("create dataset instance relation")) {
            
            $node = Node::load($data['parent_id']);
            //checking the submitted parent node type, because they are storing the
            
            $paragraph = Paragraph::create([
                        'type' => 'dataset_instance_relations',
                        'parent_id' => $data['parent_id'],
                        'parent_type' => 'node',
                        'parent_field_name' => 'field_dataset_instance_relations',
                        'field_dataset_instance_relation' => array(
                            'target_id' => $data['target_id']
                        )
            ]);
            $paragraph->isNew();
            $paragraph->save();

            $val = $node->get('field_dataset_instance_relations')->getValue();
            
            $newVal = 
                array(
                    'target_id' => $paragraph->id(),
                    'target_revision_id' => $paragraph->getRevisionId(),
                
            );
            
            if(count($val) > 0) {
                $val[] = $newVal;
                $node->field_dataset_instance_relations  = $val;
            } else {
                $node->field_dataset_instance_relations = $newVal;
            }
           
            $node->save();
            foreach($paragraph as $k => $v) {
            error_log(print_r($k, true));
            error_log(print_r($v->getValue(), true));
                
            }
            return $paragraph;
        //}
    }
}
