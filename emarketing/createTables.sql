-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
-- 
-- Servidor: localhost
-- Tempo de Geração: Mai 25, 2007 as 03:28 PM
-- Versão do Servidor: 4.1.12
-- Versão do PHP: 5.0.4
-- 
-- Banco de Dados: `mailing`
-- 

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `mailing`
-- 

CREATE TABLE `mailing` (
  `id` int(11) NOT NULL auto_increment,
  `nome` varchar(50) default NULL,
  `uf` char(2) default NULL,
  `email` varchar(100) default NULL,
  `categ` tinyint(2) NOT NULL default '0',
  `dataReg` datetime default '0000-00-00 00:00:00',
  `dataEnvio` datetime default '0000-00-00 00:00:00',
  `arquivo` varchar(100) default NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM;

-- 
-- Estrutura da tabela `mailing_categ`
-- 

CREATE TABLE `mailing_categ` (
  `id` tinyint(2) NOT NULL auto_increment,
  `categ` varchar(50) NOT NULL default '',
  `subCat` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `categ` (`categ`)
) ENGINE=MyISAM;

-- 
-- Extraindo dados da tabela `mailing_categ`
-- 

INSERT INTO `mailing_categ` (`id`, `categ`, `subCat`) VALUES (1, 'INFORMATICA', 0),
(2, 'IMÓVEIS', 0),
(3, 'SERVIÇOS', 0),
(4, 'QUALQUER CATEGORIA', 0);

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `mailing_returns`
-- 

CREATE TABLE `mailing_returns` (
  `id` int(11) NOT NULL default '0',
  `dataRetorno` datetime NOT NULL default '0000-00-00 00:00:00',
  `campanha` varchar(255) default NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `mailing_setup`
-- 

CREATE TABLE `mailing_setup` (
  `empresa` varchar(100) NOT NULL default '',
  `urlSite` varchar(100) NOT NULL default '',
  `homePage` varchar(30) NOT NULL default '',
  `mailFrom` varchar(100) NOT NULL default '',
  `returnPath` varchar(100) NOT NULL default '',
  `bgTop` varchar(50) NOT NULL default '',
  `bgBottom` varchar(50) NOT NULL default '',
  `warning` mediumtext NOT NULL
) ENGINE=MyISAM;

-- 
-- Extraindo dados da tabela `mailing_setup`
-- 

INSERT INTO `mailing_setup` (`empresa`, `urlSite`, `homePage`, `mailFrom`, `returnPath`, `bgTop`, `bgBottom`, `warning`) VALUES ('PUBLICIDADE NA NET', 'http://10.1.1.110/e-mailing', 'index.php', 'webmaster@teste.com.br', 'webmaster@teste.com.br', 'images/topoEmail.png', 'images/rodapeEmail.png', 'A #_coName preza muito a sua privacidade, caso não tenha interesse em receber nossos informativos, <a href=mailto:?subject=remover:#email>Clique aqui</a>');

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `mailing_users`
-- 

CREATE TABLE `mailing_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) default NULL,
  `password` varchar(20) default NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Extraindo dados da tabela `mailing_users`
-- 

INSERT INTO `mailing_users` (`id`, `username`, `password`, `status`) VALUES (1, 'LAURO', 'britto', 0);
