Simple Google reCAPTCHA FormType and Validator Component for Symfony2 applications
================================================

Really light and simple reCAPTCHA component for Symfony Frameworks,
it's not a Bundle, you can reconfigure all components whatever you like.

You can find full documentation about Google reCAPTCHA API v2 [here](http://developers.google.com/recaptcha/intro).

Installation
------------

You can install this package with [Composer](http://getcomposer.org/).
Add next lines to your composer.json file:

``` json
{
    "require": {
        "dario_swain/ds-recaptcha":                 "~1.0"
    }
}
```

To use this package with php version ~ 5.3 use version ~ 1.0

Usage Example
-------------

Add public and private keys, and configure reCAPTCHA Form Type like a service,
provide next configuration to application bundle service configuration (service.yml):

``` yaml
parameters:
    ds_recaptcha_public_key:  #YOUR_PUBLIC_KEY#
    ds_recaptcha_private_key: #YOUR_PRIVATE_KEY#

services:
    ds.form.type.recaptcha:
            class: DS\Component\ReCaptcha\Form\ReCaptchaType
            arguments: ['@request_stack', %ds_recaptcha_public_key%, %ds_recaptcha_private_key%, %locale%]
            tags:
                - { name: form.type, alias: ds_recaptcha }
```

After this you can add reCAPTCHA type to your custom form:

``` php
<?php

namespace AcmeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'textarea')
            /** In type add your form alias **/
			->add('captcha', 'ds_recaptcha', array('mapped' => false))
			->add('send', 'submit');
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
		    /** This option is require, because reCaptcha api.js add extra field "g-recaptcha-response" to form **/
			'allow_extra_fields' => true,
		));
	}
}

```

Next step, you need to add form_theme to your form view, it seems like that:

```twig
{% extends 'AcmeBundle::layout.html.twig' %}
{% form_theme form 'DS/ReCaptcha/views/form_div_layout.html.twig' %}
{% block content %}
    {{ form_start(form) }}
    {{ form_widget(form) }}
    {{ form_end(form) }}
{% endblock %}
```

If you need to customize from widget, feel free to change template in %kernel_root%/Resources/DS/ReCaptcha/views/form_div_layout.twig

Copyright
---------

Copyright (c) 2015 Ilya Pokamestov <dario_swain@yahoo.com>.
