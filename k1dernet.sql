-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 23 jan. 2022 à 17:06
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `k1dernet`
--
CREATE DATABASE IF NOT EXISTS `k1dernet` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `k1dernet`;

-- --------------------------------------------------------

--
-- Structure de la table `acces`
--

CREATE TABLE `acces` (
  `id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '0',
  `descr` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE `config` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE `groupes` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `acces` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_comments`
--

CREATE TABLE `mod_comments` (
  `id` int(10) NOT NULL,
  `module` varchar(255) NOT NULL,
  `resource_id` int(10) NOT NULL DEFAULT '0',
  `note` int(10) NOT NULL DEFAULT '0',
  `author_name` varchar(255) NOT NULL,
  `author_id` int(10) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_definitions`
--

CREATE TABLE `mod_definitions` (
  `id` int(10) NOT NULL,
  `mot` varchar(255) NOT NULL,
  `def` text NOT NULL,
  `exemple` text NOT NULL,
  `type` varchar(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_download`
--

CREATE TABLE `mod_download` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `illus` varchar(255) NOT NULL,
  `cat` int(10) NOT NULL DEFAULT '0',
  `ordre` int(10) NOT NULL DEFAULT '0',
  `size` int(10) NOT NULL DEFAULT '0',
  `note` float NOT NULL DEFAULT '0',
  `votes` int(10) NOT NULL DEFAULT '0',
  `dl` int(10) NOT NULL DEFAULT '0',
  `mirrors` text NOT NULL,
  `active` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_download_cats`
--

CREATE TABLE `mod_download_cats` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `cat` int(10) NOT NULL DEFAULT '0',
  `ordre` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_forum_cats`
--

CREATE TABLE `mod_forum_cats` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `ordre` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_forum_forums`
--

CREATE TABLE `mod_forum_forums` (
  `id` int(10) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `cat` int(10) NOT NULL DEFAULT '0',
  `ordre` int(10) NOT NULL DEFAULT '0',
  `nb_topics` int(10) NOT NULL DEFAULT '0',
  `nb_posts` int(10) NOT NULL DEFAULT '0',
  `last_post_date` int(10) NOT NULL DEFAULT '0',
  `last_poster_name` varchar(255) NOT NULL,
  `last_poster_id` int(10) NOT NULL DEFAULT '0',
  `last_post` varchar(255) NOT NULL,
  `last_post_id` int(10) NOT NULL DEFAULT '0',
  `special` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_forum_posts`
--

CREATE TABLE `mod_forum_posts` (
  `id` int(10) NOT NULL,
  `post` text NOT NULL,
  `post_date` int(10) NOT NULL DEFAULT '0',
  `auteur_id` int(10) NOT NULL DEFAULT '0',
  `auteur_name` varchar(255) NOT NULL,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `forum_id` int(10) NOT NULL DEFAULT '0',
  `new_topic` tinyint(1) NOT NULL DEFAULT '0',
  `view_edit` tinyint(1) NOT NULL DEFAULT '0',
  `edit_name` varchar(255) NOT NULL,
  `edit_date` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Structure de la table `mod_forum_topics`
--

CREATE TABLE `mod_forum_topics` (
  `id` int(10) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `descr` varchar(70) NOT NULL,
  `etat` varchar(8) NOT NULL,
  `posts` int(10) NOT NULL DEFAULT '0',
  `starter_id` mediumint(8) NOT NULL DEFAULT '0',
  `start_date` int(10) NOT NULL DEFAULT '0',
  `last_poster_id` mediumint(8) NOT NULL DEFAULT '0',
  `last_post` int(10) NOT NULL DEFAULT '0',
  `starter_name` varchar(32) NOT NULL,
  `last_poster_name` varchar(32) NOT NULL,
  `poll` int(10) NOT NULL DEFAULT '0',
  `last_vote` int(10) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0',
  `forum_id` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `moved_to` varchar(64) NOT NULL,
  `special` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Structure de la table `mod_galeries`
--

CREATE TABLE `mod_galeries` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `datedebut` int(10) NOT NULL DEFAULT '0',
  `datefin` int(10) NOT NULL DEFAULT '0',
  `tags` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Structure de la table `mod_galeries_photos`
--

CREATE TABLE `mod_galeries_photos` (
  `id` int(10) NOT NULL,
  `file` varchar(255) NOT NULL,
  `id_galerie` int(10) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `note` float NOT NULL DEFAULT '0',
  `votes` int(10) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_lanparty`
--

CREATE TABLE `mod_lanparty` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `date_debut` int(10) NOT NULL DEFAULT '0',
  `date_fin` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_lanparty_inscrits`
--

CREATE TABLE `mod_lanparty_inscrits` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `datenes` int(10) NOT NULL DEFAULT '0',
  `pseudo` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `equipe` int(10) NOT NULL DEFAULT '0',
  `cyb` varchar(255) NOT NULL,
  `cybnum` int(10) NOT NULL DEFAULT '0',
  `tournoi` int(10) NOT NULL DEFAULT '0',
  `pc` int(1) NOT NULL DEFAULT '1',
  `paye` varchar(255) NOT NULL,
  `prix` int(2) NOT NULL DEFAULT '0',
  `transaction` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_lanparty_tournois`
--

CREATE TABLE `mod_lanparty_tournois` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `places` int(10) NOT NULL DEFAULT '0',
  `cybid` varchar(25) NOT NULL DEFAULT '0',
  `pcchoice` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_matches`
--

CREATE TABLE `mod_matches` (
  `id` int(10) NOT NULL,
  `jeu` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `lieu_nom` varchar(255) NOT NULL,
  `lieu_id` int(10) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `mode` varchar(255) NOT NULL,
  `adversaire` varchar(255) NOT NULL,
  `niveau` int(10) NOT NULL DEFAULT '0',
  `nbmaps` int(2) NOT NULL DEFAULT '0',
  `lineup1` text NOT NULL,
  `lineup2` text NOT NULL,
  `scores` text NOT NULL,
  `votes` int(10) NOT NULL DEFAULT '0',
  `note` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_membres`
--

CREATE TABLE `mod_membres` (
  `id` int(10) NOT NULL,
  `pseudo` varchar(200) NOT NULL,
  `clan_id` int(10) NOT NULL DEFAULT '0',
  `clan_nom` varchar(255) NOT NULL,
  `pass` varchar(200) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `natio` varchar(10) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `avatar` varchar(10) NOT NULL DEFAULT '0',
  `signature` text NOT NULL,
  `theme` varchar(255) NOT NULL,
  `date_nes` int(10) NOT NULL DEFAULT '0',
  `date_ins` int(10) NOT NULL DEFAULT '0',
  `last_visit` int(10) NOT NULL DEFAULT '0',
  `ville` varchar(100) NOT NULL,
  `dep` varchar(10) NOT NULL,
  `amis` text NOT NULL,
  `www` varchar(100) NOT NULL,
  `msn` varchar(100) NOT NULL,
  `yahoo` varchar(100) NOT NULL,
  `icq` varchar(100) NOT NULL,
  `aim` varchar(100) NOT NULL,
  `skype` varchar(255) NOT NULL,
  `xfire` varchar(255) NOT NULL,
  `gtalk` varchar(255) NOT NULL,
  `posts` int(6) NOT NULL DEFAULT '0',
  `part` text NOT NULL,
  `act_key` varchar(35) NOT NULL DEFAULT '0',
  `act` int(1) NOT NULL DEFAULT '0',
  `groupe` int(10) NOT NULL DEFAULT '0',
  `acces` varchar(255) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `hard_1` varchar(100) NOT NULL,
  `hard_2` varchar(100) NOT NULL,
  `hard_3` varchar(100) NOT NULL,
  `hard_4` varchar(100) NOT NULL,
  `hard_5` varchar(100) NOT NULL,
  `hard_6` varchar(100) NOT NULL,
  `hard_7` varchar(100) NOT NULL,
  `hard_8` varchar(100) NOT NULL,
  `hard_9` varchar(100) NOT NULL,
  `hard_10` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_membres_clans`
--

CREATE TABLE `mod_membres_clans` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `tag` varchar(40) NOT NULL,
  `tagempl` int(1) NOT NULL DEFAULT '0',
  `banniere` text NOT NULL,
  `datecrea` int(10) NOT NULL DEFAULT '0',
  `leader_id` int(10) NOT NULL DEFAULT '0',
  `leader_pseudo` varchar(200) NOT NULL,
  `site` varchar(255) NOT NULL,
  `irc` varchar(255) NOT NULL,
  `ircserver` varchar(255) NOT NULL,
  `lineup` text NOT NULL,
  `postulants` text NOT NULL,
  `histo` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_messages`
--

CREATE TABLE `mod_messages` (
  `id` int(10) NOT NULL,
  `from_id` int(10) NOT NULL DEFAULT '0',
  `from_name` varchar(255) NOT NULL,
  `to_id` int(10) NOT NULL DEFAULT '0',
  `to_name` varchar(255) NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `etat` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 PACK_KEYS=0;

-- --------------------------------------------------------

--
-- Structure de la table `mod_news`
--

CREATE TABLE `mod_news` (
  `id` int(10) NOT NULL,
  `titre` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `contenu` text CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL,
  `auteur` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `type` varchar(255) CHARACTER SET latin1 COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_partenaires`
--

CREATE TABLE `mod_partenaires` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_poll`
--

CREATE TABLE `mod_poll` (
  `id` int(10) NOT NULL,
  `quest` text NOT NULL,
  `choix` text NOT NULL,
  `results` text,
  `votes` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_poll_votes`
--

CREATE TABLE `mod_poll_votes` (
  `id` int(10) NOT NULL,
  `id_poll` int(10) NOT NULL DEFAULT '0',
  `id_membre` int(10) NOT NULL DEFAULT '0',
  `choix` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_quote`
--

CREATE TABLE `mod_quote` (
  `id` int(10) NOT NULL,
  `auteur` varchar(255) NOT NULL,
  `phrase` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_rss`
--

CREATE TABLE `mod_rss` (
  `id` int(10) NOT NULL,
  `module` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `query` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_shoutbox`
--

CREATE TABLE `mod_shoutbox` (
  `id` int(10) NOT NULL,
  `date` int(10) NOT NULL DEFAULT '0',
  `auteur` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mod_tournois`
--

CREATE TABLE `mod_tournois` (
  `id` int(10) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `date_debut` int(10) NOT NULL DEFAULT '0',
  `date_fin` int(10) NOT NULL DEFAULT '0',
  `jeu` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_comments`
--

CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_post_ID` int(11) NOT NULL DEFAULT '0',
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT '',
  `comment_parent` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_links`
--

CREATE TABLE `wp_links` (
  `link_id` bigint(20) NOT NULL,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_category` bigint(20) NOT NULL DEFAULT '0',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` int(11) NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_options`
--

CREATE TABLE `wp_options` (
  `option_id` bigint(20) NOT NULL,
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_postmeta`
--

CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_posts`
--

CREATE TABLE `wp_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_category` int(4) NOT NULL DEFAULT '0',
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(20) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` text NOT NULL,
  `post_parent` bigint(20) NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_term_relationships`
--

CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_term_taxonomy`
--

CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) NOT NULL,
  `term_id` bigint(20) NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_terms`
--

CREATE TABLE `wp_terms` (
  `term_id` bigint(20) NOT NULL,
  `name` varchar(55) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_usermeta`
--

CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wp_users`
--

CREATE TABLE `wp_users` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(64) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `acces`
--
ALTER TABLE `acces`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`name`);

--
-- Index pour la table `groupes`
--
ALTER TABLE `groupes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_comments`
--
ALTER TABLE `mod_comments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_definitions`
--
ALTER TABLE `mod_definitions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_download`
--
ALTER TABLE `mod_download`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_download_cats`
--
ALTER TABLE `mod_download_cats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_forum_cats`
--
ALTER TABLE `mod_forum_cats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_forum_forums`
--
ALTER TABLE `mod_forum_forums`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_forum_posts`
--
ALTER TABLE `mod_forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);
ALTER TABLE `mod_forum_posts` ADD FULLTEXT KEY `post` (`post`);

--
-- Index pour la table `mod_forum_topics`
--
ALTER TABLE `mod_forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_id` (`forum_id`);
ALTER TABLE `mod_forum_topics` ADD FULLTEXT KEY `titre` (`titre`);

--
-- Index pour la table `mod_galeries`
--
ALTER TABLE `mod_galeries`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_galeries_photos`
--
ALTER TABLE `mod_galeries_photos`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_lanparty`
--
ALTER TABLE `mod_lanparty`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_lanparty_inscrits`
--
ALTER TABLE `mod_lanparty_inscrits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_lanparty_tournois`
--
ALTER TABLE `mod_lanparty_tournois`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_matches`
--
ALTER TABLE `mod_matches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_membres`
--
ALTER TABLE `mod_membres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_membres_clans`
--
ALTER TABLE `mod_membres_clans`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_messages`
--
ALTER TABLE `mod_messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_news`
--
ALTER TABLE `mod_news`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_partenaires`
--
ALTER TABLE `mod_partenaires`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `mod_poll`
--
ALTER TABLE `mod_poll`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_poll_votes`
--
ALTER TABLE `mod_poll_votes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_quote`
--
ALTER TABLE `mod_quote`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_rss`
--
ALTER TABLE `mod_rss`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_shoutbox`
--
ALTER TABLE `mod_shoutbox`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mod_tournois`
--
ALTER TABLE `mod_tournois`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `wp_comments`
--
ALTER TABLE `wp_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `comment_approved` (`comment_approved`),
  ADD KEY `comment_post_ID` (`comment_post_ID`),
  ADD KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  ADD KEY `comment_date_gmt` (`comment_date_gmt`);

--
-- Index pour la table `wp_links`
--
ALTER TABLE `wp_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_category` (`link_category`),
  ADD KEY `link_visible` (`link_visible`);

--
-- Index pour la table `wp_options`
--
ALTER TABLE `wp_options`
  ADD PRIMARY KEY (`option_id`,`blog_id`,`option_name`),
  ADD KEY `option_name` (`option_name`);

--
-- Index pour la table `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Index pour la table `wp_posts`
--
ALTER TABLE `wp_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`);

--
-- Index pour la table `wp_term_relationships`
--
ALTER TABLE `wp_term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Index pour la table `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`);

--
-- Index pour la table `wp_terms`
--
ALTER TABLE `wp_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Index pour la table `wp_users`
--
ALTER TABLE `wp_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_login_key` (`user_login`),
  ADD KEY `user_nicename` (`user_nicename`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `acces`
--
ALTER TABLE `acces`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `groupes`
--
ALTER TABLE `groupes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_comments`
--
ALTER TABLE `mod_comments`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_definitions`
--
ALTER TABLE `mod_definitions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_download`
--
ALTER TABLE `mod_download`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_download_cats`
--
ALTER TABLE `mod_download_cats`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_forum_cats`
--
ALTER TABLE `mod_forum_cats`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_forum_forums`
--
ALTER TABLE `mod_forum_forums`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_forum_posts`
--
ALTER TABLE `mod_forum_posts`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_forum_topics`
--
ALTER TABLE `mod_forum_topics`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_galeries`
--
ALTER TABLE `mod_galeries`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_galeries_photos`
--
ALTER TABLE `mod_galeries_photos`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_lanparty`
--
ALTER TABLE `mod_lanparty`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_lanparty_inscrits`
--
ALTER TABLE `mod_lanparty_inscrits`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_lanparty_tournois`
--
ALTER TABLE `mod_lanparty_tournois`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_matches`
--
ALTER TABLE `mod_matches`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_membres`
--
ALTER TABLE `mod_membres`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_membres_clans`
--
ALTER TABLE `mod_membres_clans`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_messages`
--
ALTER TABLE `mod_messages`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_news`
--
ALTER TABLE `mod_news`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_partenaires`
--
ALTER TABLE `mod_partenaires`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_poll`
--
ALTER TABLE `mod_poll`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_poll_votes`
--
ALTER TABLE `mod_poll_votes`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_quote`
--
ALTER TABLE `mod_quote`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_rss`
--
ALTER TABLE `mod_rss`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_shoutbox`
--
ALTER TABLE `mod_shoutbox`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mod_tournois`
--
ALTER TABLE `mod_tournois`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_comments`
--
ALTER TABLE `wp_comments`
  MODIFY `comment_ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_links`
--
ALTER TABLE `wp_links`
  MODIFY `link_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_options`
--
ALTER TABLE `wp_options`
  MODIFY `option_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  MODIFY `meta_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_posts`
--
ALTER TABLE `wp_posts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  MODIFY `term_taxonomy_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_terms`
--
ALTER TABLE `wp_terms`
  MODIFY `term_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  MODIFY `umeta_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `wp_users`
--
ALTER TABLE `wp_users`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
