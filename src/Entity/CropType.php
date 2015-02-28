<?php

/**
 * @file
 * Contains \Drupal\crop\Entity\CropType.
 */

namespace Drupal\crop\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\crop\CropTypeInterface;

/**
 * Defines the Crop type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "crop_type",
 *   label = @Translation("Crop type"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\crop\Form\CropTypeForm",
 *       "edit" = "Drupal\crop\Form\CropTypeForm",
 *       "delete" = "Drupal\crop\Form\CropTypeDeleteForm"
 *     },
 *     "list_builder" = "Drupal\crop\CropTypeListBuilder",
 *   },
 *   admin_permission = "administer crop types",
 *   config_prefix = "type",
 *   bundle_of = "crop",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/crop/manage/{crop_type}",
 *     "delete-form" = "/admin/structure/crop/manage/{crop_type}/delete",
 *   }
 * )
 */
class CropType extends ConfigEntityBundleBase implements CropTypeInterface {

  /**
   * The machine name of this crop type.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the crop type.
   *
   * @var string
   */
  public $label;

  /**
   * A brief description of this crop type.
   *
   * @var string
   */
  public $description;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

}
