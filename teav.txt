/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     5/19/2025 9:29:54 PM                         */
/*==============================================================*/


/*==============================================================*/
/* Table: About                                                 */
/*==============================================================*/
create table About
(
   AboutId              varchar(10) not null,
   Email                varchar(100),
   ImgUrl               text,
   Content              text,
   Title                text,
   DateUpload           datetime,
   IsShow               text,
   primary key (AboutId)
);

/*==============================================================*/
/* Table: Account                                               */
/*==============================================================*/
create table Account
(
   Password             text,
   CreatedDate          datetime,
   Type                 text,
   IsActive             text,
   FullName             varchar(100),
   Email                varchar(100) not null,
   PhoneNumber          varchar(10),
   Address              text,
   DateOfBirth          date,
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
/* Table: Import                                                */
/*==============================================================*/
create table Import
(
   ImportId             varchar(10) not null,
   ImportDate           datetime,
   Note                 text,
   primary key (ImportId)
);

/*==============================================================*/
/* Table: ImportProduct                                         */
/*==============================================================*/
create table ImportProduct
(
   ProductId            varchar(10) not null,
   ImportId             varchar(10) not null,
   DetailId             varchar(10),
   Quantity             int,
   UnitPrice            float,
   primary key (ProductId, ImportId)
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
   Email                varchar(100),
   PaymentId            varchar(10),
   OrderDate            datetime,
   TotalAmount          float,
   StatusOrder          text,
   PaymentStatus        text,
   primary key (OrderId)
);

/*==============================================================*/
/* Table: Payment                                               */
/*==============================================================*/
create table Payment
(
   PaymentId            varchar(10) not null,
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
   SupplierId           varchar(10),
   UnitId               varchar(10),
   Name                 varchar(50),
   Description          text,
   Quantity             int,
   ImgUrl               text,
   UpdatedAt            datetime,
   Price                float,
   Usefor               text,
   IsShow               text,
   SaleOff              float,
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
   Email                varchar(100),
   BlogId               varchar(10),
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
/* Table: Suppliers                                             */
/*==============================================================*/
create table Suppliers
(
   SupplierId           varchar(10) not null,
   SupplierName         text,
   Phone                text,
   Email                text,
   Address              text,
   Note                 text,
   primary key (SupplierId)
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
/* Table: Term                                                  */
/*==============================================================*/
create table Term
(
   TermId               varchar(10) not null,
   Email                varchar(100),
   Title                text,
   Content              text,
   ImgUrl               text,
   DateUpload           datetime,
   IsShow               text,
   primary key (TermId)
);

alter table About add constraint FK_ABOUT_WRITEABOU_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table Blog add constraint FK_BLOG_WRITE_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table BlogTag add constraint FK_BLOGTAG_BLOGTAG_BLOG foreign key (BlogId)
      references Blog (BlogId) on delete restrict on update restrict;

alter table BlogTag add constraint FK_BLOGTAG_BLOGTAG2_TAG foreign key (TagId)
      references Tag (TagId) on delete restrict on update restrict;

alter table ImportProduct add constraint FK_IMPORTPR_IMPORTPRO_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table ImportProduct add constraint FK_IMPORTPR_IMPORTPRO_IMPORT foreign key (ImportId)
      references Import (ImportId) on delete restrict on update restrict;

alter table OrderProduct add constraint FK_ORDERPRO_ORDERPROD_ORDERS foreign key (OrderId)
      references Orders (OrderId) on delete restrict on update restrict;

alter table OrderProduct add constraint FK_ORDERPRO_ORDERPROD_PRODUCT foreign key (ProductId)
      references Product (ProductId) on delete restrict on update restrict;

alter table Orders add constraint FK_ORDERS_ACCOUNTOR_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

alter table Orders add constraint FK_ORDERS_PAY_PAYMENT foreign key (PaymentId)
      references Payment (PaymentId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_CATEGORY_CATEGORI foreign key (CategoryId)
      references Categories (CategoryId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_PRODUCTUN_CALCULAT foreign key (UnitId)
      references CalculationUnit (UnitId) on delete restrict on update restrict;

alter table Product add constraint FK_PRODUCT_PROVIDE_SUPPLIER foreign key (SupplierId)
      references Suppliers (SupplierId) on delete restrict on update restrict;

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

alter table Term add constraint FK_TERM_WRITETERM_ACCOUNT foreign key (Email)
      references Account (Email) on delete restrict on update restrict;

