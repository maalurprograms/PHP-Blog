drop table articles;
create table articles (ArticleID integer primary key autoincrement, Title text, Content text);

drop table users;
create table users (UserID integer primary key autoincrement, Email text unique, Username text, Password text);

drop table users_articles;
create table users_articles (IDArticle  integer primary key, IDUser integer);

drop table themes;
create table themes (ThemeID integer primary key autoincrement, ThemeName text);
insert into themes (ThemeName) values("Computer");
insert into themes (ThemeName) values("Natur");
insert into themes (ThemeName) values("Fotografie");
insert into themes (ThemeName) values("Malerei");
insert into themes (ThemeName) values("Games");
insert into themes (ThemeName) values("Musik");
insert into themes (ThemeName) values("Reisen");

drop table articles_themes;
create table articles_themes (
IDArticle integer primary key,
IDTheme integer,
foreign key (IDArticle) references articles(ArticleID),
foreign key (IDTheme) references themes(ThemeID));