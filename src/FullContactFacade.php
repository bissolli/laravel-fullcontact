<?php

namespace Bissolli\FullContact;

use Illuminate\Support\Facades\Facade;

/**
 * This class provides a Facade for the FullContactServiceProvider
 *
 * @package  Services\FullContact
 */
class FullContactFacade extends Facade {

	/**
	 * {@inheritDocs}
	 */
	protected static function getFacadeAccessor() { return 'fullcontact'; }

}
