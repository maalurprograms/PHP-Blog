drop table articles;
create table articles (ArticleID integer primary key autoincrement, Title text, Content text);

drop table users;
create table users (UserID integer primary key autoincrement, Email text unique, Username text, Password text);
insert into users (Email, Username, Password) values ("admin@test.ch", "Admin", "$2y$10$u6jpmjB/PSY5xsoBegVne.tMU63Ep1FUztnSbubXsSYaHyVqrQ8Oa");
select * from users;

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

select Title, Content, Username from users_articles
inner join articles on articles.ArticleID=users_articles.IDArticle
inner join users on users.UserID=users_articles.IDUser;

select Title, Content, ThemeName from articles_themes
inner join articles on articles.ArticleID=articles_themes.IDArticle
inner join themes on themes.ThemeID=articles_themes.IDTheme;

insert into articles (Title, Content) values("GTX 980 Ti", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.");
insert into articles (Title, Content) values("Natur", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.");

insert into users_articles values (1, 1);
insert into users_articles values (2, 1);

insert into articles_themes values (1,1);
insert into articles_themes values (2,2);