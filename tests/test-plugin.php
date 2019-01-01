<?php
/**
 * GoDaddy Reseller Store Settingstest basics
 */

namespace Reseller_Store_Settings;

final class TestPlugin extends TestCase {

	/**
	 * Setup.
	 */
	function setUp() {

		parent::setUp();

	}

	/**
	 * Test that Plugin exists.
	 */
	public function test_basics() {

		$this->assertTrue( class_exists( __NAMESPACE__ . '\Plugin' ) );

	}

}
