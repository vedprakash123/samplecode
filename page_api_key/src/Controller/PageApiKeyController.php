<?php
/**
 * @file
 * Contains \Drupal\page_api_key\Controller\PageApiKeyController.
 */
namespace Drupal\page_api_key\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityManager;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

  /**
   * Class PageApiKeyController
   */
class PageApiKeyController extends ControllerBase{
  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Drupal\Core\Entity\EntityManager definition.
   *
   * @var Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Constructs Config Factory object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   Config Factory Service.
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   Entity Manager Service.
   */
  public function __construct(ConfigFactory $config_factory, EntityManager $entity_manager) {
    $this->configFactory = $config_factory;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('config.factory'),
        $container->get('entity.manager')
    );
  }
    /**
     * @param string $api_key
     *    String Contains API Key
     * @param int $nid
     *		An Integer Contains the Node ID
     * @return JsonResponse
     *    An Array Contains JSON Response
     */
    public function contentDetail($api_key, int $nid){
        // Site API Key configuration value
        $config = $this->configFactory->get('siteapikey.configuration');
        $site_api_key = $config->get('siteapikey');

        $node = $this->entityManager->getStorage('node')->load($nid);
        if( (!empty($node) && $node->getType() === 'page' ) && $api_key === $site_api_key ){
            return new JsonResponse($node->toArray(), 200, ['Content-Type'=> 'application/json']);
        }

        // Access Denied Message
        return new JsonResponse(["Error" => "Access Denied"], 401, ['Content-Type'=> 'application/json']);
    }
}
