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

###Test 
INSERT INTO ScoreNodes (Node,Score) VALUES
("9(S)-HODE",1),
("5-HETE",1);


INSERT INTO ImportantNodes (Node) VALUES
("9(S)-HODE"),
("5-HETE");

SELECT * FROM ImportantNodes;

DROP VIEW IF EXISTS tab0;
DROP VIEW IF EXISTS tab1;
DROP VIEW IF EXISTS tab2;
DROP VIEW IF EXISTS tab3;
DROP VIEW IF EXISTS tab4;
DROP VIEW IF EXISTS tab5;
DROP VIEW IF EXISTS tab6;
DROP VIEW IF EXISTS Net;
DROP VIEW IF EXISTS Details;




CREATE VIEW tab0 AS (SELECT Nodename FROM NodeInfo cross join ImportantNodes where LipidMapID = Node);

CREATE VIEW tab1 AS (SELECT Product FROM Network cross join tab0 where (Network.Substrate = Nodename)or (Network.Product= Nodename));
CREATE VIEW tab2 AS (SELECT Substrate FROM Network cross join tab0 where (Network.Substrate = Nodename)or(Network.Product= Nodename));
CREATE VIEW tab3 AS SELECT * from tab1 UNION select * from tab2;

SELECT Network.Substrate,InteractionType,Product FROM Network where (Network.Substrate = ANY(SELECT * from tab3) or Network.Product = ANY(SELECT * from tab3));

CREATE VIEW Net AS SELECT Network.Substrate,InteractionType,Product FROM Network where (Network.Substrate = ANY(SELECT * from tab3) or Network.Product = ANY(SELECT * from tab3));

#Get the List translated

Select distinct Substrate,TypeNode from Net cross join NodeInfo where (Substrate = NodeName) Union Select distinct Product,TypeNode from Net cross join NodeInfo where (Product = NodeName) order by TypeNode;

##Translate ID from lipid maps;
SELECT Nodename FROM NodeInfo cross join ImportantNodes where LipidMapID = Node;


CREATE VIEW tab4 AS SELECT Network.Substrate FROM Network cross join tab3 where (Network.Substrate = tab3.Product)or(Network.Product = tab3.Product);
CREATE VIEW tab5 AS SELECT Network.Product FROM Network cross join tab3 where (Network.Substrate = tab3.Product)or(Network.Product = tab3.Product);
CREATE VIEW tab6 AS SELECT * from tab4 UNION select * from tab5;


###Get the ID that are not found;
SELECT Node FROM ImportantNodes where ( Node not in (SELECT distinct LipidMapID from NodeInfo));
##Get generals
Select Name from Auxiliar where (COMBO  in (SELECT * from PreImportant));







