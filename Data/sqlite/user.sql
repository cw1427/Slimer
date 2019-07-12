CREATE TABLE user (
  id INTEGER PRIMARY KEY AUTOINCREMENT  NOT NULL,
  loginName varchar(45)   DEFAULT NULL,
  userName varchar(255)   DEFAULT NULL,
  firstName varchar(255)   DEFAULT NULL,
  lastName varchar(255)   DEFAULT NULL,
  password varchar(255)   DEFAULT NULL,
  active int(1) DEFAULT '1',
  type varchar(16)   DEFAULT NULL,
  email varchar(255)   DEFAULT NULL,
  createdOn datetime DEFAULT NULL,
  createdBy varchar(45)   DEFAULT NULL,
  changedOn datetime DEFAULT NULL,
  changedBy varchar(45)   DEFAULT NULL
);
INSERT INTO user VALUES (1,'admin','Admin','Admin','user','$2y$10$gZ7Uh847Wg.RpUtHcNcl5Oqc8tG63.aGMFgUyI1koa6.Vy2qnSi.S',1,'local','chenwen9@163.com',NULL,NULL,NULL,NULL);