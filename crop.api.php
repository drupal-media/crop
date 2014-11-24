<?php

/**
 * @file
 * Hooks related to crop API.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the information provided in \Drupal\crop\Annotation\CropEntityProvider.
 *
 * @param $providers
 *   The array of provider plugins, keyed on the machine-readable name.
 */
function hook_crop_entity_provider_info_alter(&$providers) {
  $providers['media']['label'] = t('Super fancy provider for media entity!');
}

/**
 * @} End of "addtogroup hooks".
 */
