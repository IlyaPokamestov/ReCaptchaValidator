<?php
/*
* This file is part of the ReCaptcha Validator Component.
*
* (c) Ilya Pokamestov
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DS\Component\ReCaptchaValidator\Validator;

use DS\Library\ReCaptcha\Http\Driver\DriverInterface;
use DS\Library\ReCaptcha\ReCaptcha;
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
	/** @var  DriverInterface */
	protected $driver;

	/**
	 * @param Request $request
	 * @param string $privateKey
	 * @param DriverInterface $driver
	 */
	public function __construct(Request $request, $privateKey, DriverInterface $driver = null)
	{
		$this->request = $request;
		$this->privateKey = $privateKey;
		$this->driver = $driver;
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
			$reCaptcha = new ReCaptcha($this->privateKey, $this->request->getClientIp(), $this->request->get('g-recaptcha-response', false));
			$response = $reCaptcha->buildRequest($this->driver)->send();
			if(!$response->isSuccess())
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
