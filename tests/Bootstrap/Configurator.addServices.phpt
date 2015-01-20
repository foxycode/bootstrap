<?php

/**
 * Test: Nette\Configurator::addServices()
 */

use Nette\Configurator,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class TestExistingService
{

	private $scream;

	public function __construct($scream = true)
	{
		$this->scream = $scream;
	}

	public function run()
	{
		if ($this->scream) {
			throw new \Exception('This is an instance created by container and should not be called');
		}
	}

}

$configurator = new Configurator;
$configurator->setTempDirectory(TEMP_DIR);
$configurator->addConfig(Tester\FileMock::create('
services:
	existingService:
		class: TestExistingService
		run: yes
		setup:
			- run

', 'neon'));

$existingService = new TestExistingService(false);
$newService = new stdClass();
$addServiceTwice = new stdClass();

$configurator->addServices(array(
	'existingService' => $existingService,
	'newService' => $newService,
	'addServiceTwice' => $addServiceTwice,
));

$addServiceTwice = new stdClass();

$configurator->addServices(array(
	'addServiceTwice' => $addServiceTwice,
));

$container = $configurator->createContainer();

Assert::same($existingService, $container->getService('existingService'));
Assert::same($newService, $container->getService('newService'));
Assert::same($addServiceTwice, $container->getService('addServiceTwice'));
