<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new Dataset relation.
 *
 * @DataProducer(
 *   id = "create_dataset_relation",
 *   name = @Translation("Create Dataset Relation"),
 *   description = @Translation("Creates a new Dataset relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Dataset Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Dataset relation data")
 *     )
 *   }
 * )
 */
class CreateDatasetRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * CreateArticle constructor.
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
     * Creates a Dataset.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The newly created Dataset.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        if ($this->currentUser->hasPermission("create Dataset relation content")) {
          
          
            $paragraph = Paragraph::create([
                        'type' => 'dataset_relations',
                        'parent_id' => $data['parent_id'],
                        'parent_type' => 'node',
                        'parent_field_name' =>'field_dataset_relations',
                        'field_dataset' => array(
                            'target_id' => $data['target_id']
                        )
            ]);
            try {
                $paragraph->isNew();
                $paragraph->save();
            } catch (\Exception $ex) {
                throw new Exception('Dataset Relation Paragraph save error.');
            }
            
            $node = Node::load($data['parent_id']);
            $val = $node->get('field_dataset_relations')->getValue();
            
            $newVal = 
                array(
                    'target_id' => $paragraph->id(),
                    'target_revision_id' => $paragraph->getRevisionId(),
                
            );
            
            if(count($val) > 0) {
                $val[] = $newVal;
                $node->field_dataset_relations  = $val;
            } else {
                $node->field_dataset_relations = $newVal;
            }
            
             try {
                 $node->save();
            } catch (\Exception $ex) {
                throw new Exception('Dataset Relation Node save error.');
            }
            
            return $paragraph;
        }
        return NULL;
    }

}
