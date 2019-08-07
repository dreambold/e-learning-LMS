<?php

namespace OTGS\Toolset\Types\Wordpress\Option;


/**
 * Class AOption
 * @package OTGS\Toolset\Types\Wordpress\Option
 *
 * @since 3.0
 */
abstract class AOption implements IOption {

	/**
	 * get option
	 *
	 * @param bool $default
	 *
	 * @return mixed|void
	 */
	public function getOption( $default = false ) {
		return get_option( $this->getKey(), $default );
	}

	/**
	 * update option
	 *
	 * @param $value
	 * @param bool $autoload
	 */
	public function updateOption( $value, $autoload = true ) {
		update_option( $this->getKey(), $value, $autoload );
	}

	/**
	 * delete option
	 */
	public function deleteOption() {
		delete_option( $this->getKey() );
	}
}