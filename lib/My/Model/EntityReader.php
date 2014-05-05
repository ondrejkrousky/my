<?php

namespace My\Model;

use \My\Model\Authorization\Resource;


/**
 * Description of EntityReader
 *
 * @author Ondra
 */
class EntityReader extends \Nette\Object{
	

	/** @var \Nette\Security\User */
	protected $currentUser;
	
	
	/**
	 * Constructor
	 * @param \Nette\Security\User $user
	 */
	public function __construct(\Nette\Security\User $user){
		$this->currentUser = $user;
	}
	
	
	/**
	 * Check authorization rights and read field from entity.
	 * @param \My\Model\Entity $entity
	 * @param string $field
	 * @return mixed
	 * @throws UnauthorizedAccessException
	 */
	public function get(Entity $entity, $field){
		if(!$this->currentUser->isAllowed(new Resource($entity, $field), 'read')){
			throw new UnauthorizedAccessException();
		}
		$getter = 'get' . ucfirst($field);
		return $entity->$getter();
	}
	
}
