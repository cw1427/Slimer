<?php
/**
 * Author: Shawn Chen
 * Desc:
 */
namespace App\Models;


class User extends \Slimer\Orm\Entity {

    protected $tableName = 'user';
    protected $schema = ['id','loginName','password','userName','firstName','lastName','email','active','type', 'createdOn','createdBy','changedOn','changedBy'];
    
    public function getUserGroups(){
        try {
            return $this->dbDefault->query("select g.* from <perm_group> as g join <perm_usergroup> as ug on ug.GroupID = g.ID where <ug.UserID> = :id",['id'=>$this->data['id']])->fetchAll();
        } catch (\Exception $e) {
            $this->logger->error("Load user perm_group error, plesae run Dbinit and RbacInit command first");
            return null;
        }
    }
    
    public function getUserRoles($groupid=null){
        try {
            return $this->rbac->allRoles($groupid);
        } catch (\Exception $e) {
            $this->logger->error("Load user perm_roles error, plesae run Dbinit and RbacInit command first");
            return null;
        }
    }
    
    public function updateLogin(){
        $this->set("lastLogin",$this->get("changedOn"));
        $this->set("changedOn",date('Y-m-d H:i:s',time()));
        $this->set("changedBy",$_SERVER['REMOTE_ADDR']);
        $this->dbDefault->update($this->getTable(),["changedOn"=>$this->get("changedOn"),"changedBy"=>$this->get("changedBy")],['id'=>$this->data['id']]);
    }
    
    public function getTable()
    {
        return $this->tableName;
    }

    public function getRelations()
    {}

    public function getValidators()
    {}


}
