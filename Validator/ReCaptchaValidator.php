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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * ReCaptcha Validator.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptchaValidator extends ConstraintValidator
{
	/** @var Request */
	protected $request;
	/** @var  string */
	protected $privateKey;
	/** @var string */

	/**
	 * @param Request $request
	 * @param string $privateKey
	 */
	public function __construct(Request $request, $privateKey)
	{
		$this->request = $request;
		$this->privateKey = $privateKey;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value, Constraint $constraint)
    {
		if(!($constraint instanceof ReCaptchaConstraint))
		{
			throw new InvalidArgumentException('Use ReCaptchaConstraint for ReCaptchaValidator.');
		}

		if($this->request->get('g-recaptcha-response', false))
		{
			$reCaptcha = new \ReCaptcha($this->privateKey);
			$response = $reCaptcha->verifyResponse($this->request->getClientIp(), $this->request->get('g-recaptcha-response', false));
			if(!$response->success)
			{
				$this->context->addViolation($constraint->message);
			}
		}
		else
		{
			$this->context->addViolation($constraint->message);
		}
    }
}
