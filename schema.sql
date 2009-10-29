-- Right now this is just a phpMyAdmin dump - I'll clean it up later..
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `api_access_tokens`
-- 

CREATE TABLE `api_access_tokens` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `consumerid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `key` char(16) collate utf8_unicode_ci NOT NULL,
  `secret` char(28) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `consumerid` (`consumerid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `api_consumers`
-- 

CREATE TABLE `api_consumers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `key` char(16) collate utf8_unicode_ci NOT NULL,
  `secret` char(28) collate utf8_unicode_ci NOT NULL,
  `check_nonce` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `api_nonces`
-- 

CREATE TABLE `api_nonces` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `consumerid` int(10) unsigned NOT NULL,
  `token` tinytext collate utf8_unicode_ci NOT NULL,
  `nonce` tinytext collate utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `consumerid` (`consumerid`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `api_request_tokens`
-- 

CREATE TABLE `api_request_tokens` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `consumerid` int(10) unsigned NOT NULL,
  `userid` int(10) unsigned default NULL,
  `key` char(16) collate utf8_unicode_ci NOT NULL,
  `secret` char(28) collate utf8_unicode_ci NOT NULL,
  `callback` varchar(255) collate utf8_unicode_ci default NULL,
  `authorized` tinyint(1) NOT NULL,
  `verifier` varchar(16) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `consumerid` (`consumerid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) collate utf8_unicode_ci NOT NULL,
  `kitten` enum('able','baker','charlie','dog','easy','fox') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB;

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `api_access_tokens`
-- 
ALTER TABLE `api_access_tokens`
  ADD CONSTRAINT `api_access_tokens_ibfk_1` FOREIGN KEY (`consumerid`) REFERENCES `api_consumers` (`id`),
  ADD CONSTRAINT `api_access_tokens_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);

-- 
-- Constraints for table `api_consumers`
-- 
ALTER TABLE `api_consumers`
  ADD CONSTRAINT `api_consumers_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);

-- 
-- Constraints for table `api_nonces`
-- 
ALTER TABLE `api_nonces`
  ADD CONSTRAINT `api_nonces_ibfk_1` FOREIGN KEY (`consumerid`) REFERENCES `api_consumers` (`id`);

-- 
-- Constraints for table `api_request_tokens`
-- 
ALTER TABLE `api_request_tokens`
  ADD CONSTRAINT `api_request_tokens_ibfk_1` FOREIGN KEY (`consumerid`) REFERENCES `api_consumers` (`id`),
  ADD CONSTRAINT `api_request_tokens_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);
