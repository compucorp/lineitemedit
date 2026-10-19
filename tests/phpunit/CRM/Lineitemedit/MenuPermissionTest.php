<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;

/**
 * Checks that the line item forms require the line item permissions.
 *
 * @group headless
 */
class CRM_Lineitemedit_MenuPermissionTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, TransactionalInterface {

  public function setUpHeadless() {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function tearDown(): void {
    CRM_Core_Config::singleton()->userPermissionClass->permissions = NULL;
    parent::tearDown();
  }

  /**
   * @return array
   */
  public function lineItemFormProvider(): array {
    return [
      'edit form' => ['civicrm/lineitem/edit', 'edit line item'],
      'cancel form' => ['civicrm/lineitem/cancel', 'cancel line item'],
    ];
  }

  /**
   * @dataProvider lineItemFormProvider
   */
  public function testFormIsDeniedWithoutLineItemPermission(string $path, string $permission): void {
    $this->setPermissions(['access CiviCRM']);

    $this->assertFalse($this->canAccess($path));
  }

  /**
   * @dataProvider lineItemFormProvider
   */
  public function testFormIsAllowedWithLineItemPermission(string $path, string $permission): void {
    $this->setPermissions(['access CiviCRM', $permission]);

    $this->assertTrue($this->canAccess($path));
  }

  /**
   * @dataProvider lineItemFormProvider
   */
  public function testFormIsDeniedWithoutAccessCiviCrm(string $path, string $permission): void {
    $this->setPermissions([$permission]);

    $this->assertFalse($this->canAccess($path));
  }

  private function setPermissions(array $permissions): void {
    CRM_Core_Config::singleton()->userPermissionClass->permissions = $permissions;
  }

  /**
   * Checks access to a path as defined in this extension's menu XML.
   */
  private function canAccess(string $path): bool {
    $items = [];
    CRM_Core_Menu::read(dirname(__DIR__, 4) . '/xml/Menu/lineitemedit.xml', $items);
    $this->assertArrayHasKey($path, $items);

    [$permissions, $operator] = $items[$path]['access_arguments'];

    return CRM_Core_Permission::checkMenu($permissions, $operator);
  }

}
