CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    nickname VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    password_token VARCHAR(64) NULL,
    password_token_expires DATETIME NULL,
    created_at DATETIME,
    updated_at DATETIME,
    is_public TINYINT DEFAULT 1,
    height_cm INT NULL,
    birth_year INT NULL,
    activity_level VARCHAR(20) NULL,
    start_weight DECIMAL(5,2) NULL,
    goal_weight DECIMAL(5,2) NULL,
    goal_date DATE NULL,
    streak_freeze_credits INT DEFAULT 1,
    deleted_at DATETIME NULL
);

CREATE TABLE weigh_ins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    weight_kg DECIMAL(5,2) NOT NULL,
    note TEXT NULL,
    water_l DECIMAL(4,2) NULL,
    steps INT NULL,
    created_at DATETIME,
    UNIQUE KEY uniq_user_date (user_id, date),
    INDEX idx_user_date (user_id, date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,
    title VARCHAR(150),
    description TEXT
);

CREATE TABLE user_badges (
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    awarded_at DATETIME,
    PRIMARY KEY (user_id, badge_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE,
    filename VARCHAR(255),
    note TEXT NULL,
    created_at DATETIME,
    INDEX idx_photo_user_date (user_id, date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    owner_user_id INT,
    invite_code VARCHAR(50) UNIQUE,
    is_closed TINYINT DEFAULT 0,
    created_at DATETIME,
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE group_members (
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner','member') DEFAULT 'member',
    joined_at DATETIME,
    PRIMARY KEY (group_id, user_id),
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,
    title VARCHAR(150),
    description TEXT,
    type ENUM('global','group'),
    start_date DATE NULL,
    end_date DATE NULL,
    rules_json TEXT,
    created_at DATETIME
);

CREATE TABLE user_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT NOT NULL,
    user_id INT NOT NULL,
    group_id INT NULL,
    status ENUM('active','completed') DEFAULT 'active',
    progress_json TEXT,
    started_at DATETIME,
    completed_at DATETIME NULL,
    UNIQUE KEY uniq_user_challenge (challenge_id, user_id, group_id),
    FOREIGN KEY (challenge_id) REFERENCES challenges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NULL,
    ip VARCHAR(45),
    success TINYINT,
    created_at DATETIME,
    INDEX idx_ip_created (ip, created_at),
    INDEX idx_email_created (email, created_at)
);
