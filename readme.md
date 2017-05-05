# Mediaz

This repository hold the sources of "Mediaz" project, a website with a multimedia database.

This project is made for the 2nd year of DUT Informatique (BSc. in computing) at the IUT of Clermont Ferrand, France

**Authors:**
* Corentin Billet <corentin.billet@etu.udamail.fr>
* Adam Cafière <adam.cafiere@etu.udamail.fr>
* Jules Cournut <jules.cournut@etu.udamail.fr>
* Aurélien Pillevesse <aurelien.pillevesse@etu.udamail.fr>
* Benjamin Roziere <benjamin.roziere@etu.udamail.fr>

Special thanks to Mr Chafik Samir our tutor on this project.

## Install instructions

#### Requirements
To run Mediaz, you must have the followings softwares installed:
* Node.JS (with npm)
* php (cli) >= 5.0.0
* composer
* A MySQL database up and running

### Building

1. At root folder run `$ npm install`
2. `cd` to `/client/ScorpionClient` and run `$ npm install` and `$ bower install`, this will install the client's assets
3. `cd` to `/server/ScorpionServer` and run `$ composer install`
4. Follow the install script of composer
5. To pack the entire project, in project's root folder run `$ ./build.sh`

### Launching for the first time
1. `cd` to `/build` and run `$ php app/console doctrine:database:create`
2. Run `$ php app/console doctrine:schema:create`
3. Run the scripts at "Adding Mysql Levenshtein Functions" to enable the search system
4. To start the server, you'll only need to do `$ php app/console server:run`
5. You can now browse Mediaz at `http://localhost:8000`

### Adding Mysql Levenshtein Functions
You need to add theses functions in order to enable the search system

```sql
CREATE DEFINER=`root`@`localhost` FUNCTION `LEVENSHTEIN`(`s1` VARCHAR(255), `s2` VARCHAR(255)) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
    DECLARE s1_char CHAR;
    DECLARE cv0, cv1 VARBINARY(256);
    SET s1_len = CHAR_LENGTH(s1), s2_len = CHAR_LENGTH(s2), cv1 = 0x00, j = 1, i = 1, c = 0;
    IF s1 = s2 THEN
        RETURN 0;
    ELSEIF s1_len = 0 THEN
        RETURN s2_len;
    ELSEIF s2_len = 0 THEN
        RETURN s1_len;
    ELSE
        WHILE j <= s2_len DO
            SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1;
        END WHILE;
        WHILE i <= s1_len DO
            SET s1_char = SUBSTRING(s1, i, 1), c = i, cv0 = UNHEX(HEX(i)), j = 1;
            WHILE j <= s2_len DO
                SET c = c + 1;
                IF s1_char = SUBSTRING(s2, j, 1) THEN SET cost = 0; ELSE SET cost = 1; END IF;
                SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost;
                IF c > c_temp THEN SET c = c_temp; END IF;
                SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1;
                IF c > c_temp THEN SET c = c_temp; END IF;
                SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1;
            END WHILE;
            SET cv1 = cv0, i = i + 1;
        END WHILE;
    END IF;
    RETURN c;
END
```

```sql
CREATE DEFINER=`root`@`localhost` FUNCTION `LEVENSHTEIN_RATIO`(`s1` VARCHAR(255), `s2` VARCHAR(255)) RETURNS int(11)
    DETERMINISTIC
BEGIN
    DECLARE s1_len, s2_len, max_len INT;
    SET s1_len = LENGTH(s1), s2_len = LENGTH(s2);
    IF s1_len > s2_len THEN SET max_len = s1_len; ELSE SET max_len = s2_len; END IF;
    RETURN ROUND((1 - LEVENSHTEIN(s1, s2) / max_len) * 100);
END
```

```sql
CREATE DEFINER=`root`@`localhost` FUNCTION `LEVENSHTEIN_RATIO_IN`(`s1` VARCHAR(255), `lst` VARCHAR(4000), `min_ratio` INT(11)) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN
    DECLARE lst_len, i INT;
    DECLARE item VARCHAR(255);
    SET lst_len = LENGTH(lst), i = 1;
    WHILE i <= lst_len DO
    	SET item = REPLACE(REPLACE(SUBSTRING_INDEX(lst,',',i),SUBSTRING_INDEX(lst,',',i-1),''), ',','');
        IF LEVENSHTEIN_RATIO(s1, item) >= min_ratio THEN RETURN 1; END IF;
        SET i = i+1;
    END WHILE;    
    RETURN 0;
END
```
