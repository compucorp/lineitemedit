<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;

/**
 * Checks that the line item forms require the same permissions as the
 * edit and cancel actions shown on the contribution form.
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
   * Line item form paths with the permissions each one requires.
   *
   * @return array
   */
  public function lineItemFormProvider(): array {
    return [
      'edit form' => ['civicrm/lineitem/edit', ['access CiviCRM', 'edit contributions', 'edit line item']],
      'cancel form' => ['civicrm/lineitem/cancel', ['access CiviCRM', 'delete in CiviContribute', 'cancel line item']],
    ];
  }

  /**
   * @dataProvider lineItemFormProvider
   */
  public function testFormIsAllowedWithAllRequiredPermissions(string $path, array $requiredPermissions): void {
    $this->setPermissions($requiredPermissions);

    $this->assertTrue($this->canAccess($path));
  }

  /**
   * @dataProvider lineItemFormProvider
   */
  public function testFormIsDeniedWhenAnyRequiredPermissionIsMissing(string $path, array $requiredPermissions): void {
    foreach ($requiredPermissions as $missingPermission) {
      $this->setPermissions(array_values(array_diff($requiredPermissions, [$missingPermission])));

      $this->assertFalse($this->canAccess($path), "Access to {$path} should be denied without '{$missingPermission}'.");
    }
  }

  /**
   * The edit/cancel buttons in the line item SearchKit displays should be
   * shown only to users who can open the form they link to.
   *
   * @dataProvider lineItemFormProvider
   */
  public function testSearchDisplayLinksRequireTheSamePermissionsAsTheForm(string $path, array $requiredPermissions): void {
    $links = $this->getSearchDisplayLinks($path);
    // One link in each of the two line item displays.
    $this->assertCount(2, $links);

    $expected = array_values(array_diff($requiredPermissions, ['access CiviCRM']));
    sort($expected);
    foreach ($links as $link) {
      $linkPermissions = [];
      foreach ($link['conditions'] ?? [] as $condition) {
        if (($condition[0] ?? NULL) === 'check user permission' && ($condition[1] ?? NULL) === '=') {
          $linkPermissions = array_merge($linkPermissions, (array) $condition[2]);
        }
      }
      sort($linkPermissions);

      $this->assertEquals($expected, $linkPermissions);
    }
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

  /**
   * Finds the links to a path in this extension's managed search displays.
   */
  private function getSearchDisplayLinks(string $path): array {
    $managed = include dirname(__DIR__, 4) . '/managed/LineItemsSavedSearch.mgd.php';

    $links = [];
    $this->collectLinks($managed, $path, $links);

    return $links;
  }

  private function collectLinks(array $data, string $path, array &$links): void {
    if (isset($data['path']) && is_string($data['path']) && strpos($data['path'], $path . '?') === 0) {
      $links[] = $data;
      return;
    }
    foreach ($data as $value) {
      if (is_array($value)) {
        $this->collectLinks($value, $path, $links);
      }
    }
  }

}
