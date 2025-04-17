/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     4/18/2025 12:30:48 AM                        */
/*==============================================================*/


/*==============================================================*/
/* Table: Account                                               */
/*==============================================================*/
create table Account
(
   Email                varchar(100) not null,
   OrderId              varchar(10),
   Password             text,
   CreatedDate          datetime,
   Type                 text,
   IsActive             text,
   primary key (Email)
);

/*==============================================================*/
/* Table: Blog                                                  */
/*==============================================================*/
create table Blog
(
   Title                text,
   DateUpload           datetime,
   ImgLink              text,
   Summary              text,
   BlogId               varchar(10) not null,
   Email                varchar(100),
   Content              text,
   IsShow               text,
   primary key (BlogId)
);

/*==============================================================*/
/* Table: BlogTag                                               */
/*==============================================================*/
create table BlogTag
(
   BlogId               varchar(10) not null,
   TagId                varchar(10) not null,
   primary key (BlogId, TagId)
);

/*==============================================================*/
/* Table: CalculationUnit                                       */
/*==============================================================*/
create table CalculationUnit
(
   UnitId               varchar(10) not null,
   Name                 text,
   primary key (UnitId)
);

/*==============================================================*/
/* Table: Categories                                            */
/*==============================================================*/
create table Categories
(
   CategoryId           varchar(10) not null,
   Name                 varchar(50),
   Description          text,
   primary key (CategoryId)
);

/*==============================================================*/
/* Table: Clients                                               */
/*==============================================================*/
create table Clients
(
   FullName             varchar(50),
   Email                varchar(100) not null,
   AccEmail             varchar(100),
   PhoneNumber          varchar(10),
   primary key (Email)
);

/*==============================================================*/
/* Table: Ingredients                                           */
/*==============================================================*/
create table Ingredients
(
   IngredientId         varchar(10) not null,
   IngreName            text,
   Origin               text,
   primary key (IngredientId)
);

/*==============================================================*/
/* Table: OrderProduct                                          */
/*==============================================================*/
create table OrderProduct
(
   OrderId              varchar(10) not null,
   ProductId            varchar(10) not null,
   Quantity             int,
   primary key (OrderId, ProductId)
);

/*==============================================================*/
/* Table: Orders                                                */
/*==============================================================*/
create table Orders
(
   OrderId              varchar(10) not null,
   VoucherId            varchar(10),
   PaymentId            varchar(10),
   OrderDate            datetime,
   TotalAmount          float,
   PaymentMethod        national varchar(100),
   primary key (OrderId)
);

/*==============================================================*/
/* Table: Payment                                               */
/*==============================================================*/
create table Payment
(
   PaymentId            varchar(10) not null,
   OrderId              varchar(10),
   TotalPrice           float,
   PaymentMethod        varchar(100),
   primary key (PaymentId)
);

/*==============================================================*/
/* Table: Product                                               */
/*==============================================================*/
create table Product
(
   ProductId            varchar(10) not null,
   CategoryId           varchar(10),
   UnitId               varchar(10),
   Name                 varchar(50),
   Description          text,
   Quantity             int,
   ImgUrl               text,
   UpdatedAt            datetime,
   Price                float,
   Usefor               text,
   IsShow               text,
   primary key (ProductId)
);

/*==============================================================*/
/* Table: ProductIngredient                                     */
/*==============================================================*/
create table ProductIngredient
(
   ProductId            varchar(10) not null,
   IngredientId         varchar(10) not null,
   primary key (ProductId, IngredientId)
);

/*==============================================================*/
/* Table: Reaction                                              */
/*==============================================================*/
create table Reaction
(
   ReactionId           varchar(10) not null,
   BlogId               varchar(10),
   Email                varchar(100),
   Comment              text,
   IsShow               text,
   primary key (ReactionId)
);

/*==============================================================*/
/* Table: ReviewProduct                                         */
/*==============================================================*/
create table ReviewProduct
(
   Email                varchar(100) not null,
   ProductId            varchar(10) not null,
   Rating               float,
   Comment              text,
   ReviewDate           datetime,
   primary key (Email, ProductId)
);

/*==============================================================*/
/* Table: Tag                                                   */
/*==============================================================*/
create table Tag
(
   TagId                varchar(10) not null,
   Name                 text,
   primary key (TagId)
);

/*==============================================================*/
/* Table: Vouchers                                              */
/*==============================================================*/
create table Vouchers
(
   VoucherId            varchar(10) not null,
   Name                 varchar(50),
   Description          text,
   DiscountType         national varchar(50),
   DiscountPercent      float,
   StartDate            datetime,
   EndDate              datetime,
   primary key (VoucherId)
);

alter table Account add constraint FK_ACCOUNT_ACCOUNTOR_ORDERS foreign key (OrderId)
      references Orders (OrderId) on delete restrict on update restrict;

alter table Account add constraint FK_ACCOUNT_REGISTER_CLIENTS foreign key (Email)
      references Clients (Email) on delete restrict on update restrict;

alter table Blog add constraint FK_BLOG_WRITE_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table BlogTag add constraint FK_BLOGTAG_BLOGTAG_BLOG foreign key (BlogId)
      references Blog (BlogId) on delete restrict on update restrict;

alter table BlogTag add constraint FK_BLOGTAG_BLOGTAG2_TAG foreign key (TagId)
      references Tag (TagId) on delete restrict on update restrict;

alter table Clients add constraint FK_CLIENTS_REGISTER2_ACCOUNT foreign key (AccEmail)
      references Account (Email) on delete restrict on update restrict;

alter table OrderProduct add constraint FK_ORDERPRO_ORDERPROD_ORDERS foreign key (OrderId)
      references Orders (OrderId) on delete restrict on update restrict;

alter table OrderProduct add constraint FK_ORDERPRO_ORDERPROD_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table Orders add constraint FK_ORDERS_APPLY_VOUCHERS foreign key (VoucherId)
      references Vouchers (VoucherId) on delete restrict on update restrict;

alter table Orders add constraint FK_ORDERS_PAY_PAYMENT foreign key (PaymentId)
      references Payment (PaymentId) on delete restrict on update restrict;

alter table Payment add constraint FK_PAYMENT_PAY2_ORDERS foreign key (OrderId)
      references Orders (OrderId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_CATEGORY_CATEGORI foreign key (CategoryId)
      references Categories (CategoryId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_PRODUCTUN_CALCULAT foreign key (UnitId)
      references CalculationUnit (UnitId) on delete restrict on update restrict;

alter table ProductIngredient add constraint FK_PRODUCTI_PRODUCTIN_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table ProductIngredient add constraint FK_PRODUCTI_PRODUCTIN_INGREDIE foreign key (IngredientId)
      references Ingredients (IngredientId) on delete restrict on update restrict;

alter table Reaction add constraint FK_REACTION_REACT_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table Reaction add constraint FK_REACTION_REACTBLOG_BLOG foreign key (BlogId)
      references Blog (BlogId) on delete restrict on update restrict;

alter table ReviewProduct add constraint FK_REVIEWPR_REVIEWPRO_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table ReviewProduct add constraint FK_REVIEWPR_REVIEWPRO_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

