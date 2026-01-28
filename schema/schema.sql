-- All of the schemas in one file for easy db setup
USE json_converter;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(1024) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    input_data_path VARCHAR(1024) NOT NULL,
    s_expression_path VARCHAR(1024),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (id, username, password) VALUES
(132094, "admin", "$2y$10$g1ThibIzHJWxqEgLEmEI3OxyNfEzetJoAWy5lR.jQcCsl3QCjDBD2"); 

-- Example 1
INSERT INTO history (id, user_id, name, description, input_data_path, s_expression_path) 
VALUES (1001, 132094, "And query", "And `and` function executed for two keys of an object", 
'{
  "left": true,
  "right": false
}', 
'(. and
  (."left" id)
  (."right" id)
)');

-- Example 2
INSERT INTO history (id, user_id, name, description, input_data_path, s_expression_path) 
VALUES (1002, 132094, "Correct/wrong query", "And `and` function executed for two keys of an object and returns correct or wrong", 
'{
  "left": true,
  "right": true
}', 
'(. if 
  (. and
    (."left" id)
    (."right" id)
  )
  (. literal ("correct"))
  (. literal ("wrong"))
)');

-- Example 3
INSERT INTO history (id, user_id, name, description, input_data_path, s_expression_path) 
VALUES (1003, 132094, "A simple substring query", "", 
'{
  "text": "hello world",
  "start": 6,
  "len": 5
}', 
'(. substr
  (."text" id)
  (."start" id)
  (."len" id)
)');

-- Example 4
INSERT INTO history (id, user_id, name, description, input_data_path, s_expression_path) 
VALUES (1004, 132094, "Get the squares of odd numbers", "", 
'[1, 2, 3, 4, 5, 6, 7, 8]', 
'(. pipe
  (. filter
    (. id)
    (. eq
      (. mod (. id) (. literal (2)))
      (. literal (1))
    )
  )
  (. map
    (. id)
    (. mult (. id) (. id))
  )
)');

-- Example 5
INSERT INTO history (id, user_id, name, description, input_data_path, s_expression_path) 
VALUES (1005, 132094, "Get the sum of array", "", 
'{
  "arr": [1,2,3,4,5,6,7,8,9,10]
}', 
'(. keyval
  (. literal ("sum"))
  (."arr" reduce
    (. id)
    (. literal (0))
    (. add
      (."acc" id)
      (."elem" id)
    )
  )
)');
