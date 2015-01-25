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

use GuzzleHttp\Client;
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
	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s';

	/**
	 * {@inheritdoc}
	 */
	public function validate($value, Constraint $constraint)
    {
		if(!($constraint instanceof ReCaptchaConstraint))
		{
			throw new InvalidArgumentException('Use ReCaptchaConstraint for ReCaptchaValidator.');
		}

		/** @var Request $request */
		$request = $constraint->getRequest();

		if($request->get('g-recaptcha-response', false))
		{
			$client = new Client();
			$response =  $client->get(sprintf(self::SITE_VERIFY_URL,$constraint->getPrivateKey(),$request->get('g-recaptcha-response', ''),$request->getClientIp()));
			$jsonResponse = $response->json();
			if(!(isset($jsonResponse['success']) && $jsonResponse['success']))
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
