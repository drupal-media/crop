<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\ImageEffect\CropEffect.
 */

namespace Drupal\crop\Plugin\ImageEffect;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\crop\CropStorageInterface;
use Drupal\image\Plugin\ImageEffect\CropImageEffect;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Crops an image resource.
 *
 * @ImageEffect(
 *   id = "crop_crop",
 *   label = @Translation("User crop"),
 *   description = @Translation("Applies user provided crop to the image.")
 * )
 */
class CropEffect extends CropImageEffect implements ContainerFactoryPluginInterface {

  /**
   * Crop entity storage.
   *
   * @var \Drupal\crop\CropStorageInterface
   */
  protected $storage;

  /**
   * Crop entity query.
   *
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $query;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LoggerInterface $logger, CropStorageInterface $crop_storage, QueryInterface $storage_query) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
    $this->storage = $crop_storage;
    $this->query = $storage_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory')->get('image'),
      $container->get('entity.manager')->getStorage('crop'),
      $container->get('entity.query')->get('crop')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    $ids = $this->query
      ->condition('uri', $image->getSource())
      ->sort('cid')
      ->range(0, 1)
      ->execute();
    /** @var \Drupal\crop\CropInterface $crop */
    $crop = $this->storage->load(current($ids));

    if ($crop) {
      $position = $crop->position();
      $size = $crop->size();

      // Crop effect expects x,y to represent top left corner. In our case it
      // represents center of crop area so we need to transform.
      $x = $position['x'] - $size['width'] / 2;
      $y = $position['y'] - $size['height'] / 2;

      if (!$image->crop($x, $y, $size['width'], $size['height'])) {
        $this->logger->error('Image crop failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', array(
            '%toolkit' => $image->getToolkitId(),
            '%path' => $image->getSource(),
            '%mimetype' => $image->getMimeType(),
            '%dimensions' => $image->getWidth() . 'x' . $image->getHeight()
          ));
        return FALSE;
      }
    }
    elseif ($this->configuration['automatic_crop']) {
      return parent::applyEffect($image);
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = array(
      '#theme' => 'crop_crop_summary',
      '#data' => $this->configuration,
    );
    $summary += parent::getSummary();

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'automatic_crop' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['fallback'] = [
      '#type' => 'fieldset',
      '#title' => t('No user crop behaviour'),
      '#tree' => FALSE,
    ];

    $form['fallback']['automatic_crop'] = array(
      '#type' => 'checkbox',
      '#title' => t('Crop automatically'),
      '#default_value' => $this->configuration['automatic_crop'],
      '#description' => t("Crop automatically if no user crop provided."),
      '#parents' => ['data', 'automatic_crop'],
    );

    $form['fallback']['anchor'] = $form['anchor'];
    $form['fallback']['anchor']['#parents'] = ['data', 'anchor'];
    $form['fallback']['anchor']['#states'] = [
      'visible' => array(
        ':input[name="data[automatic_crop]"]' => array('checked' => TRUE),
      ),
    ];
    unset($form['anchor']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['automatic_crop'] = $form_state->getValue('automatic_crop');
  }

}
