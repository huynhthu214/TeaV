/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     4/10/2025 8:56:25 AM                         */
/*==============================================================*/




/*==============================================================*/
/* Table: Categories                                            */
/*==============================================================*/
create table Categories
(
   CategorypId          varchar(10) not null  comment '',
   Name                  varchar(50)  comment '',
   Description           char(50)  comment '',
   primary key (CategorypId)
);

/*==============================================================*/
/* Table: Customers                                             */
/*==============================================================*/
create table Customers
(
   CustomerId           varchar(10) not null  comment '',
   FirstName             varchar(50)  comment '',
   LastName              varchar(50)  comment '',
   Email                 varchar(100)  comment '',
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
   OrderItemId          varchar(10)  comment '',
   Quantity             int  comment '',
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
   ShippingMethod        varchar(100)  comment '',
   PaymentMethod         varchar(100)  comment '',
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
   Name                  varchar(50)  comment '',
   Description           char(50)  comment '',
   Quantity             int  comment '',
   ImgUrl               text  comment '',
   CreatedAt            date  comment '',
   UpdatedAt            date  comment '',
   Status               text  comment '',
   Price                float  comment '',
   Type                 text  comment '',
   Ingredients          text  comment '',
   Usefor               text  comment '',
   primary key (ProductId)
);

/*==============================================================*/
/* Table: Promotions                                            */
/*==============================================================*/
create table Promotions
(
   ProId                varchar(10) not null  comment '',
   Name                  varchar(50)  comment '',
   Description           char(50)  comment '',
   DiscountType          varchar(50)  comment '',
   DiscountValue         varchar(50)  comment '',
   StartDate            date  comment '',
   EndDate              date  comment '',
   Active                varchar(100)  comment '',
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
   Name                  varchar(50)  comment '',
   ContactPp            text  comment '',
   Email                 varchar(100)  comment '',
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

