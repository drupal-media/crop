<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\Validation\Constraint\CropTypeValidationConstraint.
 */

namespace Drupal\crop\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validation CropType constraint.
 *
 * @Constraint(
 *   id = "CropTypeValidation",
 *   label = @Translation("Crop Type Validation", context = "Validation")
 * )
 */
class CropTypeValidationConstraint extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = 'Validation Error for the crop type.';

}