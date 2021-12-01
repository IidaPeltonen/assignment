-- poistetaan jos löytyy
drop database if exists n0peii00;
-- luodaan uusi
CREATE database n0peii00;
--luodaan taulu tunnus
CREATE table tunnus (id int primary key AUTO_INCREMENT, user varCHAR(50) NOT null, password varchar(150) not null);
--luodaan taulu tiedot
CREATE table tiedot (id int not null, etunimi char(50), sukunimi char(50), email char(100), foreign key (id) references tunnus(id));