CREATE TABLE dbhost
(
    `id` int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    `host` varchar(128),
    `dbname` varchar(32),
    `dbport` varchar(5),
    `dbusername` varchar(16),
    `dbpassword` varchar(32),
    `hostuserid` int
);

CREATE TABLE user
(
    `id` int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    `username` varchar(32),
    `password` varchar(32),
    `reg_date` datetime
);

CREATE TABLE post
(
    `id` int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    `publisherid` int,
    `chart_type` varchar(64),
    `settings` varchar(256),
    `did` int,
    `tablename` varchar(32),
    `title` varchar(32),
    `pub_date` datetime
);

CREATE TABLE LIKE
(
    `id` int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(id),
    `postid` int,
    `whoid` int
)