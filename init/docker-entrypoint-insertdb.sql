INSERT INTO users (username, email, hashed_password, birthdate, phone, profile_uuid, created_at) 
VALUES ("barb3", "barb3@salle.url.edu", "$2y$10$0yXuOU0wg1VRg.jharav/ue1oHo5dnD8zgJWDZpFlG7aIc34KrqBe", "1999-05-05", "904-203-405", "default_picture.png", "2021-05-06 10:38:20"),
        ("barb4", "barb4@salle.url.edu", "$2y$10$0yXuOU0wg1VRg.jharav/ue1oHo5dnD8zgJWDZpFlG7aIc34KrqBe", "1999-05-05", "904-203-405", "default_picture.png", "2021-05-06 10:38:20");

INSERT INTO tokens (token_value, is_redeemed, user_id) 
VALUES ("barb3", 1, 1), ("barb4", 1, 2);

INSERT INTO money (amount, user_id) 
VALUES (100, 1), (101, 2);