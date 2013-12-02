<?php


namespace My\Doctrine\Mapping;

/**
 * I realy do feel bad for definning class in foreign namespace
 * but I have a good reason. This little hack prevents me from doing much uglier things.
 *
 * In order to be able to define own annotation without namespace prefix (ugly) I'm forced
 * to create another AnnotationReader instance and read the damn class fucking twice,
 * to be able to have annotation in my own namespace, without prefix.
 *
 * So fuck it, this is the best god damn fucking way. Don't you dare to question my sanity.
 */


/**
 * @author	Filip Procházka
 * @Annotation
 * @Target("CLASS")
 */
class DiscriminatorEntry extends \Doctrine\Common\Annotations\Annotation{
	public $name;
}

class_alias('My\Doctrine\Mapping\DiscriminatorEntry', 'Doctrine\ORM\Mapping\DiscriminatorEntry');

