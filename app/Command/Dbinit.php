<?php
/**
 * Author: Shawn Chen
 * Init DB table and basic data records helper command
 */
namespace App\Command;


Class Dbinit extends GenericCommand{
    
    protected  $desc = "Basic DB init command, will check if the user table exists or not and help to reset the admin account";
    protected  $params = ["--sync" => "Check if user table exists if not then help to create it",
                          "--dbEngine" => "The name of db engine in this App"
    ];
    
    
    public function _command($args)
    {
        if (isset($args["sync"]) && isset($args["dbEngine"])){
            $engine = $this->container[$args["dbEngine"]];
            $res = $engine->query("SHOW TABLES LIKE 'user1'")->fetchAll();
            if (isset($res) && \count($res) > 0){
                return "basic table 'user' already exists, no need to init";
            }else{
                $user_ddl = <<<EOF
CREATE TABLE `user1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loginName` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `userName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `firstName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `active` int(1) DEFAULT '1',
  `type` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdBy` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `changedOn` datetime DEFAULT NULL,
  `changedBy` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loginName_UNIQUE` (`loginName`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOF;
                $admin_user_ddl = <<<'EOF'
INSERT INTO `user1` VALUES (1,'admin','Admin','Admin','user','$2y$10$gZ7Uh847Wg.RpUtHcNcl5Oqc8tG63.aGMFgUyI1koa6.Vy2qnSi.S',1,'local','chenwen9@163.com',NULL,NULL,NULL,NULL);
EOF;
                $engine->query($user_ddl);
                $engine->query($admin_user_ddl);
                return "Successfully created user table";
            }
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }

    
    
    
}