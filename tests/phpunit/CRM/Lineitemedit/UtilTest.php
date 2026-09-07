<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;

/**
 * Tests the decimal place restriction applied to line item qty and unit price.
 *
 * @group headless
 */
class CRM_Lineitemedit_UtilTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, TransactionalInterface {

  /**
   * Separator settings to restore after tests that change them.
   *
   * @var array
   */
  private $originalSeparators = [];

  /**
   * {@inheritDoc}
   */
  public function setUpHeadless() {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * {@inheritDoc}
   */
  public function setUp(): void {
    parent::setUp();
    $config = CRM_Core_Config::singleton();
    $this->originalSeparators = [
      'thousand' => $config->monetaryThousandSeparator,
      'decimal' => $config->monetaryDecimalPoint,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function tearDown(): void {
    $config = CRM_Core_Config::singleton();
    $config->monetaryThousandSeparator = $this->originalSeparators['thousand'];
    $config->monetaryDecimalPoint = $this->originalSeparators['decimal'];
    parent::tearDown();
  }

  /**
   * Values that should and should not be rejected.
   *
   * @return array
   */
  public function decimalPlacesProvider(): array {
    return [
      'two decimal places' => ['1.23', FALSE],
      'one decimal place' => ['1.2', FALSE],
      'trailing zeroes' => ['2.00', FALSE],
      'no decimal point' => ['10', FALSE],
      'decimal point with nothing after it' => ['1.', FALSE],
      'thousand separator, two decimal places' => ['1,234.56', FALSE],
      'float with two decimal places' => [1.5, FALSE],
      'empty string' => ['', FALSE],
      'null' => [NULL, FALSE],
      'array is not a value we can judge' => [[1], FALSE],
      'three decimal places' => ['1.234', TRUE],
      'four decimal places' => ['0.8571', TRUE],
      'three decimal places under one' => ['0.001', TRUE],
      'negative with three decimal places' => ['-2.505', TRUE],
      'thousand separator, three decimal places' => ['1,234.567', TRUE],
      'float with three decimal places' => [1.005, TRUE],
    ];
  }

  /**
   * Tests that only values beyond two decimal places are rejected.
   *
   * @param mixed $value
   * @param bool $expected
   *
   * @dataProvider decimalPlacesProvider
   */
  public function testExceedsAllowedDecimalPlaces($value, bool $expected): void {
    $this->assertSame(
      $expected,
      CRM_Lineitemedit_Util::exceedsAllowedDecimalPlaces($value),
      sprintf('Unexpected result for %s', var_export($value, TRUE))
    );
  }

  /**
   * Tests the localised decimal separator is respected.
   */
  public function testDecimalPlacesRespectLocalisedSeparators(): void {
    $config = CRM_Core_Config::singleton();
    $config->monetaryThousandSeparator = '.';
    $config->monetaryDecimalPoint = ',';

    $this->assertFalse(CRM_Lineitemedit_Util::exceedsAllowedDecimalPlaces('1.234'));
    $this->assertFalse(CRM_Lineitemedit_Util::exceedsAllowedDecimalPlaces('1,23'));
    $this->assertTrue(CRM_Lineitemedit_Util::exceedsAllowedDecimalPlaces('1,234'));
    $this->assertTrue(CRM_Lineitemedit_Util::exceedsAllowedDecimalPlaces('1.234,567'));
  }

  /**
   * Tests the QuickForm rule callback reports validity, not invalidity.
   *
   * @param mixed $value
   * @param bool $exceeds
   *
   * @dataProvider decimalPlacesProvider
   */
  public function testDecimalPlacesWithinLimit($value, bool $exceeds): void {
    $this->assertSame(
      !$exceeds,
      CRM_Lineitemedit_Util::decimalPlacesWithinLimit($value),
      sprintf('Unexpected rule result for %s', var_export($value, TRUE))
    );
  }

}
