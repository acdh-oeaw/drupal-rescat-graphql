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
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {

            $node = Node::load($data['dataset_instance_id']);
            $val = $node->get('field_dataset_relation')->getValue();
            
            if (count($val) > 0) {
                throw new \Exception("There is already a Dataset Relation definied and it could be only one!");
            }
            
            //create the relation paragraph
            $paragraph = Paragraph::create([
                        'type' => 'dataset_relation',
                        'parent_id' => $data['dataset_instance_id'],
                        'parent_type' => 'node',
                        'parent_field_name' => 'field_dataset_relation',
                        'field_dataset' => array(
                            'target_id' => $data['dataset_id']
                        ),
                        'field_relation' => array(
                            'target_id' => $data['relation_target_id']
                        )
            ]);

            try {
                $paragraph->isNew();
                $paragraph->save();
            } catch (\Exception $ex) {
                throw new \Exception('Dataset Relation Paragraph save error.');
            }

            

            $newVal = array(
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
            );

            $node->field_dataset_relation = $newVal;

            try {
                $node->save();
            } catch (\Exception $ex) {
                throw new \Exception('Node SAVE ERROR.' . $ex->getMessage());
            }

            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to create a Dataset relation.');
    }

}
