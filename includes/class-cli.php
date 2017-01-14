<?php

namespace Reseller_Store_Advanced;

use \WP_CLI as WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class CLI extends \WP_CLI_Command {

	/**
	 * Basic description of the custom command
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 */
	public function bar( $args, $assoc_args ) {

		WP_CLI::success( 'Foo bar!' );

	}

}
