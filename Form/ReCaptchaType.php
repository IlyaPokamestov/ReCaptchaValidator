<?php
/*
* This file is part of the DSReCaptcha Component.
*
* (c) Ilya Pokamestov
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DS\Component\ReCaptcha\Form;

use DS\Component\ReCaptcha\Validator\ReCaptchaConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type for reCaptcha.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptchaType extends AbstractType
{
	const JS_API_URL = 'https://www.google.com/recaptcha/api.js';

	/** @var  string */
    protected $publicKey;
	/** @var  string */
    protected $privateKey;
	/** @var  string */
	protected $locale;
	/** @var  Request */
	protected $request;

	public function __construct(RequestStack $requestStack, $publicKey, $privateKey, $locale = null)
	{
		if (empty($publicKey) || empty($privateKey)) {
            throw new InvalidConfigurationException('The parameters "ds_recaptcha_public_key" and "ds_recaptcha_private_key" must be configured.');
        }

		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->request = $requestStack->getCurrentRequest();

		if(null !== $locale)
		{
			$this->locale = $locale;
		}
		else
		{
			$this->locale = Locale::getDefault();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'public_key' => $this->publicKey,
			'lang' => $this->locale,
			'js_api_url' => self::JS_API_URL,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$resolver->setDefaults(array(
			'constraints' => array(new ReCaptchaConstraint(null, $this->request, $this->privateKey),),
		));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ds_recaptcha';
    }
}
