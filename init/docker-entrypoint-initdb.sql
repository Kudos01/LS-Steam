CREATE TABLE users (
    user_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL,
    email VARCHAR(50) NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL,
    phone VARCHAR(11),
    profile_uuid VARCHAR(50),
    created_at DATETIME NOT NULL,
PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tokens (
    token_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    token_value VARCHAR(20) NOT NULL, 
    is_redeemed BOOLEAN NOT NULL,
    user_id INT(11) unsigned NOT NULL,
PRIMARY KEY (token_id),
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE user_games (
    game_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    game_shark_id INT(11) NOT NULL,
    user_id INT(11) unsigned NOT NULL,
PRIMARY KEY (game_id),
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE friend_requests (
   request_id INT unsigned NOT NULL AUTO_INCREMENT,
   friend_request_from_id INT unsigned NOT NULL,
   friend_request_to_id INT unsigned NOT NULL,
   accepted INT unsigned NOT NULL,
   accept_date VARCHAR(255),
   PRIMARY KEY (request_id),
   FOREIGN KEY (friend_request_from_id) REFERENCES users(user_id) ON DELETE CASCADE,
   FOREIGN KEY (friend_request_to_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE money (
    money_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    amount   DOUBLE NOT NULL,
    user_id  INT(11) unsigned NOT NULL,
PRIMARY KEY (money_id),
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE wishlist (
    wishlist_id INT(11) unsigned NOT NULL AUTO_INCREMENT,
    game_shark_id INT(11) NOT NULL,
    user_id INT(11) unsigned NOT NULL,
PRIMARY KEY (wishlist_id),
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;