CREATE TABLE "log" (
  "id"          SERIAL NOT NULL PRIMARY KEY,
  "level"       INT,
  "category"    VARCHAR(256),
  "log_time"    DOUBLE PRECISION,
  "prefix"      TEXT,
  "message"     TEXT
);


CREATE TABLE "session" (
  id CHAR(40) NOT NULL PRIMARY KEY,
  expire INT ,
  data BYTEA
);


CREATE TABLE "cache" (
  id CHAR(128) NOT NULL PRIMARY KEY,
  expire INT,
  data BYTEA
);


CREATE TABLE "source_message" (
  id SERIAL PRIMARY KEY,
  category VARCHAR(32) DEFAULT 'app',
  message VARCHAR(256)
);
CREATE UNIQUE INDEX message_id ON "source_message" USING btree ("id");


CREATE TABLE "message" (
  "id" INT,
  "language" VARCHAR(16) DEFAULT 'ru',
  "translation" VARCHAR(256),
  PRIMARY KEY (id, language),
  CONSTRAINT fk_message_source_message FOREIGN KEY (id)
  REFERENCES source_message (id) ON DELETE CASCADE ON UPDATE RESTRICT
);


CREATE VIEW "translation" AS
  SELECT s.id, message, translation
  FROM source_message s JOIN message t ON s.id = t.id;


CREATE TABLE "user" (
  id SERIAL PRIMARY KEY NOT NULL,
  name VARCHAR(24) NOT NULL,
  account DECIMAL(8,2) NOT NULL DEFAULT '0.00',
  email VARCHAR(48) NOT NULL,
  hash CHAR(60),
  auth CHAR(64) UNIQUE,
  code CHAR(64),
  duration INT NOT NULL DEFAULT 60,
  "timezone" VARCHAR(32),
  country CHAR(2),
  status SMALLINT NOT NULL DEFAULT 2,
  perfect CHAR(8),
  skype VARCHAR(32),
  forename VARCHAR(24),
  surname VARCHAR(24),
  phone VARCHAR(16),
  data BYTEA
);
CREATE UNIQUE INDEX user_id ON "user" USING btree ("id");
CREATE UNIQUE INDEX user_name ON "user" USING btree ("name");

INSERT INTO "user"(name, email, status) VALUES ('admin', 'lab_tas@ukr.net', 1);


CREATE TABLE "journal" (
  id SERIAL PRIMARY KEY NOT NULL,
  type VARCHAR(16) NOT NULL,
  event VARCHAR(16) NOT NULL,
  object_id INT,
  data TEXT,
  user_name VARCHAR(24),
  time TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
  ip INET,
  CONSTRAINT journal_user FOREIGN KEY (user_name)
  REFERENCES "user"(name)
  ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE "invoice" (
  id SERIAL PRIMARY KEY NOT NULL,
  user_name VARCHAR(24) NOT NULL,
  amount DECIMAL(8,2) NOT NULL,
  batch BIGINT,
  status VARCHAR(16) DEFAULT 'create',
  CONSTRAINT invoice_user FOREIGN KEY (user_name)
  REFERENCES "user"(name)
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT amount CHECK (amount <> 0)
);
CREATE UNIQUE INDEX invoice_id ON "invoice" USING btree ("id");


CREATE TABLE "type" (
  id     SMALLINT NOT NULL PRIMARY KEY,
  stake  SMALLINT NOT NULL,
  income SMALLINT NOT NULL
);

INSERT INTO "type"
  (id, stake, income) VALUES
  (1,  20,    38),
  (2,  100,   190),
  (3,  300,   580);


CREATE TABLE "account" (
  "profit" NUMERIC(8,2) NOT NULL DEFAULT 0
);
INSERT INTO "account" VALUES (0);


CREATE TABLE "node" (
  id SERIAL PRIMARY KEY NOT NULL,
  user_name VARCHAR(24) NOT NULL,
  type_id SMALLINT NOT NULL,
  count SMALLINT NOT NULL,
  time INT NOT NULL,
  CONSTRAINT node_user FOREIGN KEY (user_name)
  REFERENCES "user"(name)
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT node_type FOREIGN KEY (type_id)
  REFERENCES "type"(id)
  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT "count" CHECK (count >= 0)
);
CREATE UNIQUE INDEX node_id ON "node" USING btree ("id");


CREATE TABLE "income" (
  id SERIAL PRIMARY KEY NOT NULL,
  node_id INT  NOT NULL,
  user_name VARCHAR(24) NOT NULL,
  type_id SMALLINT NOT NULL,
  time INT NOT NULL,
  CONSTRAINT income_user FOREIGN KEY (user_name)
  REFERENCES "user"(name)
  ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE UNIQUE INDEX income_id ON "income" USING btree ("id");


CREATE TABLE "feedback" (
  id SERIAL PRIMARY KEY NOT NULL,
  username VARCHAR(24) NOT NULL,
  email VARCHAR(48),
  subject VARCHAR(256) NOT NULL,
  content TEXT NOT NULL
);
CREATE UNIQUE INDEX feedback_id ON "feedback" USING btree ("id");


CREATE TABLE "visit_agent" (
  "id" SERIAL PRIMARY KEY,
  "agent" VARCHAR(200),
  "ip" INET
);
CREATE INDEX visit_agent_agent ON "visit_agent" USING btree ("agent");
CREATE INDEX visit_agent_ip ON "visit_agent" USING btree ("ip");


CREATE TABLE "visit_path" (
  "id" SERIAL PRIMARY KEY,
  "agent_id" INT NOT NULL,
  "path" VARCHAR(80) NOT NULL,
  "spend" SMALLINT,
  "time" TIMESTAMP DEFAULT current_timestamp,
  CONSTRAINT user_agent FOREIGN KEY (agent_id)
  REFERENCES "visit_agent"("id")
  ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE VIEW "visit" AS
  SELECT p.id as id, agent_id, spend, "path", "time", ip, agent FROM visit_path p
    JOIN visit_agent a ON agent_id = a.id;
