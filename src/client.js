$(document).ready(async function() {
    const doc = document;
    const history = doc.getElementById('chat-history');
    const host = '127.0.0.1';
    const port = 8088;

    $('#chat-form').submit(async function(e) {
        e.preventDefault();

        let form = $(this)[0];

        let formData = new FormData(form);

        let msg = formData.get('message').trim();

        if (msg) {
            form.reset();

            let d = new Date();

            let timestamp = `${d.toLocaleDateString()} - ${d.toLocaleTimeString()}`;

            try {
                let request = await sendMessage({
                    t: Date.now(),
                    type: 1,
                    username: username,
                    message: msg,
                    time: timestamp,
                    token: userToken,
                });
            } catch (err) {
                console.log(err);
            }
        }
    });

    async function sendMessage(msg) {
        return new Promise(function(resolve, reject) {
            let http = new XMLHttpRequest();

            http.open('POST', `http://${host}:${port}`, true);

            http.setRequestHeader("Content-type", "application/json");

            http.send(JSON.stringify(msg));

            http.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200 || this.status == 204) {
                        resolve(this.responseText);
                    } else {
                        reject(this.status, this.readyState);
                    }
                }
            }
        });
    }

    async function requestMessages() {
        return new Promise(function(resolve, reject) {
            let http = new XMLHttpRequest();

            http.open('POST', `php/client.php`, true);

            http.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        resolve(this.responseText);
                    } else {
                        reject(this.response);
                    }
                }
            }

            http.send();
        });
    }

    let request = async function() {
        try {
            let msgs = await requestMessages();

            let response = msgs.split("\r\n");

            history.value = '';

            for (let msg of response) {
                if (msg) {
                    let parsed = JSON.parse(msg);

                    history.value += `(${parsed.time}) ${parsed.username}=> ${parsed.message}\n`;
                }
            }

            return request();
        } catch (err) {
            console.log(err);
        }
    };

    //Trigger
    request();
});