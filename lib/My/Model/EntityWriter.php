<?php

namespace My\Model;

/**
 * Description of EntityWriter
 *
 * @author Ondra
 */
class EntityWriter extends \Nette\Object{

	/** @var \Nette\Security\User */
	protected $user;
	
	/** @var \Doctrine\ORM\EntityManager */
	protected $em;
	
	/** @var */
	protected $meta;
	
	
	public function __construct(\Nette\Security\User $user, \Doctrine\ORM\EntityManager $em){
		$this->user = $user;
		$this->em = $em;
	}
	
	
	/**
	 * Univerzální setter políček entit. 
	 *  - Musí obsáhnout situace má-li entita persistentní políčko
	 *  - a současně není-li políčko persistentní, resp. má-li nemagický setter
	 *  - např. setPassword($newPassword)
	 * 
	 * 
	 * @param type $entity
	 * @param type $field
	 * @param type $value
	 * @throws Exception
	 */
	public function set(Entity $entity, $field, $value){
		// kontrola existence property
		$setter = $this->checkSetPossibility($entity, $field);
		
		// kontrola validity vkládané hodnoty
		$this->checkValidity($entity, $field, $value);
		
		// kontrola autorizace k zápisu
		$this->checkAuthroization($entity, $field);
		
		return $entity->$setter($value);
		
	}

	/**
	 * Check if should be given field set.
	 * @param type $entity
	 * @param type $field
	 * @return type
	 * @throws Exception
	 */
	protected function checkSetPossibility($entity, $field){
		$meta = $this->em->getClassMetadata(get_class($entity));
		$setter = 'set' . ucfirst($field);
		if(($meta->hasField($field) && !$meta->isIdentifier($field))			//is ordinary field
				|| ($meta->hasAssociation($field) && $meta->isSingleValueAssociation($field)) // is single value association
				|| method_exists($entity, $field)								// entity has corresponding setter
				|| $meta->getReflectionClass()->hasProperty($field)){			// field that has not setter is not persisted by doctrine and use magic setter.
			return $setter;
		}
		throw new Exception();
	}
	
	
	
	/**
	 * Check if value is valid for given field.
	 * @param type $entity
	 * @param type $field
	 * @param type $value
	 */
	protected function checkValidity($entity, $field, $value){
		
	}
	
	
	/**
	 * Check if current user has permission to write to given entity
	 * @param type $entity
	 * @param type $field
	 * @return boolean
	 * @throws Exception
	 */
	protected function checkAuthorization($entity, $field){
		if($this->user->isAllowed(new Resource($entity, $field), 'write')){
			return true;
		}
		throw new Exception();
	}
	
	
	
	
}
