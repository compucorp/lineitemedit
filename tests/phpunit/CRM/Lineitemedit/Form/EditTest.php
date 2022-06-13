<?php

require_once 'BaseTest.php';
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_Lineitemedit_Form_EditTest extends CRM_Lineitemedit_Form_BaseTest {

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp(): void {
    parent::setUp();
  }

  public function tearDown(): void {
    parent::tearDown();
  }

  /**
   * Example: Test that a version is returned.
   */
  public function testLineTotalIncrease(): void {
    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $this->_contribution['contribution_status']);
    $this->assertEquals(100.00, $this->_contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $this->_contributionID));
    $lineItemInfo['qty'] += 2; // increase lineitem qty to 3
    $lineItemInfo['line_total'] *= $lineItemInfo['qty'];
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    // Contribution amount and status after LineItem edit
    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $this->_contributionID));
    $this->assertEquals('Partially paid', $contribution['contribution_status']);
    $this->assertEquals(300.00, $contribution['total_amount']);

    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');
    $actualFinancialItemEntries = $this->getFinancialItemsByContributionID($this->_contributionID);
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
      ),
      array(
        'contact_id' => $this->_contactID,
        'description' => '3.00 of Contribution Amount',
        'amount' => 200.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Unpaid'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);

    $actualFinancialTrxnEntries = $this->getFinancialTrxnsByContributionID($this->_contributionID);
    $expectedFinancialTrxnEntries = array(
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => 200.00,
        'net_amount' => 200.00,
        'is_payment' => 0,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Partially paid'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialTrxnEntries, $actualFinancialTrxnEntries);
  }

  public function testChangeRecurContributionAmount(): void {
    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $this->_contribution['contribution_status']);
    $this->assertEquals(100.00, $this->_contribution['total_amount']);
    $recurObject = civicrm_api3('ContributionRecur', 'create', [
      'amount' => $this->_contribution['total_amount'],
      'contact_id' => $this->_contribution['contact_id'],
      'frequency_interval' => 1,
      'status' => 'Pending',
      'currency' => $this->_contribution['currency'],
      'trxn_id' => '23456',
    ]);
    civicrm_api3('Contribution', 'create', [
      'contribution_recur_id' => $recurObject['id'],
      'id' => $this->_contribution['id'],
    ]);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $this->_contributionID));
    $lineItemInfo['qty'] += 1; // increase lineitem qty to 3
    $lineItemInfo['line_total'] *= $lineItemInfo['qty'];
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    // Contribution amount and status after LineItem edit
    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $this->_contributionID));
    $this->assertEquals('Partially paid', $contribution['contribution_status']);
    $this->assertEquals(200.00, $contribution['total_amount']);

    $updateRecurObject = civicrm_api3('ContributionRecur', 'getsingle', ['id' => $recurObject['id']]);
    $this->assertEquals(200.00, $updateRecurObject['amount']);

    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');
    $actualFinancialItemEntries = $this->getFinancialItemsByContributionID($this->_contributionID);
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
      ),
      array(
        'contact_id' => $this->_contactID,
        'description' => '2.00 of Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Unpaid'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);

    civicrm_api3('Contribution', 'create', [
      'id' => $this->_contribution['id'],
      'contribution_recur_id' => 'null',
    ]);
    civicrm_api3('ContributionRecur', 'delete', ['id' => $recurObject['id']]);
  }

  public function testWithoutPriceFieldID(): void {
    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $this->_contribution['contribution_status']);
    $this->assertEquals(100.00, $this->_contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $this->_contributionID));
    $this->callAPISuccess('LineItem', 'create', array('id' => $lineItemInfo['id'], 'price_field_id' => '', 'price_field_value_id' => ''));

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $this->_contributionID));
    $lineItemInfo['qty'] += 1; // increase lineitem qty to 2
    $lineItemInfo['line_total'] *= $lineItemInfo['qty'];
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    // Contribution amount and status after LineItem edit
    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $this->_contributionID));
    $this->assertEquals('Partially paid', $contribution['contribution_status']);
    $this->assertEquals(200.00, $contribution['total_amount']);

    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');
    $actualFinancialItemEntries = $this->getFinancialItemsByContributionID($this->_contributionID);
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
      ),
      array(
        'contact_id' => $this->_contactID,
        'description' => '2.00 of Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Unpaid'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);
  }

  /**
   * Example: Test that a version is returned.
   */
  public function testLineTotalDecrease(): void {
    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $this->_contribution['contribution_status']);
    $this->assertEquals(100.00, $this->_contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array(
      'contribution_id' => $this->_contributionID,
    ));
    $lineItemInfo['line_total'] = $lineItemInfo['unit_price'] -= 50;
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    // Contribution amount and status after LineItem edit
    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $this->_contributionID));
    $this->assertEquals('Pending refund', $contribution['contribution_status']);
    $this->assertEquals(50.00, $contribution['total_amount']);
  }

  public function testFinancialTypeChange() {
    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');

    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $this->_contribution['contribution_status']);
    $this->assertEquals(100.00, $this->_contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array(
      'contribution_id' => $this->_contributionID,
    ));
    $lineItemInfo['financial_type_id'] = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'financial_type_id', 'Campaign Contribution');
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $this->_contributionID));
    $this->assertEquals($lineItemInfo['financial_type_id'], $contribution['financial_type_id']);

    $actualFinancialItemEntries = $this->getFinancialItemsByContributionID($this->_contributionID);
    $prevFinancialAccountID = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount($this->_contribution['financial_type_id'], 'Income Account is');
    $newFinancialAccountID = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount($lineItemInfo['financial_type_id'], 'Income Account is');
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $prevFinancialAccountID,
      ),
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => -100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $prevFinancialAccountID,
      ),
      array(
        'contact_id' => $this->_contactID,
        'description' => 'Contribution Amount',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $newFinancialAccountID,
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);

    $actualFinancialTrxnEntries = $this->getFinancialTrxnsByContributionID($this->_contributionID);
    $expectedFinancialTrxnEntries = array(
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => -100.00,
        'net_amount' => -100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialTrxnEntries, $actualFinancialTrxnEntries);
  }

  public function testFinancialTypeChangeWithPriceSet(): void {
    $priceFieldValues = $this->createPriceSet();
    $priceFieldID = key($priceFieldValues);
    $contactID = $this->createDummyContact();
    $params = array(
      'total_amount' => 100,
      'financial_type_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'financial_type_id', 'Donation'),
      'receive_date' => '2015-04-21 23:27:00',
      'contact_id' => $contactID,
      'price_set_id' => $this->_priceSetID,
      'contribution_status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      'price_' . $priceFieldID => array($priceFieldValues[$priceFieldID][0] => 1),
    );
    $_REQUEST = [];
    $form = $this->getContributionForm($params);
    $form->_priceSet = current(CRM_Price_BAO_PriceSet::getSetDetail($this->_priceSetID));
    $form->postProcess();

    $contribution = $this->callAPISuccessGetSingle('Contribution', array('contact_id' => $contactID));

    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $contribution['contribution_status']);
    $this->assertEquals(100.00, $contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $contribution['id']));
    $lineItemInfo['financial_type_id'] = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'financial_type_id', 'Campaign Contribution');
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    $actualFinancialItemEntries = $this->getFinancialItemsByLineItemID($lineItemInfo['id']);
    $prevFinancialAccountID = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount($this->_contribution['financial_type_id'], 'Income Account is');
    $newFinancialAccountID = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount($lineItemInfo['financial_type_id'], 'Income Account is');
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $contactID,
        'description' => 'Price Field 1',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $prevFinancialAccountID,
      ),
      array(
        'contact_id' => $contactID,
        'description' => 'Price Field 1',
        'amount' => -100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $prevFinancialAccountID,
      ),
      array(
        'contact_id' => $contactID,
        'description' => 'Price Field 1',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
        'financial_account_id' => $newFinancialAccountID,
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);

    $actualFinancialTrxnEntries = $this->getFinancialTrxnsByContributionID($contribution['id']);
    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');
    $expectedFinancialTrxnEntries = array(
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => -100.00,
        'net_amount' => -100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialTrxnEntries, $actualFinancialTrxnEntries);
  }


  public function testLineTotalChangeWithPriceSet(): void {
    $priceFieldValues = $this->createPriceSet();
    $priceFieldID = key($priceFieldValues);
    $contactID = $this->createDummyContact();
    $params = array(
      'total_amount' => 100,
      'financial_type_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'financial_type_id', 'Donation'),
      'receive_date' => '2015-04-21 23:27:00',
      'contact_id' => $contactID,
      'price_set_id' => $this->_priceSetID,
      'contribution_status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      'price_' . $priceFieldID => array($priceFieldValues[$priceFieldID][0] => 1),
    );
    $_REQUEST = [];
    $form = $this->getContributionForm($params);
    $form->_priceSet = current(CRM_Price_BAO_PriceSet::getSetDetail($this->_priceSetID));
    $form->postProcess();

    $contribution = $this->callAPISuccessGetSingle('Contribution', array('contact_id' => $contactID));

    // Contribution amount and status before LineItem edit
    $this->assertEquals('Completed', $contribution['contribution_status']);
    $this->assertEquals(100.00, $contribution['total_amount']);

    $lineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('contribution_id' => $contribution['id']));
    $lineItemInfo['line_total'] = $lineItemInfo['unit_price'] += 100;
    $lineItemInfo['qty'] += 1;
    $_REQUEST['id'] = $lineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $lineItemInfo);
    $form->buildForm();
    $form->postProcess();

    // Contribution amount and status after LineItem edit
    $contribution = $this->callAPISuccessGetSingle('Contribution', array('id' => $contribution['id']));
    $this->assertEquals('Partially paid', $contribution['contribution_status']);
    $this->assertEquals(200.00, $contribution['total_amount']);

    $check = CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'payment_instrument_id', 'Check');
    $actualFinancialItemEntries = $this->getFinancialItemsByLineItemID($lineItemInfo['id']);
    $expectedFinancialItemEntries = array(
      array(
        'contact_id' => $contactID,
        'description' => 'Price Field 1',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Paid'),
      ),
      array(
        'contact_id' => $contactID,
        'description' => '2.00 of Price Field 1',
        'amount' => 100.00,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Financial_DAO_FinancialItem', 'status_id', 'Unpaid'),
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedFinancialItemEntries, $actualFinancialItemEntries);

    $actualFinancialTrxnEntries = $this->getFinancialTrxnsByContributionID($contribution['id']);
    $expectedFinancialTrxnEntries = array(
      array(
        'total_amount' => 100.00,
        'net_amount' => 100.00,
        'is_payment' => 1,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed'),
      ),
      array(
        'total_amount' => 200.00,
        'net_amount' => 100.00,
        'is_payment' => 0,
        'payment_instrument_id' => $check,
        'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Partially paid'),
      ),
    );
  }

  public function testParticipantRecordOnLineItemEdit(): void {
    $_REQUEST = [];
    $event = $this->eventCreate();
    $form = $this->getFormObject('CRM_Event_Form_Participant');
    $form->_single = TRUE;
    $form->_contactID = $form->_contactId = $this->_contactID;
    $form->_priceSetId = $this->_priceSetID;
    $form->_contactIds = [];
    $form->_eventId = $event['id'];
    $form->_bltID = 5;
    $form->_values['fee'] = $this->_eventFeeBlock;
    $form->_isPaidEvent = TRUE;
    $form->setCustomDataTypes();

    $form->submit(array(
      'register_date' => date('Y-m-d H:i:s'),
      'receive_date' => date('Y-m-d H:i:s'),
      'status_id' => 1,
      'role_id' => 1,
      'event_id' => $form->_eventId,
      'amount_level' => 'Price Field 1',
      'fee_amount' => 100,
      'total_amount' => 100,
      'priceSetId' => $this->_priceSetID,
      'price_' . key($form->_values['fee']) => array(
        key($form->_values['fee'][key($form->_values['fee'])]['options']) => 1,
      ),
      'record_contribution' => TRUE,
      'financial_type_id' => 1,
      'contribution_status_id' => 1,
      'payment_instrument_id' => 1,
    ));

    $participantID = $this->callAPISuccess('Participant', 'get', [])['id'];

    $updatedlineItemInfo = $this->callAPISuccessGetSingle('LineItem', array('entity_id' => $participantID, 'entity_table' => 'civicrm_participant'));
    $updatedlineItemInfo['line_total'] = $updatedlineItemInfo['unit_price'] += 100;
    $updatedlineItemInfo['qty'] += 1;
    $_REQUEST['id'] = $updatedlineItemInfo['id'];
    $form = $this->getFormObject('CRM_Lineitemedit_Form_Edit', $updatedlineItemInfo);
    $form->preProcess();
    $form->buildForm();
    $form->postProcess();

    $actualParticipantRecord = $this->callAPISuccess('Participant', 'get', ['id' => $participantID, 'sequential' => 1])['values'];
    $expectedParticipantRecord = array(
      array(
        'event_id' => $event['id'],
        'participant_fee_amount' => $updatedlineItemInfo['line_total'],
        'participant_fee_level' => 'Price Field 1 - ' . $updatedlineItemInfo['qty'],
        'contact_id' => $this->_contactID,
      ),
    );
    $this->checkArrayEqualsByAttributes($expectedParticipantRecord, $actualParticipantRecord);
  }

}
