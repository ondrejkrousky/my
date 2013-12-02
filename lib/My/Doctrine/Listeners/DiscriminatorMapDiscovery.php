<?php

namespace My\Doctrine\Listeners;

use Nette\Reflection\ClassType;

/**
 * Discriminator map discovery
 *
 * Support for defining discriminator maps at Child-level
 *
 * @author	Patrik Votoček
 * @author	Filip Procházka
 */
class DiscriminatorMapDiscovery extends \Nette\Object implements \Doctrine\Common\EventSubscriber
{
	/** @var \Doctrine\Common\Annotations\Reader */
	private $reader;

	/**
	 * @param \Doctrine\Common\Annotations\Reader
	 */
	public function __construct(\Doctrine\Common\Annotations\Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			\Doctrine\ORM\Events::loadClassMetadata,
		);
	}

	
	/**
	 * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs
	 */
	public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $args)
	{
        $meta = $args->getClassMetadata();
		$driver = $args->getEntityManager()->getConfiguration()->getMetadataDriverImpl();

		if ($meta->isInheritanceTypeNone()) {
			return;
		}

		$map = $meta->discriminatorMap;
        foreach ($this->getChildClasses($driver, $meta->name) as $className) {
            $entry = $this->getEntryName($className);
			if (/* fix: !in_array($className, $meta->discriminatorMap) && */$entry = $this->getEntryName($className)) {
                /* fix */
                if($key = array_search($className, $map)){
                    unset($map[$key]);
                }
                /* end fix*/
                $map[$entry->name] = $className;
			}
		}

		$meta->setDiscriminatorMap($map);
		$meta->subClasses = array_unique($meta->subClasses); // really? may array_values($map)
	}

	/**
	 * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
	 * @param string
	 * @return array
	 */
	private function getChildClasses(\Doctrine\Common\Persistence\Mapping\Driver\MappingDriver $driver, $currentClass)
	{
		$classes = array();
		foreach ($driver->getAllClassNames() as $className) {
			if (!ClassType::from($className)->isSubclassOf($currentClass)) {
				continue;
			}

			$classes[] = $className;
		}
		return $classes;
	}

	/**
	 * @param string
	 * @return string|NULL
	 */
	private function getEntryName($className)
	{
		return $this->reader->getClassAnnotation(
			ClassType::from($className), 'Doctrine\ORM\Mapping\DiscriminatorEntry'
		) ? : NULL;
	}
}

