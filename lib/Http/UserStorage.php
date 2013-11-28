<?php

namespace My\Http;

use \Nette\Security\IIdentity,
	\Nette\Http\Session,
	\Doctrine\ORM\EntityManager;

/**
 * Description of UserStorage
 *
 * @author Ondra
 */
class UserStorage extends \Nette\Http\UserStorage{

	
	/**
	 *
	 * @var string User Entity name 
	 */
	private $identityClassName;
	
	
	/**
	 * @var \Doctrine\ORM\EntitManager EntityManager
	 */
	private $em;
	
	
	/**
	 * Constructor
	 * @param \Nette\Http\Session $session
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param string $identityClassName
	 * @throws \Nette\InvalidArgumentException when invalid $identityClassName given.
	 */
	public function __construct(Session $session, EntityManager $em, $identityClassName){
		parent::__construct($session);
		
		$this->em = $em;
		
		if(!class_exists($identityClassName)){
			throw new \Nette\InvalidArgumentException("IdentityClassName '$identityClassName' must be valid class. This class does not exists.");
		}
		
		$this->identityClassName = $identityClassName;
	}
	
	
	/**
	 * Sets the user identity
	 * @param \Nette\Security\IIdentity $identity
	 * @return self
	 */
	public function setIdentity(IIdentity $identity = NULL){
		$this->getSessionSection(TRUE)->identityId = $identity->getId();
		return $this;
	}
	
	
	public function getIdentity(){
		$session = $this->getSessionSection(FALSE);
		return $session->identityId ? $this->em->find($this->identityClassName, $session->identityId) : NULL;
	}
	
}

