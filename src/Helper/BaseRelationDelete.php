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

    private $relation_field = [
        'DeleteDatasetRelation' => 'field_dataset_relation',
        'DeleteInstitutionRelation' => 'field_institution_relations',
    ];
    
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
    
    private function setRelationField(string $class) {
        $this->relation_field = $this->relation_field[$class];
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

    /**
     * Delete an person Relation.
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
        $this->setRelationField(get_class($this));
        $userRoles = $this->currentUser->getRoles();
        
        if (in_array('authenticated', $userRoles)) {
            $node = Node::load($data['node_id']);
            $paragraphId = $data['paragraph_id'];

            //delete the relation in node
            $values = ($node->get($this->relation_field)->getValue()) ? $node->get($this->relation_field)->getValue() : [];

            foreach ($values as $k => $v) {
                if (isset($v['target_id']) && $v['target_id'] == $paragraphId) {
                    unset($values[$k]);
                    $node->get($this->relation_field)->removeItem($k);
                }
            }

            try {
                $node->save();
            } catch (\Exception $ex) {
                throw new \Exception('Problem during the node update');
            }

            // delete the paragraph 
            $storage = \Drupal::entityTypeManager()->getStorage('paragraph');
            $entity = $storage->load($paragraphId);

            if ($entity) {
                try {
                    $entity->delete();
                } catch (\Exception $ex) {
                    throw new \Exception('Problem during the relation paragraph delete');
                }
            }
            return $node;
        }
        throw new \Exception('You dont have enough permission to Delete a Person Relation.');
    }
}

