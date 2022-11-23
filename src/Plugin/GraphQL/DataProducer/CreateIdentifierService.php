<?php

namespace Drupal\rescat_graphql\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new IdentifierService entity.
 *
 * @DataProducer(
 *   id = "create_identifier_service",
 *   name = @Translation("Create IdentifierService"),
 *   description = @Translation("Creates a new IdentifierService."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("IdentifierService")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("IdentifierService data")
 *     )
 *   }
 * )
 */
class CreateIdentifierService extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

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
     * Create IdentifierService constructor.
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
     * Creates an IdentifierService.
     *
     * @param array $data
     *   The title of the job.
     *
     * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
     *   The newly created IdentifierService.
     *
     * @throws \Exception
     */
    public function resolve(array $data) {

        $userRoles = $this->currentUser->getRoles();
        if (in_array('authenticated', $userRoles)) {

            $term = Term::create([
                        'vid' => 'identifier_services',
                        'name' => $data['name'],
                        'type' => 'taxonomy_term',
            ]);
            try {
                $term->save();
            } catch (\Exception $ex) {
                throw new \Exception('Error during the identifier service save!'. $ex->getMessage());
            }
            
            return array("id" => $term->id(), "name" => $data["name"]);
        }
        throw new \Exception('You dont have enough permission to create a identifier service.');
    }

}
