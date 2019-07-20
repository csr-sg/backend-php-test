INSERT INTO users (username, password_hash) VALUES
('user1', '$argon2i$v=19$m=1024,t=2,p=2$c1dQcnRGanB4SS8zblhyQQ$vC8Rx2JfqssQCcKDlIerenUGpUEkU5B6Jv23xs32r/A'),   /* password_hash('user1', PASSWORD_ARGON2I) */
('user2', '$argon2i$v=19$m=1024,t=2,p=2$ZktEMmV5QVpKRXZlaHFCNg$9ThEB0htE8bz2J6rEkTQWudOncIxUANqcpzGZs+iDf8'),   /* password_hash('user2', PASSWORD_ARGON2I) */
('user3', '$argon2i$v=19$m=1024,t=2,p=2$cC52Z2xRQjZ0L2tEQmVocQ$YUfXrDfEzsNG2IjtSCR1o9zxw1usw5BECnF13cwlFBU');   /* password_hash('user3', PASSWORD_ARGON2I) */

INSERT INTO todos (user_id, description) VALUES
(1, 'Vivamus tempus'),
(1, 'lorem ac odio'),
(1, 'Ut congue odio'),
(1, 'Sodales finibus'),
(1, 'Accumsan nunc vitae'),
(2, 'Lorem ipsum'),
(2, 'In lacinia est'),
(2, 'Odio varius gravida');