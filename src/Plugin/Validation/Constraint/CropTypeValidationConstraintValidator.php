<?php

/**
 * @file
 * Contains \Drupal\crop\Plugin\Validation\Constraint\CropTypeValidationConstraintValidator.
 */

namespace Drupal\crop\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Checks if the crop type is valid.
 */
class CropTypeValidationConstraintValidator extends ConstraintValidator {

  /**
   * Validator 2.5 and upwards compatible execution context.
   *
   * @var \Symfony\Component\Validator\Context\ExecutionContextInterface
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\crop\Entity\CropType $value */
    $id = trim($value->id());
    // '0' is invalid, since elsewhere we check it using empty().
    if ($id == '0') {
      $this->context->buildViolation('Invalid machine-readable name. Enter a name other than "0"')
        ->atPath('id')
        ->addViolation();
    }

    $aspect_ratio = $value->getAspectRatio();
    if (!empty($aspect_ratio) && !preg_match($value::VALIDATION_REGEXP, $aspect_ratio)) {
      $this->context->buildViolation('Invalid format of aspect ratio. Enter a ratio in format H:W')
        ->atPath('aspect_ratio')
        ->addViolation();
    }
  }

}
