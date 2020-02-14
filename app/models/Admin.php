<?php
namespace Growave2\Models;

class Admin extends \Phalcon\Mvc\Model
{
 public function initialize()
 {
     $this->setSource('users');
 }
}