CREATE TABLE IF NOT EXISTS `USER` (
        `id` INT NOT NULL AUTO_INCREMENT
        ,`username` VARCHAR(100) NOT NULL
        ,`passwd` VARCHAR(60) NOT NULL
        ,PRIMARY KEY (`id`)
        ,UNIQUE (`username`)
);
