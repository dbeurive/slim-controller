```json
{
  "profile": {
    "controller": {
      "class": "\\dbeurive\\Slim\\Test\\controller0\\ProfileController",
      "path": "/path/to/controllers/ProfileController.php"
    },
    "actions": [
      {
        "http-method": "post",
        "action-uri": "set",
        "method": "actionPostSet"
      },
      {
        "http-method": "get",
        "action-uri": "get/{id}",
        "method": "actionGetGet"
      }
    ]
  },
  "user": {
    "controller": {
      "class": "\\dbeurive\\Slim\\Test\\controller0\\UserController",
      "path": "/path/to/controllers/UserController.php"
    },
    "actions": [
      {
        "http-method": "post",
        "action-uri": "login",
        "method": "actionPostLogin"
      },
      {
        "http-method": "get",
        "action-uri": "get/{id}",
        "method": "actionGetGet"
      }
    ]
  }
}
```
