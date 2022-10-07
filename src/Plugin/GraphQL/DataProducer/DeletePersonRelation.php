<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delete a person relation entity.
 *
 * @DataProducer(
 *   id = "delete_person_relation",
 *   name = @Translation("Delete Person Relation"),
 *   description = @Translation("Delete a person Relation."),
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
class DeletePersonRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

    /**
     * The current user.
     *
     * @var \Drupal\Core\Session\AccountInterface
     */
    protected $currentUser;

    /**
     * The person node type relation fields
     * @var type
     */
    private $fields = ["person" => "field_person_relations", "dataset" => "field_person_dataset_relations"];

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
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $node = Node::load($data['id']);
            $paragraphId = $data['target_id'];
            //checking the submitted parent node type, because they are storing the
            //relation in a different field
            $type = strtolower($node->getType());
            $field = (isset($this->fields[$type])) ? $this->fields[$type] : $this->fields['person'];
            $values = ($node->get($field)->getValue()) ? $node->get($field)->getValue() : [];

            foreach ($values as $k => $v) {
                if (isset($v['target_id']) && $v['target_id'] == $paragraphId) {
                    unset($values[$k]);
                }
            }
            $node->{$field} = $values;
            $node->save();
            return $node;
        }
        throw new \Exception('You dont have enough permission to Delete a Person Relation.'); 
    }

}
