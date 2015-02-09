<?php
/*
* This file is part of the ReCaptcha Validator Component.
*
* (c) Ilya Pokamestov
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DS\Component\ReCaptchaValidator\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Composer\Script\CommandEvent;

/**
 * Script handler for post install/update commands.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ScriptHandler
{
	/** @var  Filesystem */
	protected static $fileSystem;

	public static function replaceViews(CommandEvent $event)
	{
		$event->getIO()->write('Copy ReCaptcha views to app/Resource/DS/ReCaptcha/views.');

		$directory = getcwd();
		self::$fileSystem = new Filesystem();

		$directory .= '/app/Resources';
		self::makeDirIfNOtExist($directory);

		$directory .= '/DS';
		self::makeDirIfNOtExist($directory);

		$directory .= '/ReCaptcha';
		self::makeDirIfNOtExist($directory);

		$directory .= '/views';
		self::makeDirIfNOtExist($directory);

		if(self::$fileSystem->exists($directory.'/form_div_layout.html.twig'))
		{
			if (!$event->getIO()->askConfirmation(sprintf('form_div_layout.html.twig already exist in %s. Would you like to rewrite this file? [y/N]  ', $directory), false)) {
				return;
			}
		}

		self::$fileSystem->copy(__DIR__.'/../Resources/views/form_div_layout.html.twig', $directory.'/form_div_layout.html.twig', true);

		$event->getIO()->write('Files creation operation completed successfully.');
	}

	/**
	 * @param string $dir
	 * @throws \RuntimeException
	 */
	protected static function makeDirIfNOtExist($dir)
	{
		if(!self::isDirectoryExist($dir))
		{
			self::$fileSystem->mkdir($dir, 775);
		}

		if(!self::isDirectoryExist($dir))
		{
			throw new \RuntimeException(sprintf('Can\'t create directory: "%s"', $dir));
		}
	}

	/**
	 * @param string $dir
	 * @return bool
	 */
	protected static function isDirectoryExist($dir)
	{
		return self::$fileSystem->exists($dir);
	}
}
