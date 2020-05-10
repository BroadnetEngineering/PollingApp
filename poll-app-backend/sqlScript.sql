CREATE Schema polldb;
CREATE TABLE polldb.`polls`
(
 `poll_id`   int NOT NULL auto_increment,
 `question` text NOT NULL ,

PRIMARY KEY (`poll_id`)
);

CREATE TABLE polldb.`answers`
(
 `answer_id`     int NOT NULL auto_increment,
 `text`   text NOT NULL ,
 `votes`  int NOT NULL default 0,
 `poll_id` int NOT NULL ,

PRIMARY KEY (`answer_id`),
KEY `fkIdx_14` (`poll_id`),
CONSTRAINT `FK_14` FOREIGN KEY `fkIdx_14` (`poll_id`) REFERENCES `polls` (`poll_id`)
);

INSERT INTO polldb.polls (question) values("What is your favorite fruit?");
INSERT INTO polldb.answers (text) values("Apple?", 0);





