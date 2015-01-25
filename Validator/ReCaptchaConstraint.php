<?php
/*
* This file is part of the DSReCaptcha Component.
*
* (c) Ilya Pokamestov
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DS\Component\ReCaptcha\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

/**
 * ReCaptcha Constraint.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptchaConstraint extends Constraint
{
	/** @var Request */
	protected $request;
	/** @var  string */
	protected $privateKey;
	/** @var string */
	public $message = 'ds.recaptcha.invalid';

	/**
	 * @param array $options
	 * @param Request $request
	 * @param string $privateKey
	 */
	public function __construct($options, Request $request, $privateKey)
	{
		parent::__construct($options);
		$this->request = $request;
		$this->privateKey = $privateKey;
	}

	/** @return Request */
	public function getRequest()
	{
		return $this->request;
	}

	/** @return string */
	public function getPrivateKey()
	{
		return $this->privateKey;
	}

	/** @return string */
	public function validatedBy()
	{
		return 'DS\Component\ReCaptcha\Validator\ReCaptchaValidator';
	}
}