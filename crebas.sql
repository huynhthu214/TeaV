/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     4/10/2025 12:55:51 AM                        */
/*==============================================================*/


drop table if exists Account;

drop table if exists Product;

/*==============================================================*/
/* Table: Account                                               */
/*==============================================================*/
create table Account
(
   Usename              varchar(50) not null  comment '',
   Password             varchar(20)  comment '',
   primary key (Usename)
);

/*==============================================================*/
/* Table: Product                                               */
/*==============================================================*/
create table Product
(
   Id                   varchar(10) not null  comment '',
   Name                 national varchar(50)  comment '',
   Price                float  comment '',
   Description          text  comment '',
   primary key (Id)
);

