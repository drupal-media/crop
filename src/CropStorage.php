<?php

/**
 * @file
 * Contains of \Drupal\crop\CropStorage.
 */

namespace Drupal\crop;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Image crop storage class.
 */
class ImageCropStorage extends SqlContentEntityStorage implements CropStorageInterface {

}
