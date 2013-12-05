<?php



namespace My\Doctrine\Config;


use My\Console\Config\Extension as CExtension,
	Nette\Config\Configurator,
	Nette\Config\Compiler,
	Nette\Utils\Strings;

/**
 * Doctrine migration Nella Framework services.
 *
 * @author	Patrik Votoček
 *
 * @property array defaults
 */
class MigrationsExtension extends \Nette\DI\CompilerExtension
{
	const DEFAULT_EXTENSION_NAME = 'migrations';

	public $defaultName = NULL;

	/**
	 * @return array
	 */
	private function getDefaults()
	{
		return array(
			'name' => $this->defaultName ?: \Nette\Framework::NAME . ' DB Migrations',
			'connection' => '@' . Extension::DEFAULT_EXTENSION_NAME . '.connection', // @doctrine.connection
			'table' => '_db_version',
			'directory' => '%appDir%/migrations',
			'namespace' => 'App\Model\Migrations',
			'console' => TRUE,
		);
	}

	/**
	 * Processes configuration data
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		if (!$this->getConfig()) { // ignore migrations if config section not exist
			return;
		}

		$config = $this->getConfig($this->getDefaults());
		$builder = $this->getContainerBuilder();

		$connection = Strings::startsWith($config['connection'], '@')
			? $config['connection'] : ('@' . $config['connection']);

		$consoleOutput = $builder->addDefinition($this->prefix('consoleOutput'))
			->setClass('Doctrine\DBAL\Migrations\OutputWriter')
			->setFactory(get_called_class().'::createConsoleOutput')
			->setAutowired(FALSE);

		$configuration = $builder->addDefinition($this->prefix('configuration'))
			->setClass('Doctrine\DBAL\Migrations\Configuration\Configuration', array(
				$connection, $consoleOutput
			))
			->addSetup('setName', array($config['name']))
			->addSetup('setMigrationsTableName', array($config['table']))
			->addSetup('setMigrationsDirectory', array($config['directory']))
			->addSetup('setMigrationsNamespace', array($config['namespace']))
			->addSetup('registerMigrationsFromDirectory', array($config['directory']));

		if (isset($config['console']) && $config['console']) {
			$this->processConsole($configuration);
		}
	}

	/**
	 * @param \Nette\DI\ServiceDefinition|string
	 */
	protected function processConsole($configuration)
	{
		if (!class_exists('My\Console\Config\Extension')) {
			throw new \Nette\InvalidStateException('Missing console extension');
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('consoleCommandDiff'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandExecute'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandGenerate'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandMigrate'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandStatus'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
		$builder->addDefinition($this->prefix('consoleCommandVersion'))
			->setClass('Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand')
			->addSetup('setMigrationConfiguration', array($configuration))
			->addTag(CExtension::COMMAND_TAG_NAME)
			->setAutowired(FALSE);
	}

	/**
	 * @return \Doctrine\DBAL\Migrations\OutputWriter
	 */
	public static function createConsoleOutput()
	{
		$output = new \Symfony\Component\Console\Output\ConsoleOutput;
		return new \Doctrine\DBAL\Migrations\OutputWriter(function ($message) use ($output) {
			$output->write($message, TRUE);
		});
	}

	/**
	 * Register extension to compiler.
	 *
	 * @param \Nette\Config\Configurator
	 * @param string
	 */
	public static function register(Configurator $configurator, $name = self::DEFAULT_EXTENSION_NAME)
	{
		$class = get_called_class();
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($class, $name) {
			$compiler->addExtension($name, new $class);
		};
	}
}

