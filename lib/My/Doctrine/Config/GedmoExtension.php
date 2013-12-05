<?php


namespace My\Doctrine\Config;

use Doctrine\Common\Persistence\ObjectManager,
	Doctrine\Common\EventSubscriber,
	Nette\Config\Configurator,
	Nette\Config\Compiler,
	Nette\Utils\Strings;

/**
 * Gedmo Nella Framework services.
 *
 * @author	Patrik Votoček
 */
class GedmoExtension extends \Nette\DI\CompilerExtension{
	
	const DEFAULT_EXTENSION_NAME = 'gedmo';

	/** @var array */
	public $defaults = array(
		'orm' => array(
			'em' => NULL,
			'loggable' => FALSE,
			'sluggable' => FALSE,
			'softDeleteable' => FALSE,
			'sortable' => FALSE,
			'timestampable' => FALSE,
			'translatable' => FALSE,
			'tree' => FALSE,
			'uploadable' => FALSE,
		),
		'odm' => array(
			'dm' => NULL,
			'loggable' => FALSE,
			'sluggable' => FALSE,
			'sortable' => FALSE,
			'timestampable' => FALSE,
			'translatable' => FALSE,
			'tree' => FALSE,
		),
	);

	/**
	 * Processes configuration data
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('loggable'))
			->setClass('Gedmo\Loggable\LoggableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('sluggable'))
			->setClass('Gedmo\Sluggable\SluggableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('softDeleteable'))
			->setClass('Gedmo\SoftDeleteable\SoftDeleteableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('sortable'))
			->setClass('Gedmo\Sortable\SortableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('timestampable'))
			->setClass('Gedmo\Timestampable\TimestampableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('translatable'))
			->setClass('Gedmo\Translatable\TranslatableListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('tree'))
			->setClass('Gedmo\Tree\TreeListener')
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('uploadable'))
			->setClass('Gedmo\Uploadable\UploadableListener')
			->setAutowired(FALSE);
	}

	public function beforeCompile()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$ref = \Nette\Reflection\ClassType::from('Gedmo\DoctrineExtensions');
		$baseDir = pathinfo($ref->getFileName(), PATHINFO_DIRNAME);

		$loggable = $this->prefix('@loggable');
		$sluggable = $this->prefix('@sluggable');
		$softDeleteable = $this->prefix('@softDeleteable');
		$sortable = $this->prefix('@sortable');
		$timestampable = $this->prefix('@timestampable');
		$translatable = $this->prefix('@translatable');
		$tree = $this->prefix('@tree');
		$uploadable = $this->prefix('@uploadable');

		if (!is_array($config['orm']['em'])) {
			$config['orm']['em'] = is_null($config['orm']['em']) ? array() : array($config['orm']['em']);
		}
		if (!is_array($config['odm']['dm'])) {
			$config['odm']['dm'] = is_null($config['odm']['dm']) ? array() : array($config['odm']['dm']);
		}

		foreach ($config['orm']['em'] as $omName) {
			$om = $builder->getDefinition($this->removeAt($omName));

			if ($config['orm']['loggable']) {
				$om->addSetup(get_called_class() . '::registerLoggable', array($om, $baseDir, $loggable));
			}

			if ($config['orm']['sluggable']) {
				$om->addSetup(get_called_class() . '::registerSluggable', array($om, $sluggable));
			}

			if ($config['orm']['softDeleteable']) {
				$om->addSetup(get_called_class() . '::registerSoftDeleteable', array($om, $softDeleteable));
			}

			if ($config['orm']['sortable']) {
				$om->addSetup(get_called_class() . '::registerSortable', array($om, $sortable));
			}

			if ($config['orm']['timestampable']) {
				$om->addSetup(get_called_class() . '::registerTimestampable', array($om, $timestampable));
			}

			if ($config['orm']['translatable']) {
				$om->addSetup(get_called_class() . '::registerTranslatable', array($om, $baseDir, $translatable));
			}

			if ($config['orm']['tree']) {
				$om->addSetup(get_called_class() . '::registerTree', array($om, $baseDir, $tree));
			}

			if ($config['orm']['uploadable']) {
				$om->addSetup(get_called_class() . '::registerUploadable', array($om, $uploadable));
			}
		}

		foreach ($config['odm']['dm'] as $omName) {
			$om = $builder->getDefinition($this->removeAt($omName));

			if ($config['odm']['loggable']) {
				$om->addSetup(get_called_class() . '::registerLoggable', array($om, $baseDir, $loggable));
			}

			if ($config['odm']['sluggable']) {
				$om->addSetup(get_called_class() . '::registerSluggable', array($om, $sluggable));
			}

			if ($config['odm']['sortable']) {
				$om->addSetup(get_called_class() . '::registerSortable', array($om, $sortable));
			}

			if ($config['odm']['timestampable']) {
				$om->addSetup(get_called_class() . '::registerTimestampable', array($om, $timestampable));
			}

			if ($config['odm']['translatable']) {
				$om->addSetup(get_called_class() . '::registerTranslatable', array($om, $baseDir, $translatable));
			}

			if ($config['odm']['tree']) {
				$om->addSetup(get_called_class() . '::registerTree', array($om, $baseDir, $tree));
			}
		}
	}

	/**
	 * @param \Nette\Utils\PhpGenerator\ClassType
	 */
	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->methods['initialize'];
		$initialize->addBody('\Gedmo\DoctrineExtensions::registerAnnotations();');
	}

	/**
	 * @param string
	 * @return string
	 */
	private function removeAt($name)
	{
		if (Strings::startsWith($name, '@')) {
			return Strings::substring($name, 1);
		}
		return $name;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param string
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerLoggable(ObjectManager $om, $baseDir, EventSubscriber $listener)
	{
		$driver = $om->getConfiguration()->getMetadataDriverImpl();

		if ($driver instanceof \Doctrine\ORM\Mapping\Driver\AnnotationDriver) {
			$driver->addPaths(array($baseDir . '/Loggable/Entity'));
		} elseif ($driver instanceof \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver) {
			$driver->addPaths(array($baseDir . '/Loggable/Document'));
		}

		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerSluggable(ObjectManager $om, EventSubscriber $listener)
	{
		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerSoftDeleteable(ObjectManager $om, EventSubscriber $listener)
	{
		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
			if ($om instanceof \Doctrine\ORM\EntityManager) {
				$filter = new \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter($om);
				$om->getConfiguration()->addFilter('soft-deleteable', $filter);
			}
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerSortable(ObjectManager $om, EventSubscriber $listener)
	{
		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param EventSubscriber
	 */
	public static function registerTimestampable(ObjectManager $om, EventSubscriber $listener)
	{
		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param string
	 * @param EventSubscriber
	 */
	public static function registerTranslatable(ObjectManager $om, $baseDir, EventSubscriber $listener)
	{
		$driver = $om->getConfiguration()->getMetadataDriverImpl();

		if ($driver instanceof \Doctrine\ORM\Mapping\Driver\AnnotationDriver) {
			$driver->addPaths(array($baseDir . '/Translatable/Entity'));
		} elseif ($driver instanceof \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver) {
			$driver->addPaths(array($baseDir . '/Translatable/Document'));
		}

		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param string
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerTree(ObjectManager $om, $baseDir, EventSubscriber $listener)
	{
		$driver = $om->getConfiguration()->getMetadataDriverImpl();

		if ($driver instanceof \Doctrine\ORM\Mapping\Driver\AnnotationDriver) {
			$driver->addPaths(array($baseDir . '/Tree/Entity'));
		}

		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager
	 * @param \Doctrine\Common\EventSubscriber
	 */
	public static function registerUploadable(ObjectManager $om, EventSubscriber $listener)
	{
		if ($om instanceof \Doctrine\ORM\EntityManager || $om instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
			$om->getEventManager()->addEventSubscriber($listener);
		}
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