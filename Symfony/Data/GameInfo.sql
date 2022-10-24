/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `game_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min_players` int(11) NOT NULL,
  `max_players` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rules` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `game_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `game_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_209C5D7B6C6E55B5` (`game_name`),
  UNIQUE KEY `UNIQ_209C5D7B81169599` (`game_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `game_info` (`id`, `description`, `image`, `rules`, `game_name`, `min_players`, `max_players`, `game_code`) VALUES
(1, "Ave cesar, un jeu franchement incroyable ! Avec des chars ! Excitant n'est-ce pas ?", 'ave-caesar.jpg', 'bundles/agoraplatform/regles/Ave_Cesar.pdf', 'Ave Cesar', 3, 6,'avc'),
(2, 'Le 6 qui prend est un jeu de cartes passionnant qui creuse les méninges !', '6QP.png', 'bundles/agoraplatform/regles/Six_Qui_Prend.pdf', '6 qui prend', 2, 10, 'sqp'),
(3, "Gagnez de l'influence en plaçant vos légions pour devenir Consul à Rome.", 'augustus.jpg', 'bundles/agoraplatform/regles/Augustus.pdf', 'Augustus', 2, 6, 'aug'),
(4, 'Dans Splendor, vous êtes à la tête d une guilde de marchands. Vous avez pour objectif de vous enrichir et de devenir le commerçant le plus prestigieux du royaume.', 'Splendor.jpg', 'bundles/agoraplatform/regles/Splendor.pdf', 'Splendor', 2, 4, 'spldr'),
(5, 'Soyez le premier à créer un alignement pour gagner ce petit jeu de réflexion !', 'Morpion.jpg', 'bundles/agoraplatform/regles/Morpion.pdf', 'Morpion', 2, 2, 'mor'),
(6, 'Embellisez le Palais Royal de Evora de mille tuiles colorés.', 'Azul.jpg', 'bundles/agoraplatform/regles/Azul.pdf', 'Azul', 2, 4, 'azul'),
(7, 'Construisez les plus longues et les meilleurs chemins de fer de toute la Russie dans RUSSIAN RAILROADS!', 'rr.jpg', 'bundles/agoraplatform/regles/Russian Railroads.pdf', 'Russian Railroads', 2, 4, 'rr'),
(8, 'Jouez au puissance 4 !', 'p4.jpg', 'bundles/agoraplatform/regles/Puissance4.pdf', 'Puissance 4', 2, 2, 'p4');
