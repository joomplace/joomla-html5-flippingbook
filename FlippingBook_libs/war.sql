-- phpMyAdmin SQL Dump
-- version 3.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 20, 2012 at 07:08 PM
-- Server version: 5.1.40
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `war`
--

-- --------------------------------------------------------

--
-- Table structure for table `Article`
--

CREATE TABLE IF NOT EXISTS `Article` (
  `Key` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID/Name` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `locale` varchar(15) NOT NULL,
  `numberOfPages` int(5) NOT NULL,
  `order` int(5) NOT NULL,
  `stub` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`Key`),
  KEY `ID/Name` (`ID/Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `Article`
--

INSERT INTO `Article` (`Key`, `ID/Name`, `active`, `hidden`, `locale`, `numberOfPages`, `order`, `stub`, `subtitle`, `title`) VALUES
(1, 'en-US|foreword', 1, 1, 'en-US', 3, 0, 'foreword', '', 'foreword to 20 things'),
(2, 'en-US|what-is-the-internet', 1, NULL, 'en-US', 3, 1, 'what-is-the-internet', 'what-is-the-internet', 'what-is-the-internet?'),
(3, 'en-US|cloud-computing', 1, NULL, 'en-US', 2, 2, 'cloud-computing', 'cloud-computing', 'cloud-computing'),
(4, 'en-US|theend', 1, 1, 'en-US', 1, 3, 'theend', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `Page`
--

CREATE TABLE IF NOT EXISTS `Page` (
  `Key` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID/Name` varchar(255) NOT NULL,
  `content` text,
  `locale` varchar(15) NOT NULL,
  `pageNumber` int(5) NOT NULL,
  `stub` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  PRIMARY KEY (`Key`),
  KEY `ID/Name` (`ID/Name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `Page`
--

INSERT INTO `Page` (`Key`, `ID/Name`, `content`, `locale`, `pageNumber`, `stub`, `template`) VALUES
(1, 'en-US|foreword|1', '<div class=''image1''><img src='''' data-src=''/media1/illustrations/cloud01.png'' /></div><div class=''left''><p class=''drop-cap''></p></div><div class=''right''><p class=''continuation''>First page text</p></div>', 'en-US', 1, 'foreword', 'template-start-7'),
(2, 'en-US|foreword|2', '<div class=''left''><p class=''continuation''>First page text</p></div><div class=''right''>First page text</p></div>', 'en-US', 2, 'foreword', 'template-inner-6'),
(3, 'en-US|foreword|3', '<p>First page text</p><div class=''spacer'' /><p class=''continuation''><em>First page text</em></p><p class=''continuation''><em>First page text</em></p>', 'en-US', 3, 'foreword', 'template-inner-7'),
(4, 'en-US|what-is-the-internet|1', '&lt;div class=''image1''&gt;&lt;img src='''' data-src=''/media/illustrations/internet01.png'' /&gt;&lt;/div&gt;&lt;div class=''left''&gt;&lt;p class=''drop-cap''&gt;Second page text', 'en-US', 1, 'what-is-the-internet', 'template-start-7'),
(5, 'en-US|what-is-the-internet|2', '<div class=''left''><p class=''continuation''>Second page text</p></div><div class=''right''>Second page text</p></div>', 'en-US', 2, 'what-is-the-internet', 'template-inner-5'),
(6, 'en-US|what-is-the-internet|3', '<p>Second page text</p><div class=''spacer'' /><p class=''continuation''><em>Second page text</em></p><p class=''continuation''><em>Second page text</em></p>', 'en-US', 3, 'what-is-the-internet', 'template-inner-2'),
(7, 'en-US|cloud-computing|1', '&lt;div class=''image1''&gt;&lt;img src='''' data-src=''/media/illustrations/cloud03.png'' /&gt;&lt;/div&gt;&lt;div class=''left''&gt;&lt;p class=''drop-cap''&gt;Third page text",''1'' =>"<div class=''left''><p class=''continuation''>Third page text</p></div><div class=''right''>Third page text</p></div>', 'en-US', 1, 'cloud-computing', 'template-start-5'),
(8, 'en-US|cloud-computing|2', '<p>Third page text</p><div class=''spacer'' /><p class=''continuation''><em>Third page text</em></p><p class=''continuation''><em>Third page text</em></p>', 'en-US', 2, 'cloud-computing', 'template-inner-5'),
(9, 'en-US|theend|1', '<h2></h2>\r\n<h3></h3>\r\n<p></p>', 'en-US', 1, 'theend', 'template-3');
