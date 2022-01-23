-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 23 jan. 2022 à 16:29
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
-- Base de données :  `k1der1`
--
CREATE DATABASE IF NOT EXISTS `k1der1` DEFAULT CHARACTER SET latin1 COLLATE latin1_german1_ci;
USE `k1der1`;

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(6) NOT NULL,
  `cat_id` int(6) NOT NULL DEFAULT '0',
  `texte` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lien` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `level` int(6) NOT NULL DEFAULT '0',
  `ordre` int(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admin_cat`
--

CREATE TABLE `admin_cat` (
  `id` int(6) NOT NULL,
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ordre` int(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `anniv`
--

CREATE TABLE `anniv` (
  `id` int(6) NOT NULL,
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `an` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive`
--

CREATE TABLE `archive` (
  `annee` int(11) NOT NULL DEFAULT '0',
  `mois` int(11) NOT NULL DEFAULT '0',
  `visiteur` int(11) NOT NULL DEFAULT '0',
  `visite` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `calendrier`
--

CREATE TABLE `calendrier` (
  `id` int(6) NOT NULL,
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `debut` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `fin` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `dur` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ville` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `adresse` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `dep` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `site` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `places` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `prix` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois4` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois5` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois6` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois7` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tournois8` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lots` text COLLATE latin1_german1_ci NOT NULL,
  `infos` text COLLATE latin1_german1_ci NOT NULL,
  `k1der` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cats_down`
--

CREATE TABLE `cats_down` (
  `id` int(6) NOT NULL,
  `nom` varchar(150) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `type` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cl`
--

CREATE TABLE `cl` (
  `best_cl` int(10) NOT NULL DEFAULT '0',
  `best_cl_date` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bad_cl` int(10) NOT NULL DEFAULT '0',
  `bad_cl_date` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `compt`
--

CREATE TABLE `compt` (
  `nb` text COLLATE latin1_german1_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `compt_secu`
--

CREATE TABLE `compt_secu` (
  `ip` text COLLATE latin1_german1_ci NOT NULL,
  `time` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE `config` (
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `valeur` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `defi`
--

CREATE TABLE `defi` (
  `id` int(6) NOT NULL,
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `clan` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `leader` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `num` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `map` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `irc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `msn` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `server` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `comm` text COLLATE latin1_german1_ci NOT NULL,
  `jour` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mois` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `annee` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `heure` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `minute` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `orderdate` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `domaines`
--

CREATE TABLE `domaines` (
  `domaine` char(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `description` char(50) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiercomments`
--

CREATE TABLE `dossiercomments` (
  `id` int(6) NOT NULL,
  `id_dossier` int(6) NOT NULL DEFAULT '0',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `heure` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `auteur` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `note` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

CREATE TABLE `dossiers` (
  `id` int(6) NOT NULL,
  `auteur` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `titre` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `image` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `resume` text COLLATE latin1_german1_ci NOT NULL,
  `conf` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiers_p`
--

CREATE TABLE `dossiers_p` (
  `id` int(6) NOT NULL,
  `titrepage` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `page` int(6) NOT NULL DEFAULT '0',
  `id_dossier` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `downcomments`
--

CREATE TABLE `downcomments` (
  `id` int(6) NOT NULL,
  `id_down` int(6) NOT NULL DEFAULT '0',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `heure` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `auteur` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `note` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dvconnectes`
--

CREATE TABLE `dvconnectes` (
  `dateDebut` int(11) NOT NULL DEFAULT '0',
  `dateFin` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(6) NOT NULL,
  `nom` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `prenom` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `age` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `age2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `icq` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `e_mail` varchar(120) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ville` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `role` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `kinder` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conn_type` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conn_fai` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `proc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `graph` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ram` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `son` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mere` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `souris` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `clavier` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ecran` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `tapis` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `cs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `war3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `armecs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mapcs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `resocs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `senscs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `sens2cs` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `herow3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mapw3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `resow3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `urlw3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `os` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `reso2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pass` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `statut` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `next_match` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `level` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `klevel` int(10) NOT NULL DEFAULT '0',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(6) NOT NULL,
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `titre` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `text` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_admin_logs`
--

CREATE TABLE `ib_admin_logs` (
  `id` bigint(20) NOT NULL,
  `act` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `code` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `member_id` int(10) DEFAULT NULL,
  `ctime` int(10) DEFAULT NULL,
  `note` text COLLATE latin1_german1_ci,
  `ip_address` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_admin_sessions`
--

CREATE TABLE `ib_admin_sessions` (
  `session_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `session_ip_address` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `session_member_name` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `session_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `session_member_login_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `session_location` varchar(64) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `session_log_in_time` int(10) NOT NULL DEFAULT '0',
  `session_running_time` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_announcements`
--

CREATE TABLE `ib_announcements` (
  `announce_id` int(10) UNSIGNED NOT NULL,
  `announce_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `announce_post` text COLLATE latin1_german1_ci NOT NULL,
  `announce_forum` text COLLATE latin1_german1_ci NOT NULL,
  `announce_member_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `announce_html_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `announce_views` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `announce_start` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `announce_end` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `announce_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_attachments`
--

CREATE TABLE `ib_attachments` (
  `attach_id` int(10) NOT NULL,
  `attach_ext` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `attach_file` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `attach_location` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `attach_thumb_location` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `attach_thumb_width` smallint(5) NOT NULL DEFAULT '0',
  `attach_thumb_height` smallint(5) NOT NULL DEFAULT '0',
  `attach_is_image` tinyint(1) NOT NULL DEFAULT '0',
  `attach_hits` int(10) NOT NULL DEFAULT '0',
  `attach_date` int(10) NOT NULL DEFAULT '0',
  `attach_temp` tinyint(1) NOT NULL DEFAULT '0',
  `attach_pid` int(10) NOT NULL DEFAULT '0',
  `attach_post_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `attach_msg` int(10) NOT NULL DEFAULT '0',
  `attach_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `attach_approved` int(10) NOT NULL DEFAULT '1',
  `attach_filesize` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_attachments_type`
--

CREATE TABLE `ib_attachments_type` (
  `atype_id` int(10) NOT NULL,
  `atype_extension` varchar(18) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `atype_mimetype` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `atype_post` tinyint(1) NOT NULL DEFAULT '1',
  `atype_photo` tinyint(1) NOT NULL DEFAULT '0',
  `atype_img` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_badwords`
--

CREATE TABLE `ib_badwords` (
  `wid` int(3) NOT NULL,
  `type` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `swop` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `m_exact` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_banfilters`
--

CREATE TABLE `ib_banfilters` (
  `ban_id` int(10) NOT NULL,
  `ban_type` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT 'ip',
  `ban_content` text COLLATE latin1_german1_ci NOT NULL,
  `ban_date` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_bulk_mail`
--

CREATE TABLE `ib_bulk_mail` (
  `mail_id` int(10) NOT NULL,
  `mail_subject` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail_content` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `mail_groups` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `mail_honor` tinyint(1) NOT NULL DEFAULT '1',
  `mail_opts` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `mail_start` int(10) NOT NULL DEFAULT '0',
  `mail_updated` int(10) NOT NULL DEFAULT '0',
  `mail_sentto` int(10) NOT NULL DEFAULT '0',
  `mail_active` tinyint(1) NOT NULL DEFAULT '0',
  `mail_pergo` smallint(5) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_cache_store`
--

CREATE TABLE `ib_cache_store` (
  `cs_key` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `cs_value` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `cs_extra` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `cs_array` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_calendar_events`
--

CREATE TABLE `ib_calendar_events` (
  `eventid` mediumint(8) NOT NULL,
  `userid` mediumint(8) NOT NULL DEFAULT '0',
  `year` int(4) NOT NULL DEFAULT '2002',
  `month` int(2) NOT NULL DEFAULT '1',
  `mday` int(2) NOT NULL DEFAULT '1',
  `title` varchar(254) COLLATE latin1_german1_ci NOT NULL DEFAULT 'no title',
  `event_text` text COLLATE latin1_german1_ci NOT NULL,
  `read_perms` varchar(254) COLLATE latin1_german1_ci NOT NULL DEFAULT '*',
  `unix_stamp` int(10) NOT NULL DEFAULT '0',
  `priv_event` tinyint(1) NOT NULL DEFAULT '0',
  `show_emoticons` tinyint(1) NOT NULL DEFAULT '1',
  `rating` smallint(2) NOT NULL DEFAULT '1',
  `end_day` int(2) DEFAULT NULL,
  `end_month` int(2) DEFAULT NULL,
  `end_year` int(4) DEFAULT NULL,
  `end_unix_stamp` int(10) DEFAULT NULL,
  `event_ranged` tinyint(1) NOT NULL DEFAULT '0',
  `event_repeat` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_unit` char(2) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `event_bgcolor` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `event_color` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_conf_settings`
--

CREATE TABLE `ib_conf_settings` (
  `conf_id` int(10) NOT NULL,
  `conf_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf_description` text COLLATE latin1_german1_ci NOT NULL,
  `conf_group` smallint(3) NOT NULL DEFAULT '0',
  `conf_type` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf_key` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf_value` text COLLATE latin1_german1_ci NOT NULL,
  `conf_default` text COLLATE latin1_german1_ci NOT NULL,
  `conf_extra` text COLLATE latin1_german1_ci NOT NULL,
  `conf_evalphp` text COLLATE latin1_german1_ci NOT NULL,
  `conf_protected` tinyint(1) NOT NULL DEFAULT '0',
  `conf_position` smallint(3) NOT NULL DEFAULT '0',
  `conf_start_group` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf_end_group` tinyint(1) NOT NULL DEFAULT '0',
  `conf_help_key` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `conf_add_cache` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_conf_settings_titles`
--

CREATE TABLE `ib_conf_settings_titles` (
  `conf_title_id` smallint(3) NOT NULL,
  `conf_title_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf_title_desc` text COLLATE latin1_german1_ci NOT NULL,
  `conf_title_count` smallint(3) NOT NULL DEFAULT '0',
  `conf_title_noshow` tinyint(1) NOT NULL DEFAULT '0',
  `conf_title_keyword` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_contacts`
--

CREATE TABLE `ib_contacts` (
  `id` mediumint(8) NOT NULL,
  `contact_id` mediumint(8) NOT NULL DEFAULT '0',
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `contact_name` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `allow_msg` tinyint(1) DEFAULT NULL,
  `contact_desc` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_custom_bbcode`
--

CREATE TABLE `ib_custom_bbcode` (
  `bbcode_id` int(10) NOT NULL,
  `bbcode_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bbcode_desc` text COLLATE latin1_german1_ci NOT NULL,
  `bbcode_tag` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bbcode_replace` text COLLATE latin1_german1_ci NOT NULL,
  `bbcode_useoption` tinyint(1) NOT NULL DEFAULT '0',
  `bbcode_example` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_email_logs`
--

CREATE TABLE `ib_email_logs` (
  `email_id` int(10) NOT NULL,
  `email_subject` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `email_content` text COLLATE latin1_german1_ci NOT NULL,
  `email_date` int(10) NOT NULL DEFAULT '0',
  `from_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `from_email_address` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `from_ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '127.0.0.1',
  `to_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `to_email_address` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `topic_id` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_emoticons`
--

CREATE TABLE `ib_emoticons` (
  `id` smallint(3) NOT NULL,
  `typed` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `image` varchar(128) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `clickable` smallint(2) NOT NULL DEFAULT '1',
  `emo_set` varchar(64) COLLATE latin1_german1_ci NOT NULL DEFAULT 'default'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_faq`
--

CREATE TABLE `ib_faq` (
  `id` mediumint(8) NOT NULL,
  `title` varchar(128) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `text` text COLLATE latin1_german1_ci,
  `description` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_forum_perms`
--

CREATE TABLE `ib_forum_perms` (
  `perm_id` int(10) NOT NULL,
  `perm_name` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_forum_tracker`
--

CREATE TABLE `ib_forum_tracker` (
  `frid` mediumint(8) NOT NULL,
  `member_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `forum_id` smallint(5) NOT NULL DEFAULT '0',
  `start_date` int(10) DEFAULT NULL,
  `last_sent` int(10) NOT NULL DEFAULT '0',
  `forum_track_type` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT 'delayed'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_forums`
--

CREATE TABLE `ib_forums` (
  `id` smallint(5) NOT NULL DEFAULT '0',
  `topics` mediumint(6) DEFAULT '0',
  `posts` mediumint(6) DEFAULT '0',
  `last_post` int(10) DEFAULT NULL,
  `last_poster_id` mediumint(8) NOT NULL DEFAULT '0',
  `last_poster_name` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `name` varchar(128) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `description` text COLLATE latin1_german1_ci,
  `position` tinyint(2) DEFAULT NULL,
  `use_ibc` tinyint(1) DEFAULT NULL,
  `use_html` tinyint(1) DEFAULT NULL,
  `status` varchar(10) COLLATE latin1_german1_ci DEFAULT NULL,
  `password` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_title` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_id` int(10) DEFAULT NULL,
  `sort_key` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `sort_order` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `prune` tinyint(3) DEFAULT NULL,
  `show_rules` tinyint(1) DEFAULT NULL,
  `preview_posts` tinyint(1) DEFAULT NULL,
  `allow_poll` tinyint(1) NOT NULL DEFAULT '1',
  `allow_pollbump` tinyint(1) NOT NULL DEFAULT '0',
  `inc_postcount` tinyint(1) NOT NULL DEFAULT '1',
  `skin_id` int(10) DEFAULT NULL,
  `parent_id` mediumint(5) DEFAULT '-1',
  `sub_can_post` tinyint(1) DEFAULT '1',
  `quick_reply` tinyint(1) DEFAULT '0',
  `redirect_url` varchar(250) COLLATE latin1_german1_ci DEFAULT '',
  `redirect_on` tinyint(1) NOT NULL DEFAULT '0',
  `redirect_hits` int(10) NOT NULL DEFAULT '0',
  `redirect_loc` varchar(250) COLLATE latin1_german1_ci DEFAULT '',
  `rules_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `rules_text` text COLLATE latin1_german1_ci NOT NULL,
  `topic_mm_id` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `notify_modq_emails` text COLLATE latin1_german1_ci,
  `permission_custom_error` text COLLATE latin1_german1_ci NOT NULL,
  `permission_array` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `permission_showtopic` tinyint(1) NOT NULL DEFAULT '0',
  `queued_topics` mediumint(6) NOT NULL DEFAULT '0',
  `queued_posts` mediumint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_groups`
--

CREATE TABLE `ib_groups` (
  `g_id` int(3) UNSIGNED NOT NULL,
  `g_view_board` tinyint(1) DEFAULT NULL,
  `g_mem_info` tinyint(1) DEFAULT NULL,
  `g_other_topics` tinyint(1) DEFAULT NULL,
  `g_use_search` tinyint(1) DEFAULT NULL,
  `g_email_friend` tinyint(1) DEFAULT NULL,
  `g_invite_friend` tinyint(1) DEFAULT NULL,
  `g_edit_profile` tinyint(1) DEFAULT NULL,
  `g_post_new_topics` tinyint(1) DEFAULT NULL,
  `g_reply_own_topics` tinyint(1) DEFAULT NULL,
  `g_reply_other_topics` tinyint(1) DEFAULT NULL,
  `g_edit_posts` tinyint(1) DEFAULT NULL,
  `g_delete_own_posts` tinyint(1) DEFAULT NULL,
  `g_open_close_posts` tinyint(1) DEFAULT NULL,
  `g_delete_own_topics` tinyint(1) DEFAULT NULL,
  `g_post_polls` tinyint(1) DEFAULT NULL,
  `g_vote_polls` tinyint(1) DEFAULT NULL,
  `g_use_pm` tinyint(1) DEFAULT NULL,
  `g_is_supmod` tinyint(1) DEFAULT NULL,
  `g_access_cp` tinyint(1) DEFAULT NULL,
  `g_title` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `g_can_remove` tinyint(1) DEFAULT NULL,
  `g_append_edit` tinyint(1) DEFAULT NULL,
  `g_access_offline` tinyint(1) DEFAULT NULL,
  `g_avoid_q` tinyint(1) DEFAULT NULL,
  `g_avoid_flood` tinyint(1) DEFAULT NULL,
  `g_icon` text COLLATE latin1_german1_ci NOT NULL,
  `g_attach_max` bigint(20) DEFAULT NULL,
  `g_avatar_upload` tinyint(1) DEFAULT '0',
  `g_calendar_post` tinyint(1) DEFAULT '0',
  `prefix` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `suffix` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `g_max_messages` int(5) DEFAULT '50',
  `g_max_mass_pm` int(5) DEFAULT '0',
  `g_search_flood` mediumint(6) DEFAULT '20',
  `g_edit_cutoff` int(10) DEFAULT '0',
  `g_promotion` varchar(10) COLLATE latin1_german1_ci DEFAULT '-1&-1',
  `g_hide_from_list` tinyint(1) DEFAULT '0',
  `g_post_closed` tinyint(1) DEFAULT '0',
  `g_bypass_badwords` tinyint(1) NOT NULL DEFAULT '0',
  `g_perm_id` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `g_photo_max_vars` varchar(200) COLLATE latin1_german1_ci DEFAULT '100:250:250',
  `g_dohtml` tinyint(1) NOT NULL DEFAULT '0',
  `g_edit_topic` tinyint(1) NOT NULL DEFAULT '0',
  `g_email_limit` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT '10:15',
  `g_attach_per_post` int(10) NOT NULL DEFAULT '0',
  `g_can_msg_attach` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_languages`
--

CREATE TABLE `ib_languages` (
  `lid` mediumint(8) NOT NULL,
  `ldir` varchar(64) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lname` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lauthor` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL,
  `lemail` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_mail_error_logs`
--

CREATE TABLE `ib_mail_error_logs` (
  `mlog_id` int(10) NOT NULL,
  `mlog_date` int(10) NOT NULL DEFAULT '0',
  `mlog_to` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mlog_from` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mlog_subject` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mlog_content` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mlog_msg` text COLLATE latin1_german1_ci NOT NULL,
  `mlog_code` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mlog_smtp_msg` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_mail_queue`
--

CREATE TABLE `ib_mail_queue` (
  `mail_id` int(10) NOT NULL,
  `mail_date` int(10) NOT NULL DEFAULT '0',
  `mail_to` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail_from` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail_subject` text COLLATE latin1_german1_ci NOT NULL,
  `mail_content` text COLLATE latin1_german1_ci NOT NULL,
  `mail_type` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_member_extra`
--

CREATE TABLE `ib_member_extra` (
  `id` mediumint(8) NOT NULL DEFAULT '0',
  `notes` text COLLATE latin1_german1_ci,
  `links` text COLLATE latin1_german1_ci,
  `bio` text COLLATE latin1_german1_ci,
  `ta_size` char(3) COLLATE latin1_german1_ci DEFAULT NULL,
  `aim_name` varchar(40) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `icq_number` int(15) NOT NULL DEFAULT '0',
  `website` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `yahoo` varchar(40) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `interests` text COLLATE latin1_german1_ci NOT NULL,
  `msnname` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `vdirs` text COLLATE latin1_german1_ci NOT NULL,
  `location` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `signature` text COLLATE latin1_german1_ci NOT NULL,
  `avatar_location` varchar(128) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `avatar_size` varchar(9) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `avatar_type` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT 'local',
  `photo_type` varchar(10) COLLATE latin1_german1_ci DEFAULT '',
  `photo_location` varchar(255) COLLATE latin1_german1_ci DEFAULT '',
  `photo_dimensions` varchar(200) COLLATE latin1_german1_ci DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_members`
--

CREATE TABLE `ib_members` (
  `id` mediumint(8) NOT NULL DEFAULT '0',
  `name` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mgroup` smallint(3) NOT NULL DEFAULT '0',
  `legacy_password` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `email` varchar(60) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joined` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `posts` mediumint(7) DEFAULT '0',
  `title` varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  `allow_admin_mails` tinyint(1) DEFAULT NULL,
  `time_offset` varchar(10) COLLATE latin1_german1_ci DEFAULT NULL,
  `hide_email` varchar(8) COLLATE latin1_german1_ci DEFAULT NULL,
  `email_pm` tinyint(1) DEFAULT NULL,
  `email_full` tinyint(1) DEFAULT NULL,
  `skin` smallint(5) DEFAULT NULL,
  `warn_level` int(10) DEFAULT NULL,
  `warn_lastwarn` int(10) NOT NULL DEFAULT '0',
  `language` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_post` int(10) DEFAULT NULL,
  `restrict_post` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `view_sigs` tinyint(1) DEFAULT '1',
  `view_img` tinyint(1) DEFAULT '1',
  `view_avs` tinyint(1) DEFAULT '1',
  `view_pop` tinyint(1) DEFAULT '1',
  `bday_day` int(2) NOT NULL DEFAULT '0',
  `bday_month` int(2) NOT NULL DEFAULT '0',
  `bday_year` int(4) NOT NULL DEFAULT '0',
  `new_msg` tinyint(2) DEFAULT NULL,
  `msg_total` smallint(5) DEFAULT '0',
  `show_popup` tinyint(1) DEFAULT NULL,
  `misc` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_visit` int(10) DEFAULT '0',
  `last_activity` int(10) DEFAULT '0',
  `dst_in_use` tinyint(1) DEFAULT '0',
  `view_prefs` varchar(64) COLLATE latin1_german1_ci DEFAULT '-1&-1',
  `coppa_user` tinyint(1) DEFAULT '0',
  `mod_posts` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `auto_track` varchar(50) COLLATE latin1_german1_ci DEFAULT '0',
  `login_anonymous` char(3) COLLATE latin1_german1_ci NOT NULL DEFAULT '0&0',
  `ignored_users` text COLLATE latin1_german1_ci NOT NULL,
  `mgroup_others` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `member_login_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `org_perm_id` varchar(255) COLLATE latin1_german1_ci DEFAULT '',
  `temp_ban` varchar(100) COLLATE latin1_german1_ci DEFAULT '0',
  `has_blog` tinyint(1) NOT NULL DEFAULT '0',
  `sub_end` int(10) NOT NULL DEFAULT '0',
  `subs_pkg_chosen` smallint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_members_converge`
--

CREATE TABLE `ib_members_converge` (
  `converge_id` int(10) NOT NULL,
  `converge_email` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `converge_joined` int(10) NOT NULL DEFAULT '0',
  `converge_pass_hash` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `converge_pass_salt` varchar(5) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_message_text`
--

CREATE TABLE `ib_message_text` (
  `msg_id` int(10) NOT NULL,
  `msg_date` int(10) DEFAULT NULL,
  `msg_post` text COLLATE latin1_german1_ci,
  `msg_cc_users` text COLLATE latin1_german1_ci,
  `msg_sent_to_count` smallint(5) NOT NULL DEFAULT '0',
  `msg_deleted_count` smallint(5) NOT NULL DEFAULT '0',
  `msg_post_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `msg_author_id` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_message_topics`
--

CREATE TABLE `ib_message_topics` (
  `mt_id` int(10) NOT NULL,
  `mt_msg_id` int(10) NOT NULL DEFAULT '0',
  `mt_date` int(10) NOT NULL DEFAULT '0',
  `mt_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mt_from_id` mediumint(8) NOT NULL DEFAULT '0',
  `mt_to_id` mediumint(8) NOT NULL DEFAULT '0',
  `mt_owner_id` mediumint(8) NOT NULL DEFAULT '0',
  `mt_vid_folder` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mt_read` tinyint(1) NOT NULL DEFAULT '0',
  `mt_hasattach` smallint(5) NOT NULL DEFAULT '0',
  `mt_hide_cc` tinyint(1) DEFAULT '0',
  `mt_tracking` tinyint(1) DEFAULT '0',
  `mt_user_read` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_moderator_logs`
--

CREATE TABLE `ib_moderator_logs` (
  `id` int(10) NOT NULL,
  `forum_id` int(5) DEFAULT '0',
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `post_id` int(10) DEFAULT NULL,
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `member_name` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `http_referer` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `ctime` int(10) DEFAULT NULL,
  `topic_title` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL,
  `action` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL,
  `query_string` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_moderators`
--

CREATE TABLE `ib_moderators` (
  `mid` mediumint(8) NOT NULL,
  `forum_id` int(5) NOT NULL DEFAULT '0',
  `member_name` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `edit_post` tinyint(1) DEFAULT NULL,
  `edit_topic` tinyint(1) DEFAULT NULL,
  `delete_post` tinyint(1) DEFAULT NULL,
  `delete_topic` tinyint(1) DEFAULT NULL,
  `view_ip` tinyint(1) DEFAULT NULL,
  `open_topic` tinyint(1) DEFAULT NULL,
  `close_topic` tinyint(1) DEFAULT NULL,
  `mass_move` tinyint(1) DEFAULT NULL,
  `mass_prune` tinyint(1) DEFAULT NULL,
  `move_topic` tinyint(1) DEFAULT NULL,
  `pin_topic` tinyint(1) DEFAULT NULL,
  `unpin_topic` tinyint(1) DEFAULT NULL,
  `post_q` tinyint(1) DEFAULT NULL,
  `topic_q` tinyint(1) DEFAULT NULL,
  `allow_warn` tinyint(1) DEFAULT NULL,
  `edit_user` tinyint(1) NOT NULL DEFAULT '0',
  `is_group` tinyint(1) DEFAULT '0',
  `group_id` smallint(3) NOT NULL DEFAULT '0',
  `group_name` varchar(200) COLLATE latin1_german1_ci DEFAULT NULL,
  `split_merge` tinyint(1) DEFAULT '0',
  `can_mm` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_pfields_content`
--

CREATE TABLE `ib_pfields_content` (
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `updated` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_pfields_data`
--

CREATE TABLE `ib_pfields_data` (
  `pf_id` smallint(5) NOT NULL,
  `pf_title` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pf_desc` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pf_content` text COLLATE latin1_german1_ci NOT NULL,
  `pf_type` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pf_not_null` tinyint(1) NOT NULL DEFAULT '0',
  `pf_member_hide` tinyint(1) NOT NULL DEFAULT '0',
  `pf_max_input` smallint(6) NOT NULL DEFAULT '0',
  `pf_member_edit` tinyint(1) NOT NULL DEFAULT '0',
  `pf_position` smallint(6) NOT NULL DEFAULT '0',
  `pf_show_on_reg` tinyint(1) NOT NULL DEFAULT '0',
  `pf_input_format` text COLLATE latin1_german1_ci NOT NULL,
  `pf_admin_only` tinyint(1) NOT NULL DEFAULT '0',
  `pf_topic_format` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_polls`
--

CREATE TABLE `ib_polls` (
  `pid` mediumint(8) NOT NULL,
  `tid` int(10) NOT NULL DEFAULT '0',
  `start_date` int(10) DEFAULT NULL,
  `choices` text COLLATE latin1_german1_ci,
  `starter_id` mediumint(8) NOT NULL DEFAULT '0',
  `votes` smallint(5) NOT NULL DEFAULT '0',
  `forum_id` smallint(5) NOT NULL DEFAULT '0',
  `poll_question` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_posts`
--

CREATE TABLE `ib_posts` (
  `append_edit` tinyint(1) DEFAULT '0',
  `edit_time` int(10) DEFAULT NULL,
  `pid` int(10) NOT NULL,
  `author_id` mediumint(8) NOT NULL DEFAULT '0',
  `author_name` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `use_sig` tinyint(1) NOT NULL DEFAULT '0',
  `use_emo` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `post_date` int(10) NOT NULL DEFAULT '0',
  `icon_id` smallint(3) DEFAULT NULL,
  `post` text COLLATE latin1_german1_ci,
  `queued` tinyint(1) NOT NULL DEFAULT '0',
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `post_title` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `new_topic` tinyint(1) DEFAULT '0',
  `edit_name` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `post_parent` int(10) NOT NULL DEFAULT '0',
  `post_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `post_htmlstate` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_reg_antispam`
--

CREATE TABLE `ib_reg_antispam` (
  `regid` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `regcode` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ip_address` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `ctime` int(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_search_results`
--

CREATE TABLE `ib_search_results` (
  `id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `topic_id` text COLLATE latin1_german1_ci NOT NULL,
  `search_date` int(12) NOT NULL DEFAULT '0',
  `topic_max` int(3) NOT NULL DEFAULT '0',
  `sort_key` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT 'last_post',
  `sort_order` varchar(4) COLLATE latin1_german1_ci NOT NULL DEFAULT 'desc',
  `member_id` mediumint(10) DEFAULT '0',
  `ip_address` varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  `post_id` text COLLATE latin1_german1_ci,
  `post_max` int(10) NOT NULL DEFAULT '0',
  `query_cache` text COLLATE latin1_german1_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_sessions`
--

CREATE TABLE `ib_sessions` (
  `id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `member_name` varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_german1_ci DEFAULT NULL,
  `browser` varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  `running_time` int(10) DEFAULT NULL,
  `login_type` tinyint(1) DEFAULT NULL,
  `location` varchar(40) COLLATE latin1_german1_ci DEFAULT NULL,
  `member_group` smallint(3) DEFAULT NULL,
  `in_forum` smallint(5) NOT NULL DEFAULT '0',
  `in_topic` int(10) NOT NULL DEFAULT '0',
  `in_error` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_skin_macro`
--

CREATE TABLE `ib_skin_macro` (
  `macro_id` smallint(3) NOT NULL,
  `macro_value` varchar(200) COLLATE latin1_german1_ci DEFAULT NULL,
  `macro_replace` text COLLATE latin1_german1_ci,
  `macro_can_remove` tinyint(1) DEFAULT '0',
  `macro_set` smallint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_skin_sets`
--

CREATE TABLE `ib_skin_sets` (
  `set_skin_set_id` int(10) NOT NULL,
  `set_name` varchar(150) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `set_image_dir` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `set_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `set_default` tinyint(1) NOT NULL DEFAULT '0',
  `set_css_method` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT 'inline',
  `set_skin_set_parent` smallint(5) NOT NULL DEFAULT '-1',
  `set_author_email` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `set_author_name` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `set_author_url` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `set_css` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `set_wrapper` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `set_css_updated` int(10) NOT NULL DEFAULT '0',
  `set_cache_css` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `set_cache_macro` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `set_cache_wrapper` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `set_emoticon_folder` varchar(60) COLLATE latin1_german1_ci NOT NULL DEFAULT 'default'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_skin_templates`
--

CREATE TABLE `ib_skin_templates` (
  `suid` int(10) NOT NULL,
  `set_id` int(10) NOT NULL DEFAULT '0',
  `group_name` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `section_content` mediumtext COLLATE latin1_german1_ci,
  `func_name` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `func_data` text COLLATE latin1_german1_ci,
  `updated` int(10) DEFAULT NULL,
  `can_remove` tinyint(4) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_skin_templates_cache`
--

CREATE TABLE `ib_skin_templates_cache` (
  `template_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `template_group_name` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `template_group_content` mediumtext COLLATE latin1_german1_ci NOT NULL,
  `template_set_id` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_spider_logs`
--

CREATE TABLE `ib_spider_logs` (
  `sid` int(10) NOT NULL,
  `bot` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `query_string` text COLLATE latin1_german1_ci NOT NULL,
  `entry_date` int(10) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_stats`
--

CREATE TABLE `ib_stats` (
  `TOTAL_REPLIES` int(10) NOT NULL DEFAULT '0',
  `TOTAL_TOPICS` int(10) NOT NULL DEFAULT '0',
  `LAST_MEM_NAME` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `LAST_MEM_ID` mediumint(8) NOT NULL DEFAULT '0',
  `MOST_DATE` int(10) DEFAULT NULL,
  `MOST_COUNT` int(10) DEFAULT '0',
  `MEM_COUNT` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscription_currency`
--

CREATE TABLE `ib_subscription_currency` (
  `subcurrency_code` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subcurrency_desc` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subcurrency_exchange` decimal(10,8) NOT NULL DEFAULT '0.00000000',
  `subcurrency_default` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscription_extra`
--

CREATE TABLE `ib_subscription_extra` (
  `subextra_id` smallint(5) NOT NULL,
  `subextra_sub_id` smallint(5) NOT NULL DEFAULT '0',
  `subextra_method_id` smallint(5) NOT NULL DEFAULT '0',
  `subextra_product_id` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `subextra_can_upgrade` tinyint(1) NOT NULL DEFAULT '0',
  `subextra_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `subextra_custom_1` text COLLATE latin1_german1_ci,
  `subextra_custom_2` text COLLATE latin1_german1_ci,
  `subextra_custom_3` text COLLATE latin1_german1_ci,
  `subextra_custom_4` text COLLATE latin1_german1_ci,
  `subextra_custom_5` text COLLATE latin1_german1_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscription_logs`
--

CREATE TABLE `ib_subscription_logs` (
  `sublog_id` int(10) NOT NULL,
  `sublog_date` int(10) NOT NULL DEFAULT '0',
  `sublog_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `sublog_transid` int(10) NOT NULL DEFAULT '0',
  `sublog_ipaddress` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `sublog_data` text COLLATE latin1_german1_ci,
  `sublog_postdata` text COLLATE latin1_german1_ci
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscription_methods`
--

CREATE TABLE `ib_subscription_methods` (
  `submethod_id` smallint(5) NOT NULL,
  `submethod_title` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `submethod_name` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `submethod_email` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `submethod_sid` text COLLATE latin1_german1_ci,
  `submethod_custom_1` text COLLATE latin1_german1_ci,
  `submethod_custom_2` text COLLATE latin1_german1_ci,
  `submethod_custom_3` text COLLATE latin1_german1_ci,
  `submethod_custom_4` text COLLATE latin1_german1_ci,
  `submethod_custom_5` text COLLATE latin1_german1_ci,
  `submethod_is_cc` tinyint(1) NOT NULL DEFAULT '0',
  `submethod_is_auto` tinyint(1) NOT NULL DEFAULT '0',
  `submethod_desc` text COLLATE latin1_german1_ci,
  `submethod_logo` text COLLATE latin1_german1_ci,
  `submethod_active` tinyint(1) NOT NULL DEFAULT '0',
  `submethod_use_currency` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT 'USD'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscription_trans`
--

CREATE TABLE `ib_subscription_trans` (
  `subtrans_id` int(10) NOT NULL,
  `subtrans_sub_id` smallint(5) NOT NULL DEFAULT '0',
  `subtrans_member_id` mediumint(8) NOT NULL DEFAULT '0',
  `subtrans_old_group` smallint(5) NOT NULL DEFAULT '0',
  `subtrans_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtrans_cumulative` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtrans_method` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subtrans_start_date` int(11) NOT NULL DEFAULT '0',
  `subtrans_end_date` int(11) NOT NULL DEFAULT '0',
  `subtrans_state` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subtrans_trxid` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subtrans_subscrid` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `subtrans_currency` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT 'USD'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_subscriptions`
--

CREATE TABLE `ib_subscriptions` (
  `sub_id` smallint(5) NOT NULL,
  `sub_title` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `sub_desc` text COLLATE latin1_german1_ci,
  `sub_new_group` mediumint(8) NOT NULL DEFAULT '0',
  `sub_length` smallint(5) NOT NULL DEFAULT '1',
  `sub_unit` char(2) COLLATE latin1_german1_ci NOT NULL DEFAULT 'm',
  `sub_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sub_run_module` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_task_logs`
--

CREATE TABLE `ib_task_logs` (
  `log_id` int(10) NOT NULL,
  `log_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `log_date` int(10) NOT NULL DEFAULT '0',
  `log_ip` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `log_desc` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_task_manager`
--

CREATE TABLE `ib_task_manager` (
  `task_id` int(10) NOT NULL,
  `task_title` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `task_file` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `task_next_run` int(10) NOT NULL DEFAULT '0',
  `task_week_day` tinyint(1) NOT NULL DEFAULT '-1',
  `task_month_day` smallint(2) NOT NULL DEFAULT '-1',
  `task_hour` smallint(2) NOT NULL DEFAULT '-1',
  `task_minute` smallint(2) NOT NULL DEFAULT '-1',
  `task_cronkey` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `task_log` tinyint(1) NOT NULL DEFAULT '0',
  `task_description` text COLLATE latin1_german1_ci NOT NULL,
  `task_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `task_key` varchar(30) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `task_safemode` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_titles`
--

CREATE TABLE `ib_titles` (
  `id` smallint(5) NOT NULL,
  `posts` int(10) DEFAULT NULL,
  `title` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL,
  `pips` varchar(128) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_topic_mmod`
--

CREATE TABLE `ib_topic_mmod` (
  `mm_id` smallint(5) NOT NULL,
  `mm_title` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mm_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `topic_state` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT 'leave',
  `topic_pin` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT 'leave',
  `topic_move` smallint(5) NOT NULL DEFAULT '0',
  `topic_move_link` tinyint(1) NOT NULL DEFAULT '0',
  `topic_title_st` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `topic_title_end` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `topic_reply` tinyint(1) NOT NULL DEFAULT '0',
  `topic_reply_content` text COLLATE latin1_german1_ci NOT NULL,
  `topic_reply_postcount` tinyint(1) NOT NULL DEFAULT '0',
  `mm_forums` text COLLATE latin1_german1_ci NOT NULL,
  `topic_approve` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_topics`
--

CREATE TABLE `ib_topics` (
  `tid` int(10) NOT NULL,
  `title` varchar(70) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `description` varchar(70) COLLATE latin1_german1_ci DEFAULT NULL,
  `state` varchar(8) COLLATE latin1_german1_ci DEFAULT NULL,
  `posts` int(10) DEFAULT NULL,
  `starter_id` mediumint(8) NOT NULL DEFAULT '0',
  `start_date` int(10) DEFAULT NULL,
  `last_poster_id` mediumint(8) NOT NULL DEFAULT '0',
  `last_post` int(10) NOT NULL DEFAULT '0',
  `icon_id` tinyint(2) DEFAULT NULL,
  `starter_name` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_poster_name` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `poll_state` varchar(8) COLLATE latin1_german1_ci DEFAULT NULL,
  `last_vote` int(10) DEFAULT NULL,
  `views` int(10) DEFAULT '0',
  `forum_id` smallint(5) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `author_mode` tinyint(1) DEFAULT NULL,
  `pinned` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `moved_to` varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  `rating` text COLLATE latin1_german1_ci,
  `total_votes` int(5) NOT NULL DEFAULT '0',
  `topic_hasattach` smallint(5) NOT NULL DEFAULT '0',
  `topic_firstpost` int(10) NOT NULL DEFAULT '0',
  `topic_queuedposts` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_topics_read`
--

CREATE TABLE `ib_topics_read` (
  `read_tid` int(10) NOT NULL DEFAULT '0',
  `read_mid` mediumint(8) NOT NULL DEFAULT '0',
  `read_date` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_tracker`
--

CREATE TABLE `ib_tracker` (
  `trid` mediumint(8) NOT NULL,
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `start_date` int(10) DEFAULT NULL,
  `last_sent` int(10) NOT NULL DEFAULT '0',
  `topic_track_type` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT 'delayed'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_upgrade_history`
--

CREATE TABLE `ib_upgrade_history` (
  `upgrade_id` int(10) NOT NULL,
  `upgrade_version_id` int(10) NOT NULL DEFAULT '0',
  `upgrade_version_human` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `upgrade_date` int(10) NOT NULL DEFAULT '0',
  `upgrade_mid` int(10) NOT NULL DEFAULT '0',
  `upgrade_notes` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_validating`
--

CREATE TABLE `ib_validating` (
  `vid` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `member_id` mediumint(8) NOT NULL DEFAULT '0',
  `real_group` smallint(3) NOT NULL DEFAULT '0',
  `temp_group` smallint(3) NOT NULL DEFAULT '0',
  `entry_date` int(10) NOT NULL DEFAULT '0',
  `coppa_user` tinyint(1) NOT NULL DEFAULT '0',
  `lost_pass` tinyint(1) NOT NULL DEFAULT '0',
  `new_reg` tinyint(1) NOT NULL DEFAULT '0',
  `email_chg` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_voters`
--

CREATE TABLE `ib_voters` (
  `vid` int(10) NOT NULL,
  `ip_address` varchar(16) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `vote_date` int(10) NOT NULL DEFAULT '0',
  `tid` int(10) NOT NULL DEFAULT '0',
  `member_id` varchar(32) COLLATE latin1_german1_ci DEFAULT NULL,
  `forum_id` smallint(5) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ib_warn_logs`
--

CREATE TABLE `ib_warn_logs` (
  `wlog_id` int(10) NOT NULL,
  `wlog_mid` mediumint(8) NOT NULL DEFAULT '0',
  `wlog_notes` text COLLATE latin1_german1_ci NOT NULL,
  `wlog_contact` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT 'none',
  `wlog_contact_content` text COLLATE latin1_german1_ci NOT NULL,
  `wlog_date` int(10) NOT NULL DEFAULT '0',
  `wlog_type` varchar(6) COLLATE latin1_german1_ci NOT NULL DEFAULT 'pos',
  `wlog_addedby` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `idees`
--

CREATE TABLE `idees` (
  `id` int(6) NOT NULL,
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `idee` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `joueurs`
--

CREATE TABLE `joueurs` (
  `nom` varchar(10) COLLATE latin1_german1_ci DEFAULT NULL,
  `statut` char(3) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lan`
--

CREATE TABLE `lan` (
  `jour` tinyint(2) DEFAULT NULL,
  `mois` varchar(10) COLLATE latin1_german1_ci DEFAULT NULL,
  `annee` smallint(4) DEFAULT NULL,
  `nom` varchar(20) COLLATE latin1_german1_ci DEFAULT NULL,
  `lanlien` varchar(250) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lan_party`
--

CREATE TABLE `lan_party` (
  `id` int(6) NOT NULL,
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jour` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mois` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `annee` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `loc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `url` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueurs` text COLLATE latin1_german1_ci NOT NULL,
  `orderdate` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lan_partycomments`
--

CREATE TABLE `lan_partycomments` (
  `id` int(5) NOT NULL,
  `id_lan` int(5) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `heure` varchar(5) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pseudo` varchar(35) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `commentaire` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liens`
--

CREATE TABLE `liens` (
  `id` int(6) NOT NULL,
  `nom` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `lien` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `image` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `conf` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liens_down`
--

CREATE TABLE `liens_down` (
  `id` int(6) NOT NULL,
  `nom` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `img` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `descr` text COLLATE latin1_german1_ci NOT NULL,
  `lien` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `taille` int(6) NOT NULL DEFAULT '0',
  `pop` float NOT NULL DEFAULT '0',
  `cat` int(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matches`
--

CREATE TABLE `matches` (
  `id` int(6) NOT NULL,
  `mechants` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `site` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `irc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `score_k1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `score_me` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jour` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mois` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `annee` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `type` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `loc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `occ` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `map` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `map2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `score_map_k1_ct` int(6) NOT NULL DEFAULT '0',
  `score_map_k1_t` int(6) NOT NULL DEFAULT '0',
  `score_map_me_ct` int(6) NOT NULL DEFAULT '0',
  `score_map_me_t` int(6) NOT NULL DEFAULT '0',
  `score_map2_k1_ct` int(6) NOT NULL DEFAULT '0',
  `score_map2_k1_t` int(6) NOT NULL DEFAULT '0',
  `score_map2_me_ct` int(6) NOT NULL DEFAULT '0',
  `score_map2_me_t` int(6) NOT NULL DEFAULT '0',
  `jou_k1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_k2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_k3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_k4` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_k5` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_m1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_m2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_m3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_m4` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `jou_m5` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `orderdate` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `hltv` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `det` int(6) NOT NULL DEFAULT '0',
  `comm` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matchescomments`
--

CREATE TABLE `matchescomments` (
  `id` int(5) NOT NULL,
  `id_match` int(5) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `heure` varchar(5) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pseudo` varchar(35) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `commentaire` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mynewscomments`
--

CREATE TABLE `mynewscomments` (
  `id` int(5) NOT NULL,
  `id_news` int(5) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `heure` varchar(5) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pseudo` varchar(35) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `commentaire` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mynewsinfos`
--

CREATE TABLE `mynewsinfos` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `date_verif` datetime DEFAULT NULL,
  `date` varchar(10) COLLATE latin1_german1_ci DEFAULT NULL,
  `heure` varchar(5) COLLATE latin1_german1_ci DEFAULT NULL,
  `signature` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `email_sign` text COLLATE latin1_german1_ci,
  `source` char(3) COLLATE latin1_german1_ci DEFAULT NULL,
  `nom_source` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `url_source` text COLLATE latin1_german1_ci,
  `image` char(3) COLLATE latin1_german1_ci DEFAULT NULL,
  `path_image` text COLLATE latin1_german1_ci,
  `url_image` text COLLATE latin1_german1_ci,
  `news` text COLLATE latin1_german1_ci,
  `conf` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `next_matches`
--

CREATE TABLE `next_matches` (
  `id` int(6) NOT NULL,
  `mechants` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `leader` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `irc` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `msn` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `server` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `heure` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `occ` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueur1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueur2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueur3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueur4` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `joueur5` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `map` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `map2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `orderdate` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pass`
--

CREATE TABLE `pass` (
  `nom` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pass` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_cache`
--

CREATE TABLE `php_stats_cache` (
  `user_id` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `data` int(11) NOT NULL DEFAULT '0',
  `lastpage` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `visitor_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `hits` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `visits` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `reso` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `colo` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `os` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bw` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `host` varchar(50) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lang` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `giorno` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_clicks`
--

CREATE TABLE `php_stats_clicks` (
  `id` int(11) NOT NULL,
  `nome` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `creazione` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_config`
--

CREATE TABLE `php_stats_config` (
  `name` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `value` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_counters`
--

CREATE TABLE `php_stats_counters` (
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `no_count_hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `no_count_visits` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_daily`
--

CREATE TABLE `php_stats_daily` (
  `data` date NOT NULL DEFAULT '0000-00-00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `no_count_hits` int(11) NOT NULL DEFAULT '0',
  `no_count_visits` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_details`
--

CREATE TABLE `php_stats_details` (
  `visitor_id` varchar(50) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `host` varchar(50) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `agent` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `os` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bw` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lang` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `referer` longtext COLLATE latin1_german1_ci,
  `currentPage` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `reso` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `colo` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `titlePage` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_domains`
--

CREATE TABLE `php_stats_domains` (
  `visits` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `tld` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `area` varchar(4) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_downloads`
--

CREATE TABLE `php_stats_downloads` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `descrizione` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `type` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `home` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `size` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `creazione` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `withinterface` enum('YES','NO') COLLATE latin1_german1_ci NOT NULL DEFAULT 'NO'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_hourly`
--

CREATE TABLE `php_stats_hourly` (
  `data` tinyint(4) NOT NULL DEFAULT '0',
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `no_count_hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `no_count_visits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `mese` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_ip`
--

CREATE TABLE `php_stats_ip` (
  `ip` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_ip_zone`
--

CREATE TABLE `php_stats_ip_zone` (
  `ip_from` double NOT NULL DEFAULT '0',
  `ip_to` double NOT NULL DEFAULT '0',
  `tld` char(2) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_langs`
--

CREATE TABLE `php_stats_langs` (
  `lang` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `hits` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_pages`
--

CREATE TABLE `php_stats_pages` (
  `data` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `visits` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `no_count_hits` int(11) NOT NULL DEFAULT '0',
  `no_count_visits` int(11) NOT NULL DEFAULT '0',
  `presence` bigint(20) UNSIGNED DEFAULT '0',
  `tocount` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `lev_1` int(10) NOT NULL DEFAULT '0',
  `lev_2` int(10) NOT NULL DEFAULT '0',
  `lev_3` int(10) NOT NULL DEFAULT '0',
  `lev_4` int(10) NOT NULL DEFAULT '0',
  `lev_5` int(10) NOT NULL DEFAULT '0',
  `lev_6` int(10) NOT NULL DEFAULT '0',
  `outs` int(10) NOT NULL DEFAULT '0',
  `titlePage` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_query`
--

CREATE TABLE `php_stats_query` (
  `data` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `engine` varchar(30) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `domain` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT 'unknown',
  `page` smallint(6) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `date` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `mese` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_referer`
--

CREATE TABLE `php_stats_referer` (
  `data` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `mese` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_stats_systems`
--

CREATE TABLE `php_stats_systems` (
  `os` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `bw` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `reso` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `colo` varchar(10) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `mese` varchar(8) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `phrases`
--

CREATE TABLE `phrases` (
  `phrase` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poll`
--

CREATE TABLE `poll` (
  `nom` varchar(5) COLLATE latin1_german1_ci DEFAULT NULL,
  `valeur` varchar(100) COLLATE latin1_german1_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recrut_comm`
--

CREATE TABLE `recrut_comm` (
  `id` int(6) NOT NULL,
  `id_recrut` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `comment` text COLLATE latin1_german1_ci NOT NULL,
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recrutement`
--

CREATE TABLE `recrutement` (
  `id` int(6) NOT NULL,
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `nom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `prenom` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `sexe` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mensurations` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `age` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `icq` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mail` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `ville` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `connection` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `xp` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `dispo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `section` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `style` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `level` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `battlenet` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lettre` text COLLATE latin1_german1_ci NOT NULL,
  `ip` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `date` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `lu` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `server`
--

CREATE TABLE `server` (
  `id` int(6) NOT NULL,
  `url` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `descr` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shoutbox`
--

CREATE TABLE `shoutbox` (
  `id` int(6) NOT NULL,
  `ip` varchar(15) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `timestamp` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `mess` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shoutbox_config`
--

CREATE TABLE `shoutbox_config` (
  `nom` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `valeur` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sondages`
--

CREATE TABLE `sondages` (
  `id` int(6) NOT NULL,
  `titre` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `nb` int(6) NOT NULL DEFAULT '0',
  `r1` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r2` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r3` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r4` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r5` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r6` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r7` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r8` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r9` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `r10` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `v1` int(6) NOT NULL DEFAULT '0',
  `v2` int(6) NOT NULL DEFAULT '0',
  `v3` int(6) NOT NULL DEFAULT '0',
  `v4` int(6) NOT NULL DEFAULT '0',
  `v5` int(6) NOT NULL DEFAULT '0',
  `v6` int(6) NOT NULL DEFAULT '0',
  `v7` int(6) NOT NULL DEFAULT '0',
  `v8` int(6) NOT NULL DEFAULT '0',
  `v9` int(6) NOT NULL DEFAULT '0',
  `v10` int(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggest`
--

CREATE TABLE `suggest` (
  `id` int(10) NOT NULL,
  `pseudo` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `profil` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `txt` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggest2`
--

CREATE TABLE `suggest2` (
  `id` int(10) NOT NULL,
  `txt` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `test_shoutbox_`
--

CREATE TABLE `test_shoutbox_` (
  `nom` varchar(100) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `valeur` varchar(250) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vacances`
--

CREATE TABLE `vacances` (
  `id` int(6) NOT NULL,
  `annee` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `debut_zonea` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `fin_zonea` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `debut_zoneb` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `fin_zoneb` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `debut_zonec` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `fin_zonec` varchar(200) COLLATE latin1_german1_ci NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `admin_cat`
--
ALTER TABLE `admin_cat`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `anniv`
--
ALTER TABLE `anniv`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`annee`,`mois`);

--
-- Index pour la table `calendrier`
--
ALTER TABLE `calendrier`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `cats_down`
--
ALTER TABLE `cats_down`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `defi`
--
ALTER TABLE `defi`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `domaines`
--
ALTER TABLE `domaines`
  ADD PRIMARY KEY (`domaine`);

--
-- Index pour la table `dossiercomments`
--
ALTER TABLE `dossiercomments`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `dossiers`
--
ALTER TABLE `dossiers`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `dossiers_p`
--
ALTER TABLE `dossiers_p`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `downcomments`
--
ALTER TABLE `downcomments`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `dvconnectes`
--
ALTER TABLE `dvconnectes`
  ADD PRIMARY KEY (`dateDebut`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `ib_admin_logs`
--
ALTER TABLE `ib_admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_admin_sessions`
--
ALTER TABLE `ib_admin_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Index pour la table `ib_announcements`
--
ALTER TABLE `ib_announcements`
  ADD PRIMARY KEY (`announce_id`);

--
-- Index pour la table `ib_attachments`
--
ALTER TABLE `ib_attachments`
  ADD PRIMARY KEY (`attach_id`),
  ADD KEY `attach_pid` (`attach_pid`),
  ADD KEY `attach_msg` (`attach_msg`),
  ADD KEY `attach_post_key` (`attach_post_key`),
  ADD KEY `attach_mid_size` (`attach_member_id`,`attach_filesize`);

--
-- Index pour la table `ib_attachments_type`
--
ALTER TABLE `ib_attachments_type`
  ADD PRIMARY KEY (`atype_id`);

--
-- Index pour la table `ib_badwords`
--
ALTER TABLE `ib_badwords`
  ADD PRIMARY KEY (`wid`);

--
-- Index pour la table `ib_banfilters`
--
ALTER TABLE `ib_banfilters`
  ADD PRIMARY KEY (`ban_id`);

--
-- Index pour la table `ib_bulk_mail`
--
ALTER TABLE `ib_bulk_mail`
  ADD PRIMARY KEY (`mail_id`);

--
-- Index pour la table `ib_cache_store`
--
ALTER TABLE `ib_cache_store`
  ADD PRIMARY KEY (`cs_key`);

--
-- Index pour la table `ib_calendar_events`
--
ALTER TABLE `ib_calendar_events`
  ADD PRIMARY KEY (`eventid`),
  ADD KEY `unix_stamp` (`unix_stamp`);

--
-- Index pour la table `ib_conf_settings`
--
ALTER TABLE `ib_conf_settings`
  ADD PRIMARY KEY (`conf_id`);

--
-- Index pour la table `ib_conf_settings_titles`
--
ALTER TABLE `ib_conf_settings_titles`
  ADD PRIMARY KEY (`conf_title_id`);

--
-- Index pour la table `ib_contacts`
--
ALTER TABLE `ib_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_custom_bbcode`
--
ALTER TABLE `ib_custom_bbcode`
  ADD PRIMARY KEY (`bbcode_id`);

--
-- Index pour la table `ib_email_logs`
--
ALTER TABLE `ib_email_logs`
  ADD PRIMARY KEY (`email_id`),
  ADD KEY `from_member_id` (`from_member_id`),
  ADD KEY `email_date` (`email_date`);

--
-- Index pour la table `ib_emoticons`
--
ALTER TABLE `ib_emoticons`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_faq`
--
ALTER TABLE `ib_faq`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_forum_perms`
--
ALTER TABLE `ib_forum_perms`
  ADD PRIMARY KEY (`perm_id`);

--
-- Index pour la table `ib_forum_tracker`
--
ALTER TABLE `ib_forum_tracker`
  ADD PRIMARY KEY (`frid`);

--
-- Index pour la table `ib_forums`
--
ALTER TABLE `ib_forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position` (`position`,`parent_id`);

--
-- Index pour la table `ib_groups`
--
ALTER TABLE `ib_groups`
  ADD PRIMARY KEY (`g_id`);

--
-- Index pour la table `ib_languages`
--
ALTER TABLE `ib_languages`
  ADD PRIMARY KEY (`lid`);

--
-- Index pour la table `ib_mail_error_logs`
--
ALTER TABLE `ib_mail_error_logs`
  ADD PRIMARY KEY (`mlog_id`);

--
-- Index pour la table `ib_mail_queue`
--
ALTER TABLE `ib_mail_queue`
  ADD PRIMARY KEY (`mail_id`);

--
-- Index pour la table `ib_member_extra`
--
ALTER TABLE `ib_member_extra`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_members`
--
ALTER TABLE `ib_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `mgroup` (`mgroup`),
  ADD KEY `bday_day` (`bday_day`),
  ADD KEY `bday_month` (`bday_month`);

--
-- Index pour la table `ib_members_converge`
--
ALTER TABLE `ib_members_converge`
  ADD PRIMARY KEY (`converge_id`);

--
-- Index pour la table `ib_message_text`
--
ALTER TABLE `ib_message_text`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `msg_date` (`msg_date`),
  ADD KEY `msg_sent_to_count` (`msg_sent_to_count`),
  ADD KEY `msg_deleted_count` (`msg_deleted_count`);

--
-- Index pour la table `ib_message_topics`
--
ALTER TABLE `ib_message_topics`
  ADD PRIMARY KEY (`mt_id`),
  ADD KEY `mt_from_id` (`mt_from_id`),
  ADD KEY `mt_owner_id` (`mt_owner_id`,`mt_to_id`,`mt_vid_folder`);

--
-- Index pour la table `ib_moderator_logs`
--
ALTER TABLE `ib_moderator_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_moderators`
--
ALTER TABLE `ib_moderators`
  ADD PRIMARY KEY (`mid`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Index pour la table `ib_pfields_content`
--
ALTER TABLE `ib_pfields_content`
  ADD PRIMARY KEY (`member_id`);

--
-- Index pour la table `ib_pfields_data`
--
ALTER TABLE `ib_pfields_data`
  ADD PRIMARY KEY (`pf_id`);

--
-- Index pour la table `ib_polls`
--
ALTER TABLE `ib_polls`
  ADD PRIMARY KEY (`pid`);

--
-- Index pour la table `ib_posts`
--
ALTER TABLE `ib_posts`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `topic_id` (`topic_id`,`queued`,`pid`),
  ADD KEY `author_id` (`author_id`,`topic_id`),
  ADD KEY `post_date` (`post_date`);

--
-- Index pour la table `ib_reg_antispam`
--
ALTER TABLE `ib_reg_antispam`
  ADD PRIMARY KEY (`regid`);

--
-- Index pour la table `ib_sessions`
--
ALTER TABLE `ib_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `in_topic` (`in_topic`),
  ADD KEY `in_forum` (`in_forum`);

--
-- Index pour la table `ib_skin_macro`
--
ALTER TABLE `ib_skin_macro`
  ADD PRIMARY KEY (`macro_id`),
  ADD KEY `macro_set` (`macro_set`);

--
-- Index pour la table `ib_skin_sets`
--
ALTER TABLE `ib_skin_sets`
  ADD PRIMARY KEY (`set_skin_set_id`);

--
-- Index pour la table `ib_skin_templates`
--
ALTER TABLE `ib_skin_templates`
  ADD PRIMARY KEY (`suid`);

--
-- Index pour la table `ib_skin_templates_cache`
--
ALTER TABLE `ib_skin_templates_cache`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `template_set_id` (`template_set_id`),
  ADD KEY `template_group_name` (`template_group_name`);

--
-- Index pour la table `ib_spider_logs`
--
ALTER TABLE `ib_spider_logs`
  ADD PRIMARY KEY (`sid`);

--
-- Index pour la table `ib_subscription_currency`
--
ALTER TABLE `ib_subscription_currency`
  ADD PRIMARY KEY (`subcurrency_code`);

--
-- Index pour la table `ib_subscription_extra`
--
ALTER TABLE `ib_subscription_extra`
  ADD PRIMARY KEY (`subextra_id`);

--
-- Index pour la table `ib_subscription_logs`
--
ALTER TABLE `ib_subscription_logs`
  ADD PRIMARY KEY (`sublog_id`);

--
-- Index pour la table `ib_subscription_methods`
--
ALTER TABLE `ib_subscription_methods`
  ADD PRIMARY KEY (`submethod_id`);

--
-- Index pour la table `ib_subscription_trans`
--
ALTER TABLE `ib_subscription_trans`
  ADD PRIMARY KEY (`subtrans_id`);

--
-- Index pour la table `ib_subscriptions`
--
ALTER TABLE `ib_subscriptions`
  ADD PRIMARY KEY (`sub_id`);

--
-- Index pour la table `ib_task_logs`
--
ALTER TABLE `ib_task_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Index pour la table `ib_task_manager`
--
ALTER TABLE `ib_task_manager`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `task_next_run` (`task_next_run`);

--
-- Index pour la table `ib_titles`
--
ALTER TABLE `ib_titles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ib_topic_mmod`
--
ALTER TABLE `ib_topic_mmod`
  ADD PRIMARY KEY (`mm_id`);

--
-- Index pour la table `ib_topics`
--
ALTER TABLE `ib_topics`
  ADD PRIMARY KEY (`tid`),
  ADD KEY `forum_id` (`forum_id`,`approved`,`pinned`),
  ADD KEY `last_post` (`last_post`),
  ADD KEY `topic_firstpost` (`topic_firstpost`);

--
-- Index pour la table `ib_topics_read`
--
ALTER TABLE `ib_topics_read`
  ADD UNIQUE KEY `read_tid_mid` (`read_tid`,`read_mid`);

--
-- Index pour la table `ib_tracker`
--
ALTER TABLE `ib_tracker`
  ADD PRIMARY KEY (`trid`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Index pour la table `ib_upgrade_history`
--
ALTER TABLE `ib_upgrade_history`
  ADD PRIMARY KEY (`upgrade_id`);

--
-- Index pour la table `ib_validating`
--
ALTER TABLE `ib_validating`
  ADD PRIMARY KEY (`vid`),
  ADD KEY `new_reg` (`new_reg`);

--
-- Index pour la table `ib_voters`
--
ALTER TABLE `ib_voters`
  ADD PRIMARY KEY (`vid`);

--
-- Index pour la table `ib_warn_logs`
--
ALTER TABLE `ib_warn_logs`
  ADD PRIMARY KEY (`wlog_id`);

--
-- Index pour la table `idees`
--
ALTER TABLE `idees`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `lan_party`
--
ALTER TABLE `lan_party`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `lan_partycomments`
--
ALTER TABLE `lan_partycomments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `liens`
--
ALTER TABLE `liens`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `liens_down`
--
ALTER TABLE `liens_down`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `matches`
--
ALTER TABLE `matches`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `matchescomments`
--
ALTER TABLE `matchescomments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mynewscomments`
--
ALTER TABLE `mynewscomments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mynewsinfos`
--
ALTER TABLE `mynewsinfos`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `next_matches`
--
ALTER TABLE `next_matches`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `php_stats_cache`
--
ALTER TABLE `php_stats_cache`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `user_id_2` (`user_id`),
  ADD UNIQUE KEY `user_id_3` (`user_id`);

--
-- Index pour la table `php_stats_clicks`
--
ALTER TABLE `php_stats_clicks`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `php_stats_config`
--
ALTER TABLE `php_stats_config`
  ADD PRIMARY KEY (`name`);

--
-- Index pour la table `php_stats_daily`
--
ALTER TABLE `php_stats_daily`
  ADD PRIMARY KEY (`data`);

--
-- Index pour la table `php_stats_domains`
--
ALTER TABLE `php_stats_domains`
  ADD PRIMARY KEY (`tld`);

--
-- Index pour la table `php_stats_downloads`
--
ALTER TABLE `php_stats_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `php_stats_ip`
--
ALTER TABLE `php_stats_ip`
  ADD PRIMARY KEY (`ip`);

--
-- Index pour la table `recrut_comm`
--
ALTER TABLE `recrut_comm`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `recrutement`
--
ALTER TABLE `recrutement`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `server`
--
ALTER TABLE `server`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `shoutbox`
--
ALTER TABLE `shoutbox`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `sondages`
--
ALTER TABLE `sondages`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `suggest`
--
ALTER TABLE `suggest`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `suggest2`
--
ALTER TABLE `suggest2`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `vacances`
--
ALTER TABLE `vacances`
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `admin_cat`
--
ALTER TABLE `admin_cat`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `anniv`
--
ALTER TABLE `anniv`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `calendrier`
--
ALTER TABLE `calendrier`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `cats_down`
--
ALTER TABLE `cats_down`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `defi`
--
ALTER TABLE `defi`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `dossiercomments`
--
ALTER TABLE `dossiercomments`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `dossiers`
--
ALTER TABLE `dossiers`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `dossiers_p`
--
ALTER TABLE `dossiers_p`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `downcomments`
--
ALTER TABLE `downcomments`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_admin_logs`
--
ALTER TABLE `ib_admin_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_announcements`
--
ALTER TABLE `ib_announcements`
  MODIFY `announce_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_attachments`
--
ALTER TABLE `ib_attachments`
  MODIFY `attach_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_attachments_type`
--
ALTER TABLE `ib_attachments_type`
  MODIFY `atype_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_badwords`
--
ALTER TABLE `ib_badwords`
  MODIFY `wid` int(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_banfilters`
--
ALTER TABLE `ib_banfilters`
  MODIFY `ban_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_bulk_mail`
--
ALTER TABLE `ib_bulk_mail`
  MODIFY `mail_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_calendar_events`
--
ALTER TABLE `ib_calendar_events`
  MODIFY `eventid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_conf_settings`
--
ALTER TABLE `ib_conf_settings`
  MODIFY `conf_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_conf_settings_titles`
--
ALTER TABLE `ib_conf_settings_titles`
  MODIFY `conf_title_id` smallint(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_contacts`
--
ALTER TABLE `ib_contacts`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_custom_bbcode`
--
ALTER TABLE `ib_custom_bbcode`
  MODIFY `bbcode_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_email_logs`
--
ALTER TABLE `ib_email_logs`
  MODIFY `email_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_emoticons`
--
ALTER TABLE `ib_emoticons`
  MODIFY `id` smallint(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_faq`
--
ALTER TABLE `ib_faq`
  MODIFY `id` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_forum_perms`
--
ALTER TABLE `ib_forum_perms`
  MODIFY `perm_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_forum_tracker`
--
ALTER TABLE `ib_forum_tracker`
  MODIFY `frid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_groups`
--
ALTER TABLE `ib_groups`
  MODIFY `g_id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_languages`
--
ALTER TABLE `ib_languages`
  MODIFY `lid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_mail_error_logs`
--
ALTER TABLE `ib_mail_error_logs`
  MODIFY `mlog_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_mail_queue`
--
ALTER TABLE `ib_mail_queue`
  MODIFY `mail_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_members_converge`
--
ALTER TABLE `ib_members_converge`
  MODIFY `converge_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_message_text`
--
ALTER TABLE `ib_message_text`
  MODIFY `msg_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_message_topics`
--
ALTER TABLE `ib_message_topics`
  MODIFY `mt_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_moderator_logs`
--
ALTER TABLE `ib_moderator_logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_moderators`
--
ALTER TABLE `ib_moderators`
  MODIFY `mid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_pfields_data`
--
ALTER TABLE `ib_pfields_data`
  MODIFY `pf_id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_polls`
--
ALTER TABLE `ib_polls`
  MODIFY `pid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_posts`
--
ALTER TABLE `ib_posts`
  MODIFY `pid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_skin_macro`
--
ALTER TABLE `ib_skin_macro`
  MODIFY `macro_id` smallint(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_skin_sets`
--
ALTER TABLE `ib_skin_sets`
  MODIFY `set_skin_set_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_skin_templates`
--
ALTER TABLE `ib_skin_templates`
  MODIFY `suid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_spider_logs`
--
ALTER TABLE `ib_spider_logs`
  MODIFY `sid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_subscription_extra`
--
ALTER TABLE `ib_subscription_extra`
  MODIFY `subextra_id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_subscription_logs`
--
ALTER TABLE `ib_subscription_logs`
  MODIFY `sublog_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_subscription_methods`
--
ALTER TABLE `ib_subscription_methods`
  MODIFY `submethod_id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_subscription_trans`
--
ALTER TABLE `ib_subscription_trans`
  MODIFY `subtrans_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_subscriptions`
--
ALTER TABLE `ib_subscriptions`
  MODIFY `sub_id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_task_logs`
--
ALTER TABLE `ib_task_logs`
  MODIFY `log_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_task_manager`
--
ALTER TABLE `ib_task_manager`
  MODIFY `task_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_titles`
--
ALTER TABLE `ib_titles`
  MODIFY `id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_topic_mmod`
--
ALTER TABLE `ib_topic_mmod`
  MODIFY `mm_id` smallint(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_topics`
--
ALTER TABLE `ib_topics`
  MODIFY `tid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_tracker`
--
ALTER TABLE `ib_tracker`
  MODIFY `trid` mediumint(8) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_upgrade_history`
--
ALTER TABLE `ib_upgrade_history`
  MODIFY `upgrade_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_voters`
--
ALTER TABLE `ib_voters`
  MODIFY `vid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ib_warn_logs`
--
ALTER TABLE `ib_warn_logs`
  MODIFY `wlog_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `idees`
--
ALTER TABLE `idees`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `lan_party`
--
ALTER TABLE `lan_party`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `lan_partycomments`
--
ALTER TABLE `lan_partycomments`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `liens`
--
ALTER TABLE `liens`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `liens_down`
--
ALTER TABLE `liens_down`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `matchescomments`
--
ALTER TABLE `matchescomments`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mynewscomments`
--
ALTER TABLE `mynewscomments`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `mynewsinfos`
--
ALTER TABLE `mynewsinfos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `next_matches`
--
ALTER TABLE `next_matches`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `php_stats_clicks`
--
ALTER TABLE `php_stats_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `php_stats_downloads`
--
ALTER TABLE `php_stats_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `recrut_comm`
--
ALTER TABLE `recrut_comm`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `recrutement`
--
ALTER TABLE `recrutement`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `server`
--
ALTER TABLE `server`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `sondages`
--
ALTER TABLE `sondages`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `suggest`
--
ALTER TABLE `suggest`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `suggest2`
--
ALTER TABLE `suggest2`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vacances`
--
ALTER TABLE `vacances`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
