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
 * @since              06.12.2017
 */
declare(strict_types=1);

use Xajax\Helper\Directories;

/**
 * Class DirectoriesTest
 */
class DirectoriesTest extends \Codeception\Test\Unit
{
	protected static function getRootDir()
	{
		return dirname(__DIR__, 3);
	}

	/**
	 * Method to cleanup an directory name an check the directory exists in system()
	 *
	 * @param $dir
	 * @param $expected
	 * @param $message
	 *
	 * @dataProvider providerDirectories
	 */
	public function testCleanDir($dir, $expected, $message)
	{
		Directories::setWebDirectory(self::getRootDir());
		$actual = ($dir = Directories::cleanDir($dir)) ? true : false;
		$this->assertEquals($expected, $actual, $message);
	}

	/**
	 * @return array
	 */
	public function providerDirectories(): array
	{
		return [
		    ['/tests/unit/datas/directories/jsoneDirectory', true, 'Directory Exists'],
		    ['/tests/unit/datas/directories/jsTwoDirectory', true, 'Directory Exists'],
		    ['/tests/unit/datas/directories/jsTwoDirectorsssy', false, 'Directory not Exists'],
		    ['/tests/unit/datas/directories/jsTwoDirectory/../../', true, 'Directory must be cleaned up'],
		    ['/tests/unit/datas/../../directories/jsTwoDirectory/../../', true, 'Directory must be cleaned up between'],
		];
	}

	/**
	 * Testing get Relative dir from Absolute Dir
	 *
	 * @param $absDir
	 * @param $expected
	 * @param $message
	 *
	 * @dataProvider providerGetRelative
	 */
	public function testGetRelative($absDir, $expected, $message)
	{
		Directories::setWebDirectory(self::getRootDir());
		$actual = Directories::getRelative($absDir);
		$this->assertEquals($expected, $actual, $message);
	}

	/**
	 * @return array
	 */
	public function providerGetRelative(): array
	{
		return [
		    [self::getRootDir() . '/tests/unit/datas/directories/jsoneDirectory',
		     '/tests/unit/datas/directories/jsoneDirectory',
		     'Directory Exists'],
		    [self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectory',
		     '/tests/unit/datas/directories/jsTwoDirectory',
		     'Directory Exists'],
		    [self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectorsssy', null, 'Directory not Exists'],
		    [self::getRootDir() . '/tests/unit/datas/directories/jsTwoDirectory/../../',
		     '/tests/unit/datas/directories/jsTwoDirectory/',
		     'Directory is evil'],
		];
	}
}
