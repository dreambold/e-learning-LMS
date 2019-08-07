<?php

namespace OTGS\Toolset\Types\Wordpress\Option;


/**
 * Interface IOption
 *
 * Add rules to an option.
 *
 * @package OTGS\Toolset\Types\Wordpress\Option
 *
 * @since 3.0
 */
interface IOption {
	/**
	 * Returns the option key
	 * @return string
	 */
	public function getKey();

	/**
	 * returns the option value
	 * @return mixed
	 */
	public function getOption();

	/**
	 * updates the option
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function updateOption( $value );

	/**
	 * deletes the option
	 *
	 * @return mixed
	 */
	public function deleteOption();
}