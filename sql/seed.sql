INSERT INTO users (email, nickname, password_hash, created_at, updated_at, is_public, start_weight, goal_weight, goal_date)
VALUES
('alice@example.com','AliceFit', '$2y$10$tmYi5Z6JVpazlRtWLpmjXeQCloxWdj3kJ3D06bAJeZXL9SFn2xlG2', NOW(), NOW(),1,95,80, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
('bob@example.com','BobPower', '$2y$10$tmYi5Z6JVpazlRtWLpmjXeQCloxWdj3kJ3D06bAJeZXL9SFn2xlG2', NOW(), NOW(),1,110,90, DATE_ADD(CURDATE(), INTERVAL 120 DAY)),
('carla@example.com','CarlaTeam', '$2y$10$tmYi5Z6JVpazlRtWLpmjXeQCloxWdj3kJ3D06bAJeZXL9SFn2xlG2', NOW(), NOW(),0,78,65, DATE_ADD(CURDATE(), INTERVAL 100 DAY));

INSERT INTO weigh_ins (user_id, date, weight_kg, note, water_l, steps, created_at) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 95.0, 'Start', 2.0, 8000, NOW()),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 94.0, 'Läuft', 2.2, 9000, NOW()),
(1, CURDATE(), 93.5, 'Top', 2.5, 10000, NOW()),
(2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 110.0, 'Los gehts', 1.8, 7000, NOW()),
(2, CURDATE(), 108.0, 'weiter', 2.0, 8500, NOW()),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 78.0, 'Start', 2.0, 6000, NOW());

INSERT INTO badges (code, title, description) VALUES
('first_entry','Erster Eintrag','Du hast deinen ersten Eintrag erstellt'),
('streak_7','7 Tage Serie','Eine Woche durchgezogen'),
('loss_1kg','1 kg geschafft','Erste Kilo verloren'),
('loss_5kg','5 kg geschafft','Fünf Kilo verloren'),
('loss_10kg','10 kg geschafft','Zehn Kilo verloren'),
('goal_reached','Ziel erreicht','Dein Zielgewicht ist erreicht'),
('month_full','Monat komplett','25 Einträge in 30 Tagen'),
('consistency','Konstanz','Über 8 Wochen mindestens 3 Einträge/Woche');

INSERT INTO user_badges (user_id, badge_id, awarded_at) VALUES
(1, 1, NOW());

INSERT INTO groups (name, owner_user_id, invite_code, created_at) VALUES
('Team Blau', 1, 'TEAMBLUE', NOW()),
('Schrittzähler', 2, 'SCHRITT', NOW());

INSERT INTO group_members (group_id, user_id, role, joined_at) VALUES
(1, 1, 'owner', NOW()),
(1, 3, 'member', NOW()),
(2, 2, 'owner', NOW());

INSERT INTO challenges (code, title, description, type, start_date, end_date, rules_json, created_at) VALUES
('30_days_log','30 Tage Eintragen','Jeden Tag eintragen', 'global', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '{}', NOW()),
('8w_consistency','8 Wochen Konstanz','3 Einträge/Woche', 'global', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 56 DAY), '{}', NOW()),
('steps_10k','10k Schritte','30 Tage lang >=10k Schritte', 'global', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '{}', NOW());

INSERT INTO user_challenges (challenge_id, user_id, status, progress_json, started_at) VALUES
(1,1,'active','{"percent":20}',NOW()),
(2,1,'active','{"percent":10}',NOW());
