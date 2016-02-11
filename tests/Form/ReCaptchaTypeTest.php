<?php
/*
* This file is part of the ReCaptcha Validator Component.
*
* (c) Ilya Pokamestov
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DS\Component\ReCaptchaValidator\Tests\Form\ReCaptchaType;

use DS\Component\ReCaptchaValidator\Form\ReCaptchaType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ReCaptcha Validator.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptchaTypeTest extends \PHPUnit_Framework_TestCase {

    private $dispatcher;
    private $factory;
    private $builder;

    protected function setUp()
    {
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $this->builder = new FormBuilder('name', null, $this->dispatcher, $this->factory);
    }

    protected function tearDown()
    {
        $this->dispatcher = null;
        $this->factory = null;
        $this->builder = null;
    }

    public function testName()
    {
        $captchaType = new ReCaptchaType('test');
        $this->assertEquals($captchaType->getName(), 'ds_re_captcha');
    }

    public function testViewConfiguration()
    {
        $captchaType = new ReCaptchaType('test', 'test_loc');
        $view = new FormView();
        $captchaType->buildView($view, $this->builder->getForm(), array());
        $this->assertEquals($view->vars['public_key'], 'test');
        $this->assertEquals($view->vars['lang'], 'test_loc');
        $this->assertEquals($view->vars['js_api_url'], 'https://www.google.com/recaptcha/api.js');
    }

    public function testWithoutPublicKey()
    {
        $this->setExpectedException('Symfony\Component\Form\Exception\InvalidConfigurationException');
        new ReCaptchaType(null);
    }

    public function testDefaults()
    {
        $captchaType = new ReCaptchaType('test', 'test_loc');
        $resolver = new OptionsResolver();
        $this->assertEquals(array(), $resolver->getDefinedOptions());

        // Prefer Symfony 2.7 API
        if (method_exists($captchaType, 'configureOptions')) {
            $captchaType->configureOptions($resolver);
        } else {
            $captchaType->setDefaultOptions($resolver);
        }

        $this->assertArraySubset(array('constraints'), $resolver->getDefinedOptions());
    }
}
