
--
-- Table structure for table `gmail_attachments`
--

CREATE TABLE IF NOT EXISTS `gmail_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attachment_id` text NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `attachment_date` datetime NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gmail_messages`
--

CREATE TABLE IF NOT EXISTS `gmail_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` varchar(255) NOT NULL,
  `thread_id` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `message_date` datetime NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `mail_content` longtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gmail_threads`
--

CREATE TABLE IF NOT EXISTS `gmail_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` varchar(255) NOT NULL,
  `snippet_text` text NOT NULL,
  `history_id` varchar(255) NOT NULL,
  `last_message_date` datetime NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(255) NOT NULL,
  `from_email` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gmail_users`
--

CREATE TABLE IF NOT EXISTS `gmail_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `next_page_token` text NOT NULL,
  `last_fetched` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
