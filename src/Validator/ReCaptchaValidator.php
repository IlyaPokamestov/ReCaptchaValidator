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
use Symfony\Component\HttpFoundation\RequestStack;
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
    /** @var bool */
    protected $enabled;

    /**
     * @param Request|RequestStack $request
     * @param string $privateKey
     * @param DriverInterface $driver
     * @param bool $enabled
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($request, $privateKey, DriverInterface $driver = null, $enabled = true)
    {
        // Typehint the $request argument for RequestStack when dropping support for Symfony 2.3
        if ($request instanceof Request) {
            $this->request = $request;
        } elseif ($request instanceof RequestStack) {
            $this->request = $request->getCurrentRequest();
        } else {
            throw new \InvalidArgumentException(
                'Argument 1 should be an instance of Symfony\Component\HttpFoundation\Request or '
                .'Symfony\Component\HttpFoundation\RequestStack'
            );
        }

        $this->privateKey = $privateKey;
        $this->driver = $driver;
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof ReCaptchaConstraint)) {
            throw new InvalidArgumentException('Use ReCaptchaConstraint for ReCaptchaValidator.');
        }

        if (false === $this->enabled || false === $constraint->enabled) {
            return;
        }

        if ($this->request->get('g-recaptcha-response', false)) {
            $reCaptcha = new ReCaptcha(
                $this->privateKey,
                $this->request->getClientIp(),
                $this->request->get('g-recaptcha-response', false)
            );
            $response = $reCaptcha->buildRequest($this->driver)->send();
            if (!$response->isSuccess()) {
                $this->context->addViolation($constraint->message);
            }
        } else {
            $this->context->addViolation($constraint->message);
        }
    }
}
