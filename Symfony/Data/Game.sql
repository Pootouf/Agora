DROP TABLE IF EXISTS `game`;
CREATE TABLE `game` (
  `game_info_id` int(11) NOT NULL,
  `game_id` int (11) NOT NULL,
  `players_nb` int(11),
  `state` varchar(255),
  `game_name` varchar(255),
  `creation_date` date,
  `host_id` int(11),
  `current_player` int(11),
  PRIMARY KEY (`game_info_id`,`game_id`),
  FOREIGN KEY (`game_info_id`) REFERENCES `game_info`(`id`)
  );
  