-- Run this in your Neon SQL Editor

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    phone TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS verification (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    full_name TEXT NOT NULL,
    problem TEXT NOT NULL,
    security_pin TEXT NOT NULL,
    experience_level TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
