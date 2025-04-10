/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     4/10/2025 7:34:40 AM                         */
/*==============================================================*/


alter table OrderDetail 
   drop foreign key FK_ORDERDET_ORDERDETA_PRODUCT;

alter table OrderDetail 
   drop foreign key FK_ORDERDET_ORDERDETA_ORDERS;

alter table Orders 
   drop foreign key FK_ORDERS_ORDER_CUSTOMER;

alter table Product 
   drop foreign key FK_PRODUCT_CATEGORY_CATEGORI;

alter table Product 
   drop foreign key FK_PRODUCT_SUPPLY_SUPPLIER;

alter table Reviews 
   drop foreign key FK_REVIEWS_REVIEW_CUSTOMER;

alter table Reviews 
   drop foreign key FK_REVIEWS_REVIEWED_PRODUCT;

alter table Sale 
   drop foreign key FK_SALE_SALE_PROMOTIO;

alter table Sale 
   drop foreign key FK_SALE_SALE2_PRODUCT;

drop table if exists Categories;

drop table if exists Customers;


alter table OrderDetail 
   drop foreign key FK_ORDERDET_ORDERDETA_PRODUCT;

alter table OrderDetail 
   drop foreign key FK_ORDERDET_ORDERDETA_ORDERS;

drop table if exists OrderDetail;


alter table Orders 
   drop foreign key FK_ORDERS_ORDER_CUSTOMER;

drop table if exists Orders;


alter table Product 
   drop foreign key FK_PRODUCT_CATEGORY_CATEGORI;

alter table Product 
   drop foreign key FK_PRODUCT_SUPPLY_SUPPLIER;

drop table if exists Product;

drop table if exists Promotions;


alter table Reviews 
   drop foreign key FK_REVIEWS_REVIEWED_PRODUCT;

alter table Reviews 
   drop foreign key FK_REVIEWS_REVIEW_CUSTOMER;

drop table if exists Reviews;


alter table Sale 
   drop foreign key FK_SALE_SALE_PROMOTIO;

alter table Sale 
   drop foreign key FK_SALE_SALE2_PRODUCT;

drop table if exists Sale;

drop table if exists Suppliers;

/*==============================================================*/
/* Table: Categories                                            */
/*==============================================================*/
create table Categories
(
   CategorypId          varchar(10) not null  comment '',
   Name                 national varchar(50)  comment '',
   Description          national char(50)  comment '',
   primary key (CategorypId)
);

/*==============================================================*/
/* Table: Customers                                             */
/*==============================================================*/
create table Customers
(
   CustomerId           varchar(10) not null  comment '',
   FirstName            national varchar(50)  comment '',
   LastName             national varchar(50)  comment '',
   Email                national varchar(100)  comment '',
   Password             text  comment '',
   Address              text  comment '',
   RegistrationDate     date  comment '',
   primary key (CustomerId)
);

/*==============================================================*/
/* Table: OrderDetail                                           */
/*==============================================================*/
create table OrderDetail
(
   ProductId            varchar(10) not null  comment '',
   OrderId              varchar(10) not null  comment '',
   Quantity             int  comment '',
   Price                float  comment '',
   primary key (ProductId, OrderId)
);

/*==============================================================*/
/* Table: Orders                                                */
/*==============================================================*/
create table Orders
(
   OrderId              varchar(10) not null  comment '',
   CustomerId           varchar(10)  comment '',
   OrderDate            date  comment '',
   TotalAmount          float  comment '',
   Status               text  comment '',
   ShippingAddress      text  comment '',
   ShippingMethod       national varchar(100)  comment '',
   PaymentMethod        national varchar(100)  comment '',
   TrackingNumber       float  comment '',
   primary key (OrderId)
);

/*==============================================================*/
/* Table: Product                                               */
/*==============================================================*/
create table Product
(
   ProductId            varchar(10) not null  comment '',
   SupplierId           varchar(10) not null  comment '',
   CategorypId          varchar(10)  comment '',
   Name                 national varchar(50)  comment '',
   Description          national char(50)  comment '',
   Quantity             int  comment '',
   ImgUrl               text  comment '',
   CreatedAt            date  comment '',
   UpdatedAt            date  comment '',
   Status               text  comment '',
   primary key (ProductId)
);

/*==============================================================*/
/* Table: Promotions                                            */
/*==============================================================*/
create table Promotions
(
   ProId                varchar(10) not null  comment '',
   Name                 national varchar(50)  comment '',
   Description          national char(50)  comment '',
   DiscountType         national varchar(50)  comment '',
   DiscountValue        national varchar(50)  comment '',
   StartDate            date  comment '',
   EndDate              date  comment '',
   Active               national varchar(100)  comment '',
   primary key (ProId)
);

/*==============================================================*/
/* Table: Reviews                                               */
/*==============================================================*/
create table Reviews
(
   ReviewId             varchar(10) not null  comment '',
   CustomerId           varchar(10)  comment '',
   ProductId            varchar(10) not null  comment '',
   Rating               float  comment '',
   Comment              text  comment '',
   ReviewDate           date  comment '',
   primary key (ReviewId)
);

/*==============================================================*/
/* Table: Sale                                                  */
/*==============================================================*/
create table Sale
(
   ProId                varchar(10) not null  comment '',
   ProductId            varchar(10) not null  comment '',
   primary key (ProId, ProductId)
);

/*==============================================================*/
/* Table: Suppliers                                             */
/*==============================================================*/
create table Suppliers
(
   SupplierId           varchar(10) not null  comment '',
   Name                 national varchar(50)  comment '',
   ContactPp            text  comment '',
   Email                national varchar(100)  comment '',
   Phone                int  comment '',
   primary key (SupplierId)
);

alter table OrderDetail add constraint FK_ORDERDET_ORDERDETA_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table OrderDetail add constraint FK_ORDERDET_ORDERDETA_ORDERS foreign key (OrderId)
      references Orders (OrderId) on delete restrict on update restrict;

alter table Orders add constraint FK_ORDERS_ORDER_CUSTOMER foreign key (CustomerId)
      references Customers (CustomerId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_CATEGORY_CATEGORI foreign key (CategorypId)
      references Categories (CategorypId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_SUPPLY_SUPPLIER foreign key (SupplierId)
      references Suppliers (SupplierId) on delete restrict on update restrict;

alter table Reviews add constraint FK_REVIEWS_REVIEW_CUSTOMER foreign key (CustomerId)
      references Customers (CustomerId) on delete restrict on update restrict;

alter table Reviews add constraint FK_REVIEWS_REVIEWED_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table Sale add constraint FK_SALE_SALE_PROMOTIO foreign key (ProId)
      references Promotions (ProId) on delete restrict on update restrict;

alter table Sale add constraint FK_SALE_SALE2_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

