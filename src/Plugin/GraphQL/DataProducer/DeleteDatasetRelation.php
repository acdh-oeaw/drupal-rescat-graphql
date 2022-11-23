<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delete a Dataset relation entity.
 *
 * @DataProducer(
 *   id = "delete_dataset_relation",
 *   name = @Translation("Delete Dataset Relation"),
 *   description = @Translation("Delete a Dataset Relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Dataset Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Dataset Relation data")
 *     )
 *   }
 * )
 */
class DeleteDatasetRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

    /**
     * The dataset node type relation fields
     * @var type
     */

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
     * Delete dataset Relation constructor.
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
     * Delete an dataset Relation.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The deleted Project.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            
            $node = Node::load($data['node_id']);
            $paragraphId = $data['paragraph_id'];

            //delete the relation in node
            $values = ($node->get('field_dataset_relation')->getValue()) ? $node->get('field_dataset_relation')->getValue() : [];

            foreach ($values as $k => $v) {
                if (isset($v['target_id']) && $v['target_id'] == $paragraphId) {
                    unset($values[$k]);
                    $node->get('field_dataset_relation')->removeItem($k);
                }
            }
            //$node->{$field} = $values;
            try {
                $node->save();
            } catch (\Exception $ex) {
                throw new \Exception('Problem during the dataset instance node update');
            }

            // delete the paragraph 
            $storage = \Drupal::entityTypeManager()->getStorage('paragraph');
            $entity = $storage->load($paragraphId);
            
            if($entity) {
                try {
                     $entity->delete();
                } catch (\Exception $ex) {
                    throw new \Exception('Problem during the relation paragraph delete');
                }
            }

            return $node;
        }
        throw new \Exception('You dont have enough permission to Delete a Project Relation.');
    }

}
