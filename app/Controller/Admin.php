<?php
/**
 * Author: Shawn Chen
 * Desc: Admin module controller
 */

namespace App\Controller;

use function GuzzleHttp\json_encode;

/**
 * Admin Controler for the GAM admin component.
 *
 * That controller handles work with auth for you
 */
class Admin extends \Slimer\Controller
{
    
    public function user_manageAction(){
        
        return $this->render('admin/usermanage.html.twig');
    }
    
    public function get_usersAction(){
        $params = $this->request->getParams();
        $offset = $params['offset'];
        $limit = $params['limit'];
        $search = isset($params['search'])? $params['search']: null;
        $WHERE = [];
        if (isset($search) && $search !=""){
            $WHERE['OR']=["loginName[~]"=>"%{$search}%","userName[~]"=>"%{$search}%"];
        }
        $total = $this->dbDefault->count("user",$WHERE);
        $WHERE['LIMIT']=[$offset,$limit];
        $userList=$this->dbDefault->select("user","*",$WHERE);
        //---collect all of the current page user id to fetch their group set
        $uids=[];
        foreach($userList as $user){
            \array_push($uids,$user["id"]);
        }
        if (sizeof($uids)>0){
            $groupList = $this->dbDefault->select("perm_usergroup(a)",["[><]perm_group(b)"=>["a.GroupID"=>"ID"],"[><]user(c)"=>["a.UserID"=>"id"]],
                ["b.Name","b.Title","c.id"],["c.id"=>$uids]);
        }else{
            $groupList=[];
        }
        foreach($userList as &$user){
            $user["groups"]=[];
            foreach ($groupList as $group){
                if ($user["id"]==$group["id"]){//----this group belong to this user
                    \array_push($user["groups"],$group);
                }
            }
        }
        return $this->response->withStatus(200)->withJson(["rows"=>$userList,"total"=>$total]);
    }
    
    public function add_userAction(){
        $params = $this->request->getParams();
        //---check if the loginName exists
        $count = $this->dbDefault->count("user",["loginName"=>$params["loginName"]]);
        if ($count>0){
            $this->flash->addMessage('danger',"loginName exists: {$params['loginName']}");
            return $this->response->withRedirect($this->router->pathFor('admin-user_manage'));
        }
        $user = $this->entity('user');
        $params["userName"] ="{$params["firstName"]} {$params["lastName"]}";
        $params["active"] =1;
        $params["type"] ="local";
        $params["createdOn"] =date('Y-m-d H:i:s',time());
        $params["createdBy"] = $this->user->get('loginName');
        $params["password"] = \password_hash("123456", PASSWORD_DEFAULT);
        $user->setData($params);
        //$user->save();
        $this->dbDefault->insert($user->getTable(),$params);
        $this->flash->addMessage('success',"Successfully add a new user {$params['loginName']}");
        return $this->response->withRedirect($this->router->pathFor('admin-user_manage'));
    }
    
    public function del_userAction(){
        $params = $this->request->getParsedBody();
        if (isset($params["id"])){
            //----protected admin account not allow been deleted
            if ($params["id"] ==1){
                return $this->json(["error"=>["message"=>"not allowed"]],400);
            }
            //---begin transaction delete the usergroup info first and then the user data
            $this->dbDefault->action(function($db) use (&$params){
                try{
                    $db->delete("perm_usergroup",["UserID"=>$params['id']]);
                    $db->delete("user",["id"=>$params["id"]]);
                    $this->flash->addMessage('success',"Successfully delete the user {$params['name']}");
                    $params["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $params["result"]=false;
                    return false;
                }
            });
                if ($params['result']){
                    return $this->json(["message"=>"successfully deleted"]);
                }else{
                    return $this->json(["error"=>["message"=>"internal error"]],500);
                }
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function edit_userAction($args){
        $id = $args['uid'];
        if ($id == 1 && $this->user->get('id') !=1){
            return $this->appErrorHandler->error403($this->request,$this->response);
        }
        $user = $this->entity('user');
        $user->load($id);
        //----fetch its groups and roles
        $gs=$user->getUserGroups();
        $permGroupIds = [];
        foreach ($gs as $group){
            \array_push($permGroupIds,$group['ID']);
        }
        $rs = $user->getUserRoles($permGroupIds);
        $user->set('groups',$gs);
        $user->set('roles',$rs);
        $user->set('groupsid',$permGroupIds);
        $groups=$this->dbDefault->select("perm_group","*");
        $this->render("admin/useredit.html.twig",["user"=>$user->getData(),"groups"=>$groups]);
    }
    
    public function userinfo_editAction(){
        $args = $this->request->getParams();
        $this->dbDefault->update("user",["firstName"=> $args['firstName'],"lastName"=>$args['lastName'],'userName'=>"{$args['firstName']} {$args['lastName']}"],["id"=>$args['id']]);
        $this->logger->info("userid={$args["id"]} userinfo changed by {$this->user->get('loginName')}");
        return $this->json(["message"=>"success"]);
    }
    
    public function password_changeAction(){
        $data = $this->request->getParsedBody();
        if (isset($data['newPassword']) && isset($data['id'])){
            if ($data['id'] ==1 && $this->user->get('id') != 1){
                return $this->json(["error"=>["message"=>"admin account not allow been modified by other account"],"msg"=>"not allow change admin account info"],403);
            }
            $this->dbDefault->update("user",["password"=> \password_hash($data['newPassword'], PASSWORD_DEFAULT)],["id"=>$data["id"]]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter, new password not found"]],400);
        }
        $this->logger->info("userid={$data["id"]} password changed by {$this->user->get('loginName')}");
        return $this->json(["message"=>"success"]);
    }
    
    public function usergroup_editAction(){
        $data = $this->request->getParsedBody();
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_usergroup",["UserID"=>$data['id']]);
                    $userGroupData = [];
                    foreach($data['selected'] as $g){
                        \array_push($userGroupData,["GroupID"=>$g,"UserID"=>$data['id']]);
                    }
                    if (sizeof($userGroupData)>0){
                        $db->insert("perm_usergroup",$userGroupData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["message"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
        
    }
    
    public function group_manageAction(){
        return $this->render('admin/groupmanage.html.twig');
    }
    
    public function get_groupsAction(){
        $params = $this->request->getParams();
        $offset = $params['offset'];
        $limit = $params['limit'];
        $search = isset($params['search'])? $params['search']: null;
        $WHERE = [];
        if (isset($search) && $search !=""){
            $WHERE['OR']=["Name[~]"=>"%{$search}%","Title[~]"=>"%{$search}%"];
        }
        $total = $this->dbDefault->count("perm_group",$WHERE);
        $WHERE['LIMIT']=[$offset,$limit];
        $groupList=$this->dbDefault->select("perm_group","*",$WHERE);
        //---collect all of the current page user id to fetch their group set
        $uids=[];
        foreach($groupList as $group){
            \array_push($uids,$group["ID"]);
        }
        if (sizeof($uids)>0){
            $roleList = $this->dbDefault->select("perm_userroles(a)",["[><]perm_roles(b)"=>["a.RoleID"=>"ID"],"[><]perm_group(c)"=>["a.UserID"=>"ID"]],
                ["b.Title","b.Description","c.ID"],["c.ID"=>$uids]);
        }else{
            $roleList=[];
        }
        foreach($groupList as &$group){
            $group["Roles"]=[];
            foreach ($roleList as $role){
                if ($role["ID"]==$group["ID"]){//----this role belong to this group
                    \array_push($group["Roles"],$role);
                }
            }
        }
        return $this->response->withStatus(200)->withJson(["rows"=>$groupList,"total"=>$total]);
    }
    
    public function add_groupAction(){
        $params = $this->request->getParams();
        $params['Name'] = trim($params['Name']," ");
        //---check if the loginName exists
        $count = $this->dbDefault->count("perm_group",['Name'=>$params['Name']]);
        if ($count>0){
            $this->flash->addMessage('danger',"group exists: {$params['Name']}");
            return $this->response->withRedirect($this->router->pathFor('admin-group_manage'));
        }
        $params["Type"]="local";
        $this->dbDefault->insert("perm_group",$params);
        $this->flash->addMessage('success',"Successfully add a new group {$params['Name']}");
        return $this->response->withRedirect($this->router->pathFor('admin-group_manage'));
    }
    
    public function del_groupAction(){
        $params = $this->request->getParsedBody();
        if (isset($params["id"])){
            //---begin transaction delete the usergroup info first and then the user data
            $this->dbDefault->action(function($db) use (&$params){
                try{
                    $db->delete("perm_usergroup",["GroupID"=>$params['id']]);
                    $db->delete("perm_userroles",["UserID"=>$params["id"]]);
                    $db->delete("perm_group",["ID"=>$params["id"]]);
                    $this->flash->addMessage('success',"Successfully delete the group {$params['name']}");
                    $params["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $params["result"]=false;
                    return false;
                }
            });
                if ($params['result']){
                    return $this->json(["message"=>"successfully deleted"]);
                }else{
                    return $this->json(["error"=>["message"=>"internal error"]],500);
                }
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function edit_groupAction($args){
        $id = $args['uid'];
        $group = $this->dbDefault->get("perm_group","*",["ID"=>$id]);
        //----fetch its users and roles
        $userList = $this->dbDefault->select("perm_usergroup(a)",["[><]perm_group(b)"=>["a.GroupID"=>"ID"],"[><]user(c)"=>["a.UserID"=>"id"]],
            "c.id",["b.ID"=>$id]);
        $group["users"] = $userList;
        $roleList = $this->dbDefault->select("perm_userroles(a)",["[><]perm_group(b)"=>["a.UserID"=>"ID"],"[><]perm_roles(c)"=>["a.RoleID"=>"ID"]],
            "c.ID",["b.ID"=>$id]);
        //----iterator the role list to fetch all of their permissions
        $roleids=[];
        foreach ($roleList as $role){
            array_push($roleids, $role);
            $roleChildren = $this->rbac->Roles->descendants($role);
            foreach($roleChildren as $childName=>$child){
                array_push($roleids, $child['ID']);
            }
        }
        if (sizeof($roleids)>0){
            $permList = $this->dbDefault->select("perm_rolepermissions(a)",["[><]perm_roles(b)"=>["a.RoleID"=>"ID"],"[><]perm_permissions(c)"=>["a.PermissionID"=>"ID"]],
                ["c.ID","c.Title","c.Description"],["b.ID"=>$roleids]);
        }else{
            $permList = [];
        }
        $group["roles"] = $roleList;
        $group["perms"] = array_values($permList);
        $users=$this->dbDefault->select("user",["id","userName","loginName"]);
        $roles=$this->dbDefault->select("perm_roles",["ID","Title"]);
        $this->render("admin/groupedit.html.twig",["group"=>$group,"users"=>$users,"roles"=>$roles]);
    }
    
    public function groupinfo_editAction(){
        $args = $this->request->getParams();
        $this->dbDefault->update("perm_group",["Name"=> $args['name'],"Title"=>$args['title']],["ID"=>$args['id']]);
        $this->logger->info("groupid={$args["id"]} info changed by {$this->user->get('loginName')}");
        return $this->json(["msg"=>"success"]);
    }
    
    public function grouprole_editAction(){
        $data = $this->request->getParsedBody();
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_userroles",["UserID"=>$data['id']]);
                    $groupRoleData = [];
                    foreach($data['selected'] as $g){
                        \array_push($groupRoleData,["RoleID"=>$g,"UserID"=>$data['id'],"AssignmentDate"=>time()]);
                    }
                    if (sizeof($groupRoleData)>0){
                        $db->insert("perm_userroles",$groupRoleData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["message"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function groupuser_editAction(){
        $data = $this->request->getParsedBody();
        if (isset($data['type']) && $data['type'] == 'ldap'){
            return $this->json(["error"=>["message"=>"Not allow change this group users list"]],402);
        }
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_usergroup",["GroupID"=>$data['id']]);
                    $groupUserData = [];
                    foreach($data['selected'] as $g){
                        \array_push($groupUserData,["UserID"=>$g,"GroupID"=>$data['id']]);
                    }
                    if (sizeof($groupUserData)>0){
                        $db->insert("perm_usergroup",$groupUserData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["message"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function role_manageAction(){
        $roles = $this->dbDefault->select("perm_roles",["ID(key)","Title(value)"]);
        return $this->render('admin/rolemanage.html.twig',['roles'=>$roles]);
    }
    
    public function get_rolesAction(){
        $params = $this->request->getParams();
        $offset = $params['offset'];
        $limit = $params['limit'];
        $search = isset($params['search'])? $params['search']: null;
        $WHERE = [];
        if (isset($search) && $search !=""){
            $WHERE['OR']=["Title[~]"=>"%{$search}%","Description[~]"=>"%{$search}%"];
        }
        $total = $this->dbDefault->count("perm_roles",$WHERE);
        $WHERE['LIMIT']=[$offset,$limit];
        $roleList=$this->dbDefault->select("perm_roles","*",$WHERE);
        //---collect all of the current page user id to fetch their group set
        $uids=[];
        foreach($roleList as $role){
            \array_push($uids,$role["ID"]);
        }
        $permList = $this->dbDefault->select("perm_rolepermissions(a)",["[><]perm_roles(b)"=>["a.RoleID"=>"ID"],"[><]perm_permissions(c)"=>["a.PermissionID"=>"ID"]],
            ["c.Title","c.Description","b.ID"],["b.ID"=>$uids]);
        foreach($roleList as &$role){
            $role["permissions"]=[];
            foreach ($permList as $perm){
                if ($role["ID"]==$perm["ID"]){//----this permission belong to this role
                    \array_push( $role["permissions"],$perm);
                }
            }
            //----get the node path
            $role['Path']=$this->rbac->Roles->getPath($role['ID']);
        }
        return $this->response->withStatus(200)->withJson(["rows"=>$roleList,"total"=>$total]);
    }
    
    public function add_roleAction(){
        $params = $this->request->getParams();
        $params['Title'] = trim($params['Title']," ");
        //----check role name exists or not
        $id = $this->rbac->Roles->titleId($params['Title']);
        if(isset($id)){
            $this->flash->addMessage("danger","role={$params['Title']} already exists");
        }else{
            $this->rbac->Roles->add($params['Title'],$params['Description'], $params['parent']);
            $this->logger->info("Add a new role={$params['Title']} by user={$this->user->get('loginName')}");
            $this->flash->addMessage("success","Successfully added a new role={$params['Title']}");
        }
        return $this->response->withRedirect($this->router->pathFor('admin-role_manage'));
    }
    
    public function del_roleAction(){
        $params = $this->request->getParsedBody();
        if (isset($params["id"])){
            //---begin transaction delete the usergroup info first and then the user data
            //---remove the role node from the role tree
            //---clean its relation ship
            $this->rbac->Roles->remove($params["id"]);
            $this->dbDefault->action(function($db) use (&$params){
                try{
                    $db->delete("perm_rolepermissions",["RoleID"=>$params['id']]);
                    $db->delete("perm_userroles",["RoleID"=>$params["id"]]);
                    $this->flash->addMessage('success',"Successfully delete the role {$params['name']}");
                    $params["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $params["result"]=false;
                    return false;
                }
            });
                if ($params['result']){
                    return $this->json(["message"=>"successfully deleted"]);
                }else{
                    return $this->json(["error"=>["message"=>"internal error"]],500);
                }
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function edit_roleAction($args){
        $id = $args['uid'];
        if ($id == 1 && $this->user->get('id') !=1){
            return $this->appErrorHandler->error403($this->request,$this->response);
        }
        $role = $this->dbDefault->get("perm_roles","*",["ID"=>$id]);
        //----fetch its directly children.
        $roleChildren = $this->rbac->Roles->descendants($id);
        $roleids=[$id];
        foreach($roleChildren as $childName=>$child){
            array_push($roleids, $child['ID']);
        }
        $permList = $this->dbDefault->select("perm_rolepermissions(a)",["[><]perm_roles(b)"=>["a.RoleID"=>"ID"],"[><]perm_permissions(c)"=>["a.PermissionID"=>"ID"]],
            ["c.ID","c.Title","c.Description","b.ID(roleid)"],["b.ID"=>$roleids]);
        $role["allperm"]=$permList;
        $role["perms"] = [];
        foreach ($permList as $perm){
            if ($perm["roleid"] == $id){
                array_push($role["perms"], $perm["ID"]);
            }
        }
        $children =array_values($roleChildren);
        usort($children, function($a,$b){
            if ($a["Depth"] < $b["Depth"]){
                return false;
            }else if ($a["Depth"] > $b["Depth"]) {
                return true;
            }else{
                if($a["Lft"] <= $b["Lft"]) return false;
                else return true;
            }
        });
            $role["children"] = $children;
            $groupList = $this->dbDefault->select("perm_userroles(a)",["[><]perm_roles(b)"=>["a.RoleID"=>"ID"],"[><]perm_group(c)"=>["a.UserID"=>"ID"]],
                "c.ID",["b.ID"=>$id]);
            $role["groups"] = $groupList;
            $groups=$this->dbDefault->select("perm_group",["ID","Name"]);
            $perms=$this->dbDefault->select("perm_permissions",["ID","Title"]);
            $this->render("admin/roleedit.html.twig",["role"=>$role,"groups"=>$groups,"perms"=>$perms]);
    }
    
    public function roleinfo_editAction(){
        $args = $this->request->getParams();
        //----check if Title already exists
        $count = $this->dbDefault->count("perm_roles",["Title"=>$args['Title']]);
        if ($count>0){
            return $this->json(["error"=>["message"=>"role title exists"]],400);
        }else{
            $this->dbDefault->update("perm_roles",["Title"=> $args['Title'],"Description"=>$args['Description']],["ID"=>$args['id']]);
            $this->logger->info("roleid={$args["id"]} info changed by {$this->user->get('loginName')}");
            return $this->json(["message"=>"success"]);
        }
    }
    
    public function roleperm_editAction(){
        $data = $this->request->getParsedBody();
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_rolepermissions",["RoleID"=>$data['id']]);
                    $rolePermData = [];
                    foreach($data['selected'] as $g){
                        \array_push($rolePermData,["PermissionID"=>$g,"RoleID"=>$data['id'],"AssignmentDate"=>time()]);
                    }
                    if (sizeof($rolePermData)>0){
                        $db->insert("perm_rolepermissions",$rolePermData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["msg"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function rolegroup_editAction(){
        $data = $this->request->getParsedBody();
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_userroles",["RoleID"=>$data['id']]);
                    $roleGroupData = [];
                    foreach($data['selected'] as $g){
                        \array_push($roleGroupData,["UserID"=>$g,"RoleID"=>$data['id']]);
                    }
                    if (sizeof($roleGroupData)>0){
                        $db->insert("perm_userroles",$roleGroupData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["message"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    //----------------start permissions related action--------------------------------
    
    public function perm_manageAction(){
        $perm = $this->dbDefault->select("perm_permissions",["ID(key)","Title(value)"]);
        return $this->render('admin/permmanage.html.twig',['perms'=>$perm]);
    }
    
    public function get_permsAction(){
        $params = $this->request->getParams();
        $offset = $params['offset'];
        $limit = $params['limit'];
        $search = isset($params['search'])? $params['search']: null;
        $WHERE = [];
        if (isset($search) && $search !=""){
            $WHERE['OR']=["Title[~]"=>"%{$search}%","Description[~]"=>"%{$search}%"];
        }
        $total = $this->dbDefault->count("perm_permissions",$WHERE);
        $WHERE['LIMIT']=[$offset,$limit];
        $permList=$this->dbDefault->select("perm_permissions","*",$WHERE);
        //---collect all of the current page user id to fetch their group set
        $uids=[];
        foreach($permList as $perm){
            \array_push($uids,$perm["ID"]);
        }
        $roleList = $this->dbDefault->select("perm_rolepermissions(a)",["[><]perm_permissions(b)"=>["a.PermissionID"=>"ID"],"[><]perm_roles(c)"=>["a.RoleID"=>"ID"]],
            ["c.Title","c.Description","b.ID"],["b.ID"=>$uids]);
        foreach($permList as &$perm){
            $perm["roles"]=[];
            foreach ($roleList as $role){
                if ($role["ID"]==$perm["ID"]){//----this role belong to this perm
                    \array_push( $perm["roles"],$role);
                }
            }
            //----get the node path
            $perm['Path']=$this->rbac->Permissions->getPath($perm['ID']);
        }
        return $this->response->withStatus(200)->withJson(["rows"=>$permList,"total"=>$total]);
    }
    
    public function add_permAction(){
        $params = $this->request->getParams();
        $params['Title'] = trim($params['Title']," ");
        //----check role name exists or not
        $id = $this->rbac->Permissions->titleId($params['Title']);
        if(isset($id)){
            $this->flash->addMessage("danger","permission={$params['Title']} already exists");
        }else{
            $this->rbac->Permissions->add($params['Title'],$params['Description'], $params['parent']);
            $this->logger->info("Add a new permission={$params['Title']} by user={$this->user->get('loginName')}");
            $this->flash->addMessage("success","Successfully added a new role={$params['Title']}");
        }
        return $this->response->withRedirect($this->router->pathFor('admin-perm_manage'));
    }
    
    public function del_permAction(){
        $params = $this->request->getParsedBody();
        if (isset($params["id"])){
            //---begin transaction delete the usergroup info first and then the user data
            //---remove the role node from the role tree
            //---clean its relation ship
            $this->rbac->Permissions->remove($params["id"]);
            $this->dbDefault->delete("perm_rolepermissions",["PermissionID"=>$params['id']]);
            $this->flash->addMessage('success',"Successfully delete the permission {$params['name']}");
            return $this->json(["message"=>"successfully deleted"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
    public function edit_permAction($args){
        $id = $args['uid'];
        if ($id == 1 && $this->user->get('id') !=1){
            return $this->appErrorHandler->error403($this->request,$this->response);
        }
        $perm = $this->dbDefault->get("perm_permissions","*",["ID"=>$id]);
        //----fetch its directly children.
        $permChildren = $this->rbac->Permissions->descendants($id);
        $children =array_values($permChildren);
        usort($children, function($a,$b){
            if ($a["Depth"] < $b["Depth"]){
                return false;
            }else if ($a["Depth"] > $b["Depth"]) {
                return true;
            }else{
                if($a["Lft"] <= $b["Lft"]) return false;
                else return true;
            }
        });
            $perm["children"] = $children;
            $roleList = $this->dbDefault->select("perm_rolepermissions(a)",["[><]perm_permissions(b)"=>["a.PermissionID"=>"ID"],"[><]perm_roles(c)"=>["a.RoleID"=>"ID"]],
                "c.ID",["b.ID"=>$id]);
            $perm["roles"] = $roleList;
            $roles=$this->dbDefault->select("perm_roles",["ID","Title"]);
            $this->render("admin/permedit.html.twig",["perm"=>$perm,"roles"=>$roles]);
    }
    
    public function perminfo_editAction(){
        $args = $this->request->getParams();
        //----check if Title already exists
        $count = $this->dbDefault->count("perm_permissions",["Title"=>$args['Title']]);
        if ($count>0){
            return $this->json(["error"=>["message"=>"permission title exists"]],400);
        }else{
            $this->dbDefault->update("perm_permissions",["Title"=> $args['Title'],"Description"=>$args['Description']],["ID"=>$args['id']]);
            $this->logger->info("permissionid={$args["id"]} info changed by {$this->user->get('loginName')}");
            return $this->json(["message"=>"success"]);
        }
    }
    
    public function permrole_editAction(){
        $data = $this->request->getParsedBody();
        if(!isset($data['selected'])) $data['selected']=[];
        if (isset($data['id'])){
            $this->dbDefault->action(function($db) use (&$data){
                try{
                    $db->delete("perm_rolepermissions",["PermissionID"=>$data['id']]);
                    $rolePermData = [];
                    foreach($data['selected'] as $g){
                        \array_push($rolePermData,["RoleID"=>$g,"PermissionID"=>$data['id'],"AssignmentDate"=>time()]);
                    }
                    if (siezeof($rolePermData)>0){
                        $db->insert("perm_rolepermissions",$rolePermData);
                    }
                    $data["result"]=true;
                }catch (\Exception $e){
                    //---roll back
                    $this->logger->error($e->__toString());
                    $data["result"]=false;
                    return false;
                }
            });
                if (!$data["result"]){
                    return $this->json(["error"=>["message"=>"Internal error"]],500);
                }
                return $this->json(["message"=>"success"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
}