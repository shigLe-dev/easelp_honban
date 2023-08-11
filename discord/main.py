import requests
from flask  import *

app = Flask(__name__)

@app.route("/send/", methods=["GET"])
def send_message():

    api_url = ""

    user_id = str(request.args.get("user",""))

    print(user_id)

    with open("../setting/"+user_id+".csv",encoding="utf-8") as f:
        for line in f:
            split_line = line.replace("\n","").split(",")
            if split_line[0] == "webhook":
                api_url = split_line[1]
                pass
            pass
        pass
        

    message_data = {
        "username": "easeLp Bot",
        "content": str(request.args.get("url",""))
    }

    res = requests.post(api_url, json=message_data)
    return ""


app.run(port=6000)
