DROP TABLE IF EXISTS "basic_fields";
CREATE TABLE "basic_fields" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "type" VARCHAR, "label" VARCHAR, "attributes" VARCHAR, "db_type" VARCHAR, "machine_type" VARCHAR);
INSERT INTO "basic_fields" VALUES(1,'text','Text','size:60;maxlength:128','VARCHAR','text');
INSERT INTO "basic_fields" VALUES(2,'textarea','Textbox','cols:30;rows:15;wrap:off','TEXT','textarea');
INSERT INTO "basic_fields" VALUES(3,'text','Dátum a čas','size:19;maxlength:19','DATETIME','datetime');
INSERT INTO "basic_fields" VALUES(4,'text','Dátum','size:10;maxlength:10','DATETIME','date');
INSERT INTO "basic_fields" VALUES(5,'text','Číslo','size:10;maxlength:20;min:0;max:0','NUMERIC','number');
INSERT INTO "basic_fields" VALUES(6,'select','Zoznam',NULL,'TEXT','select');
INSERT INTO "basic_fields" VALUES(7,'checkbox','Zaškrtávacie pole',NULL,'BOOL','checkbox');
INSERT INTO "basic_fields" VALUES(8,'checkboxgroup','Skupina zaškrtávacích polí','polozka1:popis1;polozka2:popis2','TEXT','checkboxgroup');
INSERT INTO "basic_fields" VALUES(9,'radio','Voľby','polozka1:popis1;polozka2:popis2','TEXT','radio');
INSERT INTO "basic_fields" VALUES(10,'file','Súbor','max_file_size:2;max_files:0;order_by:description','VARCHAR','file');
INSERT INTO "basic_fields" VALUES(11,'file','Obrázok','max_file_size:2;max_files:0;preview_size:640;icon_size:175;thumb_create:cut;order_by:description','VARCHAR','image');
DROP TABLE IF EXISTS "cache";
CREATE TABLE "cache" ("filename" VARCHAR(128), "request" VARCHAR(128));
INSERT INTO "cache" VALUES('13054835474dd0191ba70fd.css','css');
DROP TABLE IF EXISTS "content";
CREATE TABLE "content" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "content_type_id" INTEGER, "content_type_name" VARCHAR, "content_type_machine_name" VARCHAR, "content_id" INTEGER, "path_alias" VARCHAR, "last_update" DATETIME, "content_title" VARCHAR, "uid" INTEGER DEFAULT 0, "edit_uid" INTEGER DEFAULT 0, "lang" VARCHAR(2), "module" VARCHAR(128) DEFAULT 0);
INSERT INTO "content" VALUES(1,1,'Stránka','page',1,'hlavna-stranka','2011-07-07 08:42:48','Blahoželáme',0,1,'sk','0');
INSERT INTO "content" VALUES(2,1,'Stránka','page',2,'texy','2011-07-07 08:04:25','TEXY! je SEXY!',0,1,'sk','0');
DROP TABLE IF EXISTS "content_files";
CREATE TABLE "content_files" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "content_type_id" INTEGER DEFAULT 0, "file_name" VARCHAR(128) DEFAULT null, "description" VARCHAR(256) DEFAULT null, "datetime"  DEFAULT CURRENT_TIMESTAMP, "content_id" INTEGER DEFAULT 0, "priority" INTEGER DEFAULT 0, "file_type" VARCHAR);
DROP TABLE IF EXISTS "content_images";
CREATE TABLE "content_images" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "content_type_id" INTEGER DEFAULT 0, "image_name" VARCHAR(128) DEFAULT null, "description" VARCHAR(256) DEFAULT null, "datetime" DATETIME DEFAULT CURRENT_TIMESTAMP, "content_id" INTEGER DEFAULT 0, "priority" INTEGER DEFAULT 0);
DROP TABLE IF EXISTS "content_seo";
CREATE TABLE `content_seo` (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		  `cid` INTEGER,
		  `keywords` VARCHAR(256),
		  `description` VARCHAR(256),
		  `robots` VARCHAR(128));
INSERT INTO "content_seo" VALUES(1,1,'','','');
INSERT INTO "content_seo" VALUES(2,2,'','','');
DROP TABLE IF EXISTS "content_type_fields";
CREATE TABLE "content_type_fields" ("id" INTEGER PRIMARY KEY  NOT NULL ,"type" VARCHAR,"label" VARCHAR,"attributes" VARCHAR,"content_type_id" INTEGER,"priority" INTEGER DEFAULT 0 ,"default" VARCHAR, "frm_name" VARCHAR, "required" BOOL DEFAULT 0, "basic" BOOL DEFAULT 1, "field_type" VARCHAR, "description" TEXT, "machine_field_type" VARCHAR DEFAULT null, "content_label" VARCHAR, "editable" BOOL DEFAULT 1);
INSERT INTO "content_type_fields" VALUES(1,'text','Titulok','a:2:{s:4:"size";s:2:"60";s:9:"maxlength";s:3:"128";}',1,0,'','title',1,1,'Text','','text','',0);
INSERT INTO "content_type_fields" VALUES(2,'textarea','Telo','a:3:{s:4:"cols";s:2:"50";s:4:"rows";s:2:"30";s:4:"wrap";s:3:"off";}',1,2,'','body',0,1,'Textbox','','textarea','',1);
INSERT INTO "content_type_fields" VALUES(3,'text','URL alias','a:2:{s:4:"size";s:2:"60";s:9:"maxlength";s:3:"128";}',1,1,'','path_alias',0,1,'Text','','text','',0);
INSERT INTO "content_type_fields" VALUES(4,'text','Titulok','a:2:{s:4:"size";s:2:"60";s:9:"maxlength";s:3:"128";}',2,0,'','title',1,1,'Text','','text','',0);
INSERT INTO "content_type_fields" VALUES(5,'textarea','Telo','a:3:{s:4:"cols";s:2:"50";s:4:"rows";s:2:"30";s:4:"wrap";s:3:"off";}',2,2,'','body',0,1,'Textbox','','textarea','',1);
INSERT INTO "content_type_fields" VALUES(6,'text','URL alias','a:2:{s:4:"size";s:2:"60";s:9:"maxlength";s:3:"128";}',2,1,'','path_alias',0,1,'Text','','text','',0);
INSERT INTO "content_type_fields" VALUES(7,'file','Obrázok','a:6:{s:13:"max_file_size";s:1:"2";s:9:"max_files";s:1:"0";s:8:"order_by";s:11:"description";s:12:"preview_size";s:3:"640";s:9:"icon_size";s:3:"175";s:12:"thumb_create";s:3:"cut";}',2,3,'','image',0,0,'Obrázok','','image','',1);
INSERT INTO "content_type_fields" VALUES(8,'file','Súbor','a:3:{s:13:"max_file_size";s:1:"2";s:9:"max_files";s:1:"0";s:8:"order_by";s:11:"description";}',2,4,'','file_box',0,0,'Súbor','','file','',1);
INSERT INTO "content_type_fields" VALUES(9,'textarea','Prev','a:3:{s:4:"cols";s:2:"30";s:4:"rows";s:1:"3";s:4:"wrap";s:3:"off";}',2,5,'','prev',0,0,'Textbox','','textarea','',1);
INSERT INTO "content_type_fields" VALUES(10,'textarea','Next','a:3:{s:4:"cols";s:2:"30";s:4:"rows";s:1:"3";s:4:"wrap";s:3:"off";}',2,6,'','next',0,0,'Textbox','','textarea','',1);
DROP TABLE IF EXISTS "content_types";
CREATE TABLE "content_types" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "name" VARCHAR, "description" TEXT, "machine_name" VARCHAR);
INSERT INTO "content_types" VALUES(1,'Stránka','Jednoduchý formulár s titulkom a obsahom','page');
INSERT INTO "content_types" VALUES(2,'Testovací typ 2','','test_type');
DROP TABLE IF EXISTS "development";
CREATE TABLE "development" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "constant" VARCHAR, "title" VARCHAR, "description" TEXT, "value" VARCHAR, "frm_name" TEXT);
INSERT INTO "development" VALUES(1,'CACHE_PAGES','FRM_DEV_CACHE_PAGES_TITLE','FRM_DEV_CACHE_PAGES_DESC','1','cache_pages');
INSERT INTO "development" VALUES(2,'CACHE_CSS','FRM_DEV_CACHE_CSS_TITLE','FRM_DEV_CACHE_CSS_DESC','1','cache_css');
INSERT INTO "development" VALUES(3,'CACHE_JS','FRM_DEV_CACHE_JS_TITLE','FRM_DEV_CACHE_JS_DESC','0','cache_js');
INSERT INTO "development" VALUES(4,'DEVELOPMENT','FRM_DEV_ENABLE_TITLE','FRM_DEV_ENABLE_DESC','1','dev_enable');
INSERT INTO "development" VALUES(5,'SQL_BROWSER','FRM_DEV_SQL_TRACKER_TITLE','FRM_DEV_SQL_TRACKER_DESC','0','sql_tracker');
DROP TABLE IF EXISTS "filter";
CREATE TABLE "filter" ("uid" INTEGER, "content_type" VARCHAR, "category" VARCHAR, "language" VARCHAR);
DROP TABLE IF EXISTS "languages";
CREATE TABLE "languages" ("langid" VARCHAR, "name" VARCHAR, "eng_machine_name" VARCHAR, "main_page_path" VARCHAR, "main_lang" BOOL DEFAULT 0, "active" BOOL DEFAULT 0, "system" BOOL DEFAULT 0);
INSERT INTO "languages" VALUES('sk','Slovenčina','slovak','hlavna-stranka',1,1,1);
INSERT INTO "languages" VALUES('en','English','english','welcome',0,1,0);
DROP TABLE IF EXISTS "menu_items";
CREATE TABLE "menu_items" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "title" VARCHAR, "path" VARCHAR, "allowed" BOOL DEFAULT 0, "expanded" BOOL DEFAULT 0, "priority" INTEGER, "child_of" INTEGER DEFAULT 0, "menu_id" INTEGER, "name" VARCHAR DEFAULT null, "image" VARCHAR DEFAULT null, "content_id" INTEGER DEFAULT 0, "module" VARCHAR);
INSERT INTO "menu_items" VALUES(8,'System','admin/system',1,1,0,0,4,'System',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(9,'Obsah','admin/content',1,1,2,0,4,'Obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(10,'Užívatelia','admin/users',1,1,3,0,4,'Užívatelia',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(11,'Nastavenie stránky','admin/pageSettings',1,0,0,8,4,'Nastavenie stránky',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(12,'Menu','admin/menus',1,0,1,8,4,'Menu',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(13,'Zoznam','admin/content/list',1,0,0,9,4,'Zoznam',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(14,'Typy obsahu','admin/contentTypes',1,0,1,9,4,'Typy obsahu',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(15,'Zoznam','admin/users/list',1,0,0,10,4,'Zoznam',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(16,'Pridať užívateľa','admin/users/add',1,0,1,10,4,'Pridať užívateľa',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(17,'Môj detajl','admin/users/detail',1,0,2,10,4,'Môj detajl',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(18,'Pridat obsah','admin/content/add',1,0,2,9,4,'Pridat obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(65,'Oprávnenia','admin/permisions',1,0,4,0,4,'Oprávnenia','',0,NULL);
INSERT INTO "menu_items" VALUES(66,'Moduly','admin/modules',1,1,1,0,4,'Moduly','',0,NULL);
INSERT INTO "menu_items" VALUES(67,'Zoznam','admin/modules/list',1,0,0,66,4,'Zoznam','',0,NULL);
INSERT INTO "menu_items" VALUES(77,'Ponuky (Menu)','admin/menus',1,0,0,0,5,'Ponuky (Menu)',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(78,'Moduly','admin/modules',1,1,0,0,5,'Moduly','',0,NULL);
INSERT INTO "menu_items" VALUES(79,'Obsah','admin/content',1,1,0,0,5,'Obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(80,'Môj detajl','admin/users/detail',1,0,0,0,5,'Môj detajl',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(83,'Zoznam','admin/content/list',1,0,0,79,5,'Zoznam',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(84,'Typy obsahu','admin/contentTypes',1,0,1,79,5,'Typy obsahu',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(85,'Pridať obsah','admin/content/add',1,0,2,79,5,'Pridať obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(99,'Môj obsah','admin/content/list',1,0,0,0,6,'Môj obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(100,'Pridať obsah','admin/content/add',1,0,0,0,6,'Pridať obsah',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(101,'Môj detajl','admin/users/detail',1,0,0,0,6,'Môj detajl',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(120,'Jazyky','admin/languages',1,0,3,8,4,'Jazyky',NULL,0,NULL);
INSERT INTO "menu_items" VALUES(134,'Hlavná stránka','hlavna-stranka',1,0,0,0,1,'Hlavná stránka','',1,'content');
INSERT INTO "menu_items" VALUES(190,'Texy','texy',1,0,0,0,1,'Texy','',2,'content');
INSERT INTO "menu_items" VALUES(209,'Texyla','texyla/admin',1,0,0,66,4,'Texyla','',0,NULL);
INSERT INTO "menu_items" VALUES(213,'Test','<basepath>',1,0,0,0,1,'Test','',0,NULL);
INSERT INTO "menu_items" VALUES(214,'Web formulár','webform/admin',1,0,0,66,4,'Web formulár','',0,NULL);
INSERT INTO "menu_items" VALUES(215,'Web formulár','webform/admin',1,0,0,78,5,'Web formulár','',0,NULL);
INSERT INTO "menu_items" VALUES(216,'Vývoj / Výkon','admin/development',1,0,4,8,4,'Vývoj / Výkon','',0,NULL);
DROP TABLE IF EXISTS "menus";
CREATE TABLE "menus" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "name" VARCHAR, "machine_name" VARCHAR, "system" BOOL, "only_admin" BOOL DEFAULT 0, "lang" VARCHAR DEFAULT sk);
INSERT INTO "menus" VALUES(1,'Navigácia','navigation',1,0,'sk');
INSERT INTO "menus" VALUES(2,'Primárne menu','primary_menu',1,0,'none');
INSERT INTO "menus" VALUES(3,'Sekundárne menu','secondary_menu',1,0,'sk');
INSERT INTO "menus" VALUES(4,'Administrácia','administration',1,1,'none');
INSERT INTO "menus" VALUES(5,'Menu editora','editors_menu',1,1,'sk');
INSERT INTO "menus" VALUES(6,'Menu užívateľa','users_menu',1,1,'sk');
DROP TABLE IF EXISTS "modules";
CREATE TABLE "modules" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT,"name" VARCHAR,"version" TEXT,"machine_name" VARCHAR,"installed" BOOL, "description" TEXT, "standalone" BOOL DEFAULT 0, "content_extension" BOOL DEFAULT 0, "module_extension" VARCHAR(128), "application" BOOL DEFAULT 0);
INSERT INTO "modules" VALUES(7,'Web formulár','v1.00','webform',1,'Modul web formulár Vám umožnuje vytvoriť užívateľský formulár a návševníci vašej stránky cez neho môžu odoslať správu na váš email.
                    Po inštalácii sa v administračnom menu -> moduly vytvorí odkaz Web formulár.',1,0,'0',0);
INSERT INTO "modules" VALUES(9,'Texyla','v1.00','texyla',1,'jQuery WYSIWYM (What You See Is What You Mean) editor pre knižnicu TEXY. Možnosť nastaviť na všetky alebo konkrétne textarey. Vyžaduje inštaláciu jQuery UI.',0,0,'0',1);
INSERT INTO "modules" VALUES(12,'Simple SEO','v1.00','simpleseo',1,'Modul pridá ku kontentu keywords a description meta značky',0,1,'0',0);
DROP TABLE IF EXISTS "page";
CREATE TABLE "page" ("id" INTEGER PRIMARY KEY  NOT NULL ,"title" VARCHAR,"body" TEXT);
INSERT INTO "page" VALUES(1,'Blahoželáme','Ak čítate tento text, tak ste najpravdepodobnejšie úspešne nainštalovali CMS
(systém na správu obsahu) LitePAGE.

Systém je budovaný na prácu s databázou SQLite 3, ale nakoľko využíva PHP rozšírenie
PDO, ktoré je jednotné pre viacero druhov databáz. Systém je ľahko prekonfigurovateľný.

Toto však nie je cieľom. Ako názov LitePAGE naznačuje, tento CMS je určený
hlavne na vybudovanie webových prezentácií, ktoré nepotrebujú mohutné databázy,
a ich obsah je poväčšine statický. Systém LitePAGE ponúka rozhranie, ktoré má
zjednodušiť a hlavne urýchliť vybudovanie webovej prezentácie.

Na formátovanie textu v podstránkach slúži textový parser TEXY. Základný syntax
si môžete pozrieť \"tu\":[files/texy_zakladny_syntax.txt] a porovnajte si to
s preloženým dokumentom \"tu\":[texy_syntax]. Kompletný návod, ako používať TEXY
 nájdete na \"oficiálnej stránke TEXY\":[http://texy.info/cs/syntax-podrobne].

Do administrácie stránky sa dostanete tak, že za hlavnú adresu Vašej stránky
napíšete slovko: //login//, alebo kliknete na znak © v pätičke stránky.
');
INSERT INTO "page" VALUES(2,'TEXY! je SEXY!','Základné formátovanie v TEXTY
#############################

TEXY je výborný nástroj na formátovanie textu na webových stránkach. Hlavnou devízou je to, že pisateľ
nepotrebuje ovládať značkovací jazyk HTML použivaný na webových stránkach, ale stačí mu písať jednoducho
formátovaný text, podobný tomu v príkladoch nižšie a TEXY sa postará o správny prevod do HTML.

**POZNÁMKA:** TEXY je jednosmerný nástroj, čiže nedokáže konvertovať HTML na TEXY formát, preto je vhodné
si vaše výtvory uchovávať v nejakom TXT súbore v znakovej sade UTF-8 a prípade úpravy obsahu stránky, len nahradíte celý obsah z TEXY súboru.

Toto je hlavný titulok
######################

Toto je podtitulok
******************

Toto by mal byť tretí titulok
=============================

A toto 4. titulok
-----------------

**Úprava Textu**

Toto je nejaký text a **toto je nejaký hrubý text**
 //a to je text v kurzíve na novom riadku//
 text zalomíme tak, že na začiatok riadku
 dáme medzeru.

Nasledujúci text bude odsadený:
> Tento text je odsadený
> Text odsadíme tak, že na začiatku riadku dáme > a medzeru.

Citáciu označíme takto:
 Hamlet povedal: >>Byť, či nebyť...<<


Čiary sa zobrazujú jednoducho:

-----

alebo

*****

Môžeš zobraziť aj zoznam:
- položka 1
- položka 2
- položka 3

alebo

1) položka 1
2) položka 2
3) položka 3

alebo vnorené zoznamy:
1) Položka jedna
   - podpoložka 1
   - podpoložka 2
2) Položka dva
   - podpoložka 1
   - podpoložka 2');
DROP TABLE IF EXISTS "page_settings";
CREATE TABLE "page_settings" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "constant" VARCHAR, "title" VARCHAR, "description" TEXT, "value" VARCHAR, "frm_name" TEXT);
INSERT INTO "page_settings" VALUES(1,'LOGO','Logo','Názov súboru obrázku loga umiestnený v adresary images. Ked nechcete logo zobrazovať, zadajte prázdne políčko.','logo.png','logo');
INSERT INTO "page_settings" VALUES(2,'PAGE_TITLE','Hlavný titulok stránky','Titulok, ktorý sa bude zobrazovať záhlavý prehliadača.','Lite PAGE','title');
INSERT INTO "page_settings" VALUES(3,'MAIN_TEMPLATE','Hlavná šablóna','Šablóna s rozložením stránky, ktorá je umiestnená v adresáry /app/templates/','1-2-1-left-fix.tpl','template');
INSERT INTO "page_settings" VALUES(4,'PAGE_SLOGAN','Slogan stránky','Slogan stránky, ktorý sa bude zobrazovať v hlavičke stránky, jeho polohu, je možné meniť cez CSS. Ak si slogan neželáte, tak uložte políčko prázdne','systém na správu obsahu na databáze SQLite','slogan');
INSERT INTO "page_settings" VALUES(5,'HEADER_TITLE','Titulok záhlavia','Titulok zobrazený v hlavičke stránky. Ak nechcete titulok zobrazovať, uložte políčko prázdne.','Lite PAGE','header_title');
INSERT INTO "page_settings" VALUES(6,'FANCY_LOGIN_FORM','Prihlasovací fomulár vo FancyBoxe','Blok s prihlasovacím formulárom môžete pridať do stránky pomocou funkcie &lt;?block::get(''login_form'')?>. Ak zvolíte túto variantu - zobrazovanie vo FancyBoxe, tak možete umiestniť blok kdekoľvek do stránky a vyvolať prihlasovací formulár po kliknutí na odkaz: http://vasa_stranka/#login-form.
Tag <b>a</b> musi obsahovat triedu <i>login-form</i>','1','fancy_login_form');
INSERT INTO "page_settings" VALUES(7,'META_KEYWORDS','Keywords','Kľúčové slová (SEO)','','meta_keywords');
INSERT INTO "page_settings" VALUES(8,'META_DESCRIPTION','Description','Popis stránky (SEO)','','meta_description');
INSERT INTO "page_settings" VALUES(9,'META_ROBOTS','Robots','Vyhľadávače (SEO)','index, follow','meta_robots');
INSERT INTO "page_settings" VALUES(10,'META_GOOGLE_SITE_VERIFICATION','Google Site Verification Code','Kontrolný kód Google','','meta_google_site_verification');
INSERT INTO "page_settings" VALUES(11,'META_GOOGLE_ANALYTICS','Google Analytics Code','Kód Google Analytics','','meta_google_analytics');
INSERT INTO "page_settings" VALUES(12,'META_AUTHOR','Author','Autor (META)','MBM ARTWORKS (info@mbmartworks.sk - www.mbmartworks.sk)','meta_author');
INSERT INTO "page_settings" VALUES(13,'META_COPYRIGHT','Copyright','Autorské práva (META)','MBM ARTWORKS, v.o.s.','meta_copyright');
INSERT INTO "page_settings" VALUES(14,'META_RATING','Rating','Rating','general','meta_rating');
DROP TABLE IF EXISTS "path_aliases";
CREATE TABLE "path_aliases" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "path_alias" VARCHAR, "url" VARCHAR, "module" VARCHAR, "mod_id" INTEGER);
INSERT INTO "path_aliases" VALUES(8,'hlavna-stranka','content/show/1','content',1);
INSERT INTO "path_aliases" VALUES(24,'texy','content/show/2','content',2);
INSERT INTO "path_aliases" VALUES(25,'welcome','content/show/1','content',1);
DROP TABLE IF EXISTS "permisions";
CREATE TABLE "permisions" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , "content" VARCHAR, "cont_mach_name" VARCHAR, "editor_add" BOOL DEFAULT 1, "editor_edit" BOOL DEFAULT 1, "editor_delete" BOOL DEFAULT 0, "editor_view" BOOL DEFAULT 1, "user_add" BOOL DEFAULT 0, "user_edit" BOOL DEFAULT 0, "user_delete" BOOL DEFAULT 0, "user_view" BOOL DEFAULT 1, "visitor_add" BOOL DEFAULT 0, "visitor_edit" BOOL DEFAULT 0, "visitor_delete" BOOL DEFAULT 0, "visitor_view" BOOL DEFAULT 1);
INSERT INTO "permisions" VALUES(1,'Užívatelia','users',0,0,0,1,0,0,0,0,0,0,0,0);
INSERT INTO "permisions" VALUES(2,'Typy obsahu','content_types',0,0,0,1,0,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(3,'Menu','menus',0,0,0,1,0,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(4,'Položky menu','menu_items',1,1,1,1,0,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(5,'Stránka','page',1,1,0,1,0,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(7,'Produkty','products',1,1,1,1,0,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(8,'Obsah','content',1,0,0,1,1,0,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(32,'Jazyky','languages',0,0,0,1,0,0,0,0,0,0,0,0);
INSERT INTO "permisions" VALUES(40,'Testovací typ 2','test_type',1,1,1,1,1,1,0,1,0,0,0,1);
INSERT INTO "permisions" VALUES(60,'Texyla','texyla',0,0,0,0,0,0,0,0,0,0,0,0);
INSERT INTO "permisions" VALUES(62,'Web formulár','webform',1,1,1,1,0,0,0,1,0,0,0,1);
DROP TABLE IF EXISTS "temp_content";
CREATE TABLE temp_content(
  id INTEGER,
  content_type_id INTEGER,
  content_type_name VARCHAR,
  content_type_machine_name VARCHAR,
  content_id INTEGER,
  path_alias VARCHAR,
  last_update DATETIME,
  content_title VARCHAR,
  uid INTEGER,
  edit_uid INTEGER,
  lang VARCHAR(2)
);
INSERT INTO "temp_content" VALUES(1,1,'Stránka','page',1,'hlavna-stranka','2010-08-24 13:47:30','Blahoželáme',0,1,'sk');
INSERT INTO "temp_content" VALUES(2,1,'Stránka','page',2,'texy','2010-11-27 13:07:39','TEXY! je SEXY!',0,1,'sk');
DROP TABLE IF EXISTS "test";
CREATE TABLE "test" ("date" TIMESTAMP DEFAULT CURRENT_TIMESTAMP, "t" INTEGER);
INSERT INTO "test" VALUES('2010-08-06 12:09:41',1);
DROP TABLE IF EXISTS "test_type";
CREATE TABLE 'test_type' 
												  (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
												   title VARCHAR(128),
												   body TEXT, 'prev' 'TEXT', 'next' 'TEXT');
DROP TABLE IF EXISTS "texyla";
CREATE TABLE texyla (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `textarea` VARCHAR(128),
			  `description` TEXT);
INSERT INTO "texyla" VALUES(1,'#formContentEdit-body','');
DROP TABLE IF EXISTS "texyla_settings";
CREATE TABLE `texyla_settings` (
			  `texyla_id` INTEGER,
			  `role` VARCHAR(20),
			  `allow` BOOL DEFAULT 0,
			  `texyCfg` VARCHAR(50),
			  `bottomLeftToolbarEdit` BOOL DEFAULT 1,
			  `bottomLeftToolbarPreview` BOOL DEFAULT 1,
			  `bottomLeftToolbarHtmlPreview` BOOL DEFAULT 0,
			  `buttonType` VARCHAR(20),
			  `tabs` BOOL DEFAULT 1,
			  `headers` BOOL DEFAULT 0,
			  `font_style` BOOL DEFAULT 1,
			  `text_align` BOOL DEFAULT 1,
			  `lists` BOOL DEFAULT 1,
			  `link` BOOL DEFAULT 1,
			  `img` BOOL DEFAULT 1,
			  `table` BOOL DEFAULT 1,
			  `emoticon` BOOL DEFAULT 1,
			  `symbol` BOOL DEFAULT 1,
			  `color` BOOL DEFAULT 1,
			  `textTransform` BOOL DEFAULT 1,
			  `blocks` BOOL DEFAULT 1,
			  `codes` BOOL DEFAULT 1,
			  `others` BOOL DEFAULT 1);
INSERT INTO "texyla_settings" VALUES(1,'admin',1,'admin',1,1,1,'span',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
INSERT INTO "texyla_settings" VALUES(1,'editor',1,'admin',1,1,0,'button',0,1,1,1,1,1,0,0,0,1,0,0,0,0,0);
INSERT INTO "texyla_settings" VALUES(1,'user',1,'admin',1,1,0,'button',0,0,1,0,0,0,0,0,0,1,0,0,0,0,0);
INSERT INTO "texyla_settings" VALUES(1,'visitor',0,'admin',1,1,0,'button',0,0,1,0,0,0,0,0,0,1,0,0,0,0,0);
DROP TABLE IF EXISTS "users";
CREATE TABLE "users" ("id" INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL  UNIQUE , "user" VARCHAR, "password" VARCHAR, "role" VARCHAR, "name" VARCHAR, "surname" VARCHAR, "last_login" DATETIME, "session_id" VARCHAR);
INSERT INTO "users" VALUES(1,'admin','35ae32e31c965f718f3268fe48931eb2','admin','Branislav','Zvolenský','2011-07-02 19:28:43','cl4m14odn7h0los54mvn8fffi1');
INSERT INTO "users" VALUES(3,'editor','5aee9dbd2a188839105073571bee1b1f','editor','','','2010-10-24 20:27:23','XXX');
INSERT INTO "users" VALUES(4,'user','ee11cbb19052e40b07aac0ca060c23ee','user','','','2010-08-25 20:36:33','XXX');
DROP TABLE IF EXISTS "users_permisions";
CREATE TABLE `users_permisions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  `uid` INTEGER,
  `name` varchar(256),
  `machine_name` varchar(256),
  `view` BOOL DEFAULT '0',
  `add` BOOL DEFAULT '0',
  `edit` BOOL DEFAULT '0',
  `delete` BOOL DEFAULT '0'
);
DROP TABLE IF EXISTS "webform";
CREATE TABLE webform (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `title` VARCHAR(128),
			  `form_title` VARCHAR(128),
			  `body` TEXT,
			  `path_alias` VARCHAR(128),
			  `form_after_text` BOOL,
			  `hide_on_load` BOOL,
			  `email` VARCHAR(256),
			  `lang` VARCHAR(4),
			  `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO "webform" VALUES(1,'Testovací formulár','','','',0,0,'','sk','2010-11-28 08:46:34');
DROP TABLE IF EXISTS "webform_fields";
CREATE TABLE `webform_fields` (
	          `id` INTEGER PRIMARY KEY  NOT NULL ,
			  `webform_id` INTEGER,
			  `type` VARCHAR(256) ,
			  `label` VARCHAR(256) ,
			  `webform_label` VARCHAR(256) ,
			  `attributes` VARCHAR(256) ,
			  `priority` INTEGER DEFAULT 0 ,
			  `default` VARCHAR(256) ,
			  `frm_name` VARCHAR(256) ,
			  `required` BOOL DEFAULT 0,
			  `email` BOOL DEFAULT 1,
			  `field_type` VARCHAR(256) ,
			  `description` TEXT,
			  `machine_field_type` VARCHAR(256)  DEFAULT null);
INSERT INTO "webform_fields" VALUES(1,1,'text','filled','','size:60;maxlength:128',1,'','test_filled',1,1,'Text','','text');
INSERT INTO "webform_fields" VALUES(2,1,'text','number','','size:10;maxlength:20;min:0;max:0',2,'','test_number',1,1,'Číslo','','number');
DROP TABLE IF EXISTS "webform_messages";
CREATE TABLE webform_messages (
	          `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			  `webform_id` INTEGER,
			  `html_content` TEXT,
			  `datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
DROP TABLE IF EXISTS "webform_messages_settings";
CREATE TABLE webform_messages_settings (
	          `constant` VARCHAR(50),
			  `title` VARCHAR(50),
			  `description` TEXT,
			  `value` VARCHAR(256) ,
			  `frm_name` VARCHAR(256) );
INSERT INTO "webform_messages_settings" VALUES('WF_MESSAGES_PER_PAGE','Počet správ na stránku','Stránkovanie správ v histórii správ','10','messages_per_page');
INSERT INTO "webform_messages_settings" VALUES('WF_ORDER','Zoradenie','Spôsob zoradenia správ','ASC','order');
INSERT INTO "webform_messages_settings" VALUES('WF_NOTIFICATE','Email','Zadajte email, na ktorý budú chodiť správy. Ak email nezadáte správy sa nebudú posielať a budú len uložené do databázy.','info@mbmartworks.sk','notificate');
INSERT INTO "webform_messages_settings" VALUES('WF_AUTO_CLEAN','Automatické mazanie starých správ','Ak zaškrtnete túto voľbu, staré správy sa budú automaticky mazať z databázy. Týmto zabezbečíte to, že databáza bude stále udržovaná a jej načítanie bude rýchlejše.','0','auto_clean');
INSERT INTO "webform_messages_settings" VALUES('WF_DELETE_OLDER_THEN','Automaticky vymazať staršie ako (počet dní)','Ak je zvolená voľba <i>Automatické mazanie starých správ</i>, tak správy staršie ako zvolený počet dní budú automaticky vymazané. Minimálna hodnota je 1.','30','delete_older_then');
