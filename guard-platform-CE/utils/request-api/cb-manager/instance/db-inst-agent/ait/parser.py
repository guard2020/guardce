import json

file = open("config.yml")

data = {"id": "aminer@aminer",
        "operations": [
            {
                "resources": [
                    {
                        "id": "config-file",
                        "content": [
                            file.read()
                        ]
                    }
                ]
            }
        ]
        }

with open('config.json', 'w') as outfile:
    json.dump(data, outfile)
