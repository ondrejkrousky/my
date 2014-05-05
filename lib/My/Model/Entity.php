<?php

namespace My\Model;



class Entity extends \Nette\Object{
	
	
	
	/**
	 * Magic function call. This enables using getters and setters.
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args){
		if(preg_match('/^(set|get|add|remove)([a-zA-z0-9]+)$/', $name, $matches)){
			list($all, $cmd, $_field) = $matches;
			$field = lcfirst($_field);
			return $this->{$cmd}($field, $args);
		}
		return parent::__call($name, $args);
	}

	/**
	 * Sets value into field.
	 * @param string $field
	 * @param type $args
	 * @return self
	 */
	private function set($field, $args){
		$this->{$field} = $args[0];
		return $this;
	}
	
	/**
	 * Returns field value
	 * @param string $field
	 * @return mixed
	 */
	private function get($field){
		return $this->{$field};
	}
	
	
	/**
	 * Field element adder
	 * @param string $field
	 * @param array $args
	 * @return self
	 */
	private function add($field, $args){
		$field = $this->pluralize($field);
		if($this->{$field} instanceof \Doctrine\Common\Collections\Collection){
			$this->{$field}->add($args[0]);
		}else{
			array_push($this->{$field}, $args[0]);
		}
		return $this;
	}
	
	
	/**
	 * Field element remover
	 * @param string $field
	 * @param array $args
	 * @return self
	 */
	private function remove($field, $args){
		$field = $this->pluralize($field);
		if($this->{$field} instanceof \Doctrine\Common\Collections\Collection){
			$this->{$field}->removeElement($args[0]);
		}else{
			$index = array_search($args[0], $this->{$field});
			unset($this->{$field}[$index]);
		}
		return $this;
	}
        
	
	
	/**
	 * Pluralize field name for collections
	 * @param string $field
	 * @return string
	 */
	private function pluralize($field){
		if(Strings::endsWith($field, 'y')){
			return substr($field, 0, -1) . 'ies';
		}else{
			return $field . 's';
		}
	}
	
}