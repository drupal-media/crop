<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\Validation\Constraint\AspectRatioConstraintValidator.
 */

namespace Drupal\crop\Plugin\Validation\Constraint;

use Drupal\crop\Entity\CropType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the AspectRatio constraint.
 */
class AspectRatioConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($aspect_ratio, Constraint $constraint) {
    if (!isset($aspect_ratio)) {
      return;
    }

    if (preg_match(CropType::VALIDATION_REGEXP, $aspect_ratio)) {
      return;
    }

    $this->context->addViolation($constraint->message);
  }

}
