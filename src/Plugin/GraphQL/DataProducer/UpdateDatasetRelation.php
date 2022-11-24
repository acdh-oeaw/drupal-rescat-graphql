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
 * Update a Dataset relation entity.
 *
 * @DataProducer(
 *   id = "update_dataset_relation",
 *   name = @Translation("Update Dataset Relation"),
 *   description = @Translation("Update a Dataset Relation."),
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
class UpdateDatasetRelation extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Update Dataset Relation constructor.
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
     * Creates an Dataset.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The deleted Dataset.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {
      
        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {
            $pKey = $this->helper->getKeyFromNode((int) $data['dataset_instance_id'], (int) $data['paragraph_id'], 'field_dataset_relation' );
            //check the pragraph and change the value
            $paragraph = Paragraph::load($data['paragraph_id']);
            $this->changeParagraph($paragraph, $pKey, $data);
            return $paragraph;
        }
        throw new \Exception('You dont have enough permission to Update Dataset Relation.');
    }

   

    
    /**
     * Change the relation inside the paragraph
     * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
     * @param int $pKey
     * @param int $relation_target_id
     * @throws \Exception
     */
    public function changeParagraph(\Drupal\paragraphs\Entity\Paragraph &$paragraph, int $pKey, array $data) {
        if (count($paragraph->get('field_dataset')->getValue()) > 0) {
            if (!$this->helper->changeParagraphRelationship($paragraph, $pKey, $data['relation_target_id'], 'field_relation')) {
                throw new \Exception('Paragraph relation field saving error.');
            }
        } else {
            throw new \Exception('This paragraph relation has no Dataset relation.');
        }
    }

}
