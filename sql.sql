/*
Created		01/03/2018
Modified		01/03/2018
Project		
Model		
Company		
Author		
Version		
Database		mySQL 5 
*/









Create table Log (
	idLog Int NOT NULL AUTO_INCREMENT,
	idTipoCambio Int NOT NULL,
	idUsuario Int NOT NULL,
	descripcion Varchar(2000) NOT NULL,
    sentenciaSql Varchar(2000),
	fechaCreacion Datetime NOT NULL,
 Primary Key (idLog)) ENGINE = MyISAM;



Create table TipoCambio (
	idTipoCambio Int NOT NULL,
	TipoCambio Varchar(200),
 Primary Key (idTipoCambio)) ENGINE = MyISAM;


Alter table Log add Foreign Key (idUsuario) references users (id) on delete  cascade on update  cascade;
Alter table Log add Foreign Key (idTipoCambio) references TipoCambio (idTipoCambio) on delete  cascade on update  cascade;


INSERT INTO `mezinal_sistema`.`tipocambio`
(`idTipoCambio`,
`TipoCambio`)
VALUES (1,'Alta'),(2,'Eliminación'),(3,'Modificación'),(4,'Consulta');