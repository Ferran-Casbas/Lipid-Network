DROP TABLE IF EXISTS Network;
DROP TABLE IF EXISTS NodeInfo;
DROP TABLE IF EXISTS Auxiliar;

CREATE TABLE NodeInfo (
NodeID INT NOT NULL AUTO_INCREMENT,
NodeName VARCHAR(255) NOT NULL,
TypeNode ENUM('Gene','Reaction Node','Metabolite') NOT NULL,
LipidMapID VARCHAR(255),
CONSTRAINT pk_NodeInfo PRIMARY KEY (NodeID),
CONSTRAINT ck_NodeInfo UNIQUE (NodeName)
);

LOAD DATA INFILE '/home/stxfc1/public_html/Node-INFO-2016-11-24.txt'into table NodeInfo
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '$' 
(NodeName,TypeNode,LipidMapID);


CREATE TABLE Network(
ReactionID INT NOT NULL AUTO_INCREMENT,
Substrate VARCHAR(255) NOT NULL,
InteractionType VARCHAR(255) NOT NULL,
Product VARCHAR(255) NOT NULL,
CONSTRAINT pk_Network PRIMARY KEY (ReactionID)
#CONSTRAINT fk_subs FOREIGN KEY (Substrate) REFERENCES NodeInfo (NodeName),
#CONSTRAINT fk_prod FOREIGN KEY (Product) REFERENCES NodeInfo (NodeName)
);

LOAD DATA INFILE 'C:/Users/user/Desktop/PhD/Python-prog/DATABASEINFO\Network-2016-11-24.txt'into table Network
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '$' 
(Substrate,InteractionType,Product);


CREATE TABLE Auxiliar (
HEAD VARCHAR(255) NOT NULL,
Name VARCHAR(255) NOT NULL,
TOTAL VARCHAR(255) NOT NULL,
COMBO VARCHAR(255) NOT NULL,
CONSTRAINT pk_NodeInfo PRIMARY KEY (Name)
);
LOAD DATA INFILE '/var/lib/mysql-files/Auxiliar-2015-12-14-Corrected.txt'into table Auxiliar
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '$' 
(HEAD,Name,TOTAL,COMBO);



#####// UNTIL HERE IS THE CREATION AND POPULATION OF CORE TABLES


create table ScoreNodes(
Node VARCHAR(95) NOT NULL,
Score float,
CONSTRAINT pk_Node PRIMARY KEY (Node)
);

CREATE TABLE ImportantNodes(
Node VARCHAR(95) NOT NULL,
CONSTRAINT pk_Node PRIMARY KEY (Node)
);

TRUNCATE Table ImportantNodes
TRUNCATE Table ScoreNode;

CREATE TABLE PreImportant(
Node VARCHAR(95) NOT NULL,
CONSTRAINT pk_Node PRIMARY KEY (Node)
);
CREATE TABLE PreImportante(
Node VARCHAR(95) NOT NULL,
CONSTRAINT pk_Node PRIMARY KEY (Node)
);









