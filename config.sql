-- poistetaan jos löytyy
drop database if exists n0peii00;
-- luodaan uusi
CREATE database n0peii00;
--luodaan taulu tunnus
CREATE table tunnus (user varCHAR(50) primary key NOT null, password varchar(150) not null);
--luodaan taulu tiedot
CREATE table tiedot (user varCHAR(50), etunimi char(50), sukunimi char(50), email char(100), foreign key (user) references tunnus(user));