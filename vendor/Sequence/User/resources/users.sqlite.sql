CREATE TABLE IF NOT EXISTS "groups" (
  "name" varchar(255) NOT NULL,
  "roles" text NOT NULL,
  "real_name" varchar(255) NOT NULL,
  "description" text NOT NULL
);

CREATE TABLE IF NOT EXISTS "users" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
  "name" varchar(32) NOT NULL UNIQUE,
  "email" varchar(64) NOT NULL UNIQUE,
  "password" varchar(64) NOT NULL,
  "enabled" integer(4) NOT NULL DEFAULT "0",
  "role" varchar(30) NOT NULL DEFAULT "ROLE_USER",
  "ip" varchar(40) NOT NULL,
  "last_login" datetime NOT NULL,
  "date_created" datetime NOT NULL
);

CREATE TABLE IF NOT EXISTS "users_custom_field" (
  "user_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS "users_groups" (
  "group_name" varchar(255) NOT NULL,
  "user_id" INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "users_online" (
  "sess_id" varchar(64) NOT NULL UNIQUE,
  "user_id" INTEGER NOT NULL DEFAULT "0",
  "user_ip" varchar(40) NOT NULL,
  "user_role" varchar(30) NOT NULL DEFAULT "ROLE_GUEST",
  "user_agent" varchar(255) NOT NULL,
  "last_active" datetime NOT NULL
);