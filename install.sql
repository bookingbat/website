
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `phone` int(10) NOT NULL,
  `business_name` varchar(150) NOT NULL,
  `owner_name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
