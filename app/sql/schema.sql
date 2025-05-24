CREATE TABLE roles (
    role_id SERIAL PRIMARY KEY,
    role_name VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO roles (role_name) VALUES ('user'), ('pro'), ('admin');

CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    role_id INT NOT NULL DEFAULT 1 REFERENCES roles(role_id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE decks (
    deck_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    user_id INT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_studied_at TIMESTAMP
);

CREATE TABLE flashcards (
    card_id SERIAL PRIMARY KEY,
    deck_id INT NOT NULL REFERENCES decks(deck_id) ON DELETE CASCADE,
    front TEXT NOT NULL,
    back TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE flashcard_progress (
    user_id INT REFERENCES users(user_id) ON DELETE CASCADE,
    card_id INT REFERENCES flashcards(card_id) ON DELETE CASCADE,
    status VARCHAR(10) NOT NULL CHECK (status IN ('bad', 'ok', 'good')),
    seen_good_count INT DEFAULT 0,
    next_review_at TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, card_id)
);
