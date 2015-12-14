insert into users (Email, Username, Password) values ("admin@gibb.ch", "Admin", "$2y$10$TxSHpr7Vl4tBLwN6f0c6JutPmkQD1rUBQLr96RBassK4u5vt0jx/6");
insert into users (Email, Username, Password) values ("test1@gibb.ch", "Test1", "$2y$10$C3WP/qy.SMsXnavs29TDi.B3vhn4fBRqlbY4vWpatSae1u5rcxn8O");
insert into users (Email, Username, Password) values ("test2@gibb.ch", "Test2", "$2y$10$C3WP/qy.SMsXnavs29TDi.B3vhn4fBRqlbY4vWpatSae1u5rcxn8O");


insert into articles (Title, Content) values("GTX 980 Ti", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.");
insert into articles (Title, Content) values("Natur", "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.");

insert into users_articles values (1, 2);
insert into users_articles values (2, 3);

insert into articles_themes values (1,1);
insert into articles_themes values (2,2);

select * from articles;
select * from users;
select * from themes;
select * from articles_themes;
select * from users_articles;