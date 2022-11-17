<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new Project relation.
 *
 * @DataProducer(
 *   id = "create_project_relation",
 *   name = @Translation("Create Project Relation"),
 *   description = @Translation("Creates a new Project relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Project Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Project relation data")
 *     )
 *   }
 * )
 */
class CreateProjectRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Creates a Project.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The newly created Project.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {

            $node = Node::load($data['dataset_id']);
            //create the relation paragraph
            $paragraph = Paragraph::create([
                'type' => 'project_relation',
                'parent_id' => $data['dataset_id'],
                'parent_type' => 'node',
                'parent_field_name' => 'field_project_relation',
                'field_project' => array(
                    'target_id' => $data['target_id']
                ),
                'field_relation' => array(
                    'target_id' => $data['relation_target_id']
                )
            ]);

            try {
                $paragraph->isNew();
                $paragraph->save();
            } catch (\Exception $ex) {
                throw new \Exception('Project Relation Paragraph save error.');
            }

            $val = $node->get('field_project_relation')->getValue();
            
            $newVal = array(
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
            );
            
            /*
             * !!!! ONLY ONE PROJECT RELATION IS AVAILABLE???
             * 
             */
            
            if (count($val) > 0) {
                $val[] = $newVal;
                $node->field_project_relation = $val;
            } else {
                $node->field_project_relation = $newVal;
            }
            
            try {
                $node->save();
            } catch (\Exception $ex) {
                throw new \Exception('Node SAVE ERROR.'.$ex->getMessage());
            }
            
            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to create a Project relation.');
    }

}
