# Documents

## Database

### logs

To store access info.

| id                     | user_id           | key              | created_at         | updated_at         |
| ---------------------- | ----------------- | ---------------- | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign(nullable) | string(nullable) | datetime(nullable) | datetime(nullable) |

### log_infos

To store access user.

| id                     | log_id  | ip_address | user_agent | device | browser | created_at         | updated_at         |
| ---------------------- | ------- | ---------- | ---------- | ------ | ------- | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign | ipAddress  | text       | string | string  | datetime(nullable) | datetime(nullable) |

### log_requests

To store requests.

| id                     | log_id  | path   | route_name       | method | queries                             | bodies                              | created_at         | updated_at         |
| ---------------------- | ------- | ------ | ---------------- | ------ | ----------------------------------- | ----------------------------------- | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign | string | string(nullable) | string | longText(encrypted:array, nullable) | longText(encrypted:array, nullable) | datetime(nullable) | datetime(nullable) |

### log_heads

To store request headers.

| id                     | log_id  | head_key | head_value | created_at         | updated_at         |
| ---------------------- | ------- | -------- | ---------- | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign | string   | longText   | datetime(nullable) | datetime(nullable) |

### log_servers

To store request servers.

| id                     | log_id  | server_key | server_value | created_at         | updated_at         |
| ---------------------- | ------- | ---------- | ------------ | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign | string     | longText     | datetime(nullable) | datetime(nullable) |

### log_responses

To store responses.

| id                     | log_id  | status_code | resources                          | created_at         | updated_at         |
| ---------------------- | ------- | ----------- | ---------------------------------- | ------------------ | ------------------ |
| bigInt(autoIncreament) | foreign | int         | longText(encrypted:array,nullable) | datetime(nullable) | datetime(nullable) |
