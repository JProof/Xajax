<?php
/**
 * PHP version php7
 *
 * @category
 * @package            xajax-php-7
 * @author             ${JProof}
 * @copyright          ${copyright}
 * @license            ${license}
 * @link
 * @see                ${docu}
 * @since              08.12.2017
 */

declare(strict_types=1);

namespace Xajax\Scripts;

use Xajax\Helper\Directories;

/**
 * Class ScriptsTest
 *
 * @package Xajax\Scripts
 */
class ScriptsTest extends \Codeception\Test\Unit
{
	/**
	 * simple Helper
	 *
	 * @return string
	 */
	protected static function getRootDir(): string
	{
		return dirname(__DIR__, 3);
	}

	/**
	 * @return \Xajax\Scripts\Scripts
	 */
	protected function getScriptsInstance(): Scripts
	{
		Directories::setWebDirectory(self::getRootDir());

		return Scripts::getInstance();
	}

	/**
	 * @dataProvider providerAddScriptDir
	 */
	public function testAddScriptDir($dir, $expected, $msg)
	{
		$scripts = $this->getScriptsInstance();
		$actual  = $scripts->addScriptDir($dir);

		$this->assertEquals($expected, $actual, $msg);
	}

	/**
	 * @return array
	 */
	public function providerAddScriptDir(): array
	{
		return [
		    [self::getRootDir() . '/tests/unit/datas\\//directories/jsTwoDirectory', true, 'Ugly Input dir is cleaned but will be add',],
		    [self::getRootDir(), true, 'Root is Root',],
		    [self::getRootDir() . '/tests/unit/datas\\//', true, 'Ugly Input dir is cleaned an valid relative Dir',],
		    ['//unit/datas\\//', false, 'Can not add Evil Dir ',],
		];
	}
}
