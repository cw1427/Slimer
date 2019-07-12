CREATE TABLE IF NOT EXISTS [perm_group] (
  ID INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  Name varchar(255) UNIQUE NOT NULL,
  Title varchar(1024) NOT NULL,
  Type varchar(255) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS [perm_usergroup] (
  GroupID INTEGER NOT NULL,
  UserID INTEGER NOT NULL
) ;

INSERT INTO perm_group (ID, Name, Title, Type) VALUES (1, 'admin', 'admin group', 'local');

INSERT INTO perm_usergroup (GroupID, UserID) VALUES (1, 1);
