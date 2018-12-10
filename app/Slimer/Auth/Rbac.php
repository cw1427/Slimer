<?php
/**
 * Author: Shawn Chen
 * Desc: The authentication function provider
 * http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
 * The hierarchical data structure in DB
 */
namespace Slimer\Auth;

use PhpRbac\Rbac as PhpRbac;
use \Jf;

class Rbac extends PhpRbac
{
    
    public $container;
    public $target;
    
    public function __construct($unit_test = '',$container=null)
    {
        $this->container = $container;
        $host = $this->container['config']('db.default.server');
        $user = $this->container['config']('db.default.username');
        $pass = $this->container['config']('db.default.password');
        $dbname = $this->container['config']('db.default.database_name');
        $adapter="pdo_mysql";
        $tablePrefix = $this->container['config']('rbac.rbac_table_prefix') ? $this->container['config']('rbac.rbac_table_prefix') : 'PREFIX_';
      
        require_once APP_ROOT . DS . 'vendor/owasp/phprbac/PhpRbac/src/PhpRbac/core/lib/Jf.php';
        
        $this->Permissions = Jf::$Rbac->Permissions;
        $this->Roles = Jf::$Rbac->Roles;
        $this->Users = Jf::$Rbac->Users;
    }
    
    function check($Permission, $UserID = null)
    {
        if ($UserID === null)
            throw new \RbacUserNotProvidedException ("\$UserID is a required argument.");
        
         if (is_numeric($UserID)){
             return parent::check($Permission, $UserID);
         }else{
             $UserID = \implode(',', $UserID);
            // convert permission to ID
            if (is_numeric ( $Permission ))
            {
                $PermissionID = $Permission;
            }
            else
            {
                if (substr ( $Permission, 0, 1 ) == "/")
                    $PermissionID = $this->Permissions->pathId ( $Permission );
                    else
                        $PermissionID = $this->Permissions->titleId ( $Permission );
            }
            // if invalid, throw exception
            if ($PermissionID === null)
                throw new \RbacPermissionNotFoundException ( "The permission '{$Permission}' not found." );
                
                if ($this->isSQLite())
                {
                    $LastPart="AS Temp ON ( TR.ID = Temp.RoleID)
 							WHERE
 							TUrel.UserID in (" . $UserID . ")
 							AND
 							Temp.ID=?";
                }
                else //mysql
                {
                    $LastPart="ON ( TR.ID = TRel.RoleID)
 							WHERE
 							TUrel.UserID in (" . $UserID . ")
 							AND
 							TPdirect.ID=?";
                }
                $Res=Jf::sql ( "SELECT COUNT(*) AS Result
            FROM
            {$this->tablePrefix()}userroles AS TUrel
            
            JOIN {$this->tablePrefix()}roles AS TRdirect ON (TRdirect.ID=TUrel.RoleID)
            JOIN {$this->tablePrefix()}roles AS TR ON ( TR.Lft BETWEEN TRdirect.Lft AND TRdirect.Rght)
            JOIN
            (	{$this->tablePrefix()}permissions AS TPdirect
            JOIN {$this->tablePrefix()}permissions AS TP ON ( TPdirect.Lft BETWEEN TP.Lft AND TP.Rght)
            JOIN {$this->tablePrefix()}rolepermissions AS TRel ON (TP.ID=TRel.PermissionID)
            ) $LastPart", $PermissionID );
                return $Res [0] ['Result'] >= 1;
         }
    }
    
    protected function isSQLite()
    {
        $Adapter=get_class(Jf::$Db);
        return $Adapter == "PDO" and Jf::$Db->getAttribute(\PDO::ATTR_DRIVER_NAME)=="sqlite";
    }
    protected function isMySql()
    {
        $Adapter=get_class(Jf::$Db);
        return $Adapter == "mysqli" or ($Adapter == "PDO" and Jf::$Db->getAttribute(\PDO::ATTR_DRIVER_NAME)=="mysql");
    }
    
    /*
     * overwrite the RbacUserManager allRoles to fetch all of the group roles
     * 
     */
    function allRoles($UserID = null)
    {
        if (is_numeric($UserID)){
            return $this->Users->allRoles($UserID);
        }else{
            if ($UserID === null)
                    throw new \RbacUserNotProvidedException ("\$UserID is a required argument.");
            $UserID = \implode(',', $UserID);
            return Jf::sql ( "SELECT TR.*
			FROM
			{$this->tablePrefix()}userroles AS `TRel`
			JOIN {$this->tablePrefix()}roles AS `TR` ON
			(`TRel`.RoleID=`TR`.ID)
			WHERE TRel.UserID in ( ". $UserID. ")" );
            
        }
    }
    
    public function showRoleTree(){
        $this->target="roles";
        return $this->_showTree();
    }
    
    public function showPermTree(){
        $this->target="permissions";
        return $this->_showTree();
    }
    /*
     * show the whole hierarchical tree in the console
     */
    private function _showTree(){
        $Query=" SELECT CONCAT( REPEAT('-', COUNT(parent.Title) - 1), node.Title) as name, (COUNT(parent.Title) - 1) as depth from {$this->table()} as node, {$this->table()} as parent
                    where node.{$this->left()} between parent.{$this->left()} and parent.{$this->right()} 
                    group by node.{$this->id()}
                    order by node.{$this->left()}
                ";
        return JF::sql( $Query );
    }
    
    private function table(){
        return "{$this->tablePrefix()}{$this->target}";
    }
    
    private function id(){
        return "ID";
    }
    private function left(){
        return "Lft";
    }
    private function right(){
        return "Rght";
    }
  
}