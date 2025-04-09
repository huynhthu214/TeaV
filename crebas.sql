/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     4/9/2025 4:33:03 PM                          */
/*==============================================================*/


drop table if exists Product;

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

