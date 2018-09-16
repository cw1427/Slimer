<?php
/**
 * Author: Shawn Chen
 * Desc:
 */
namespace App\Models;


class User extends \Slimer\Orm\Entity {

    protected $tableName = 'user';
    protected $schema = ['id','loginName','password','userName','firstName','lastName','email','active','type', 'createdOn','createdBy','changedOn','changedBy'];
    
    public function getTable()
    {
        return $this->tableName;
    }

    public function getRelations()
    {}

    public function getValidators()
    {}


}
