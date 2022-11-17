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
 * Update a Project relation entity.
 *
 * @DataProducer(
 *   id = "update_project_relation",
 *   name = @Translation("Update Project Relation"),
 *   description = @Translation("Update a Project Relation."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Project Relation")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Project Relation data")
 *     )
 *   }
 * )
 */
class UpdateProjectRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Update Project Relation constructor.
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
     * Creates an Project.
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
            
            $node = Node::load($data['dataset_id']);
            
            //the new target paragraph relation id
            $paragraphId = $data['target_id'];
           
            $nodeValues = ($node->get('field_project_relation')->getValue()) ? $node->get('field_project_relation')->getValue() : [];
            //check the dataset node values
            if (count($nodeValues) > 0) {
                foreach ($nodeValues as $k => $v) {
                    //if the field target id is the same like our posted paragaphid then load the paragraph relation
                    if (isset($v['target_id']) && $v['target_id'] === $paragraphId) {
                        $paragraph = Paragraph::load($v['target_id']);
                        //if the paragraph project field has a value
                        if (count($paragraph->get('field_project')->getValue()) > 0) {
                            //then we check if it is the right datasetid
                            if ($this->checkProject($paragraph->get('field_project')->getValue(), $data['target_id'])) {
                                if (!$this->changeRelation($paragraph, $k, $data['relation_target_id'])) {
                                    throw new \Exception('Dataset relation field saving error.');
                                }
                            }
                        }
                    }
                }
            }
            return $node;
        }
        throw new \Exception('You dont have enough permission to Update Project Relation.'); 
    }

    /**
     * check Project exists in our paragraph
     * @param array $data
     * @param int $projectId
     * @return bool
     */
    private function checkProject(array $data, int $projectId): bool {
        foreach ($data as $k => $v) {
            if ($v['target_id'] == $projectId) {
                return true;
            }
        }
        return false;
    }

    /**
     * change the Project relation id
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
