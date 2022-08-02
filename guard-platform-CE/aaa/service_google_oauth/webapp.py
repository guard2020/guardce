from flask import Flask, request, redirect, session, url_for
from flask.json import jsonify
from requests_oauthlib import OAuth2Session

import base64
import configparser
import re
import os
import json

app = Flask(__name__)

config = configparser.ConfigParser()
config.read('webapp.conf')

client_id            = config['webapp']['client_id']
client_secret        = config['webapp']['client_secret']
base_is_url_internal = config['webapp']['base_is_url_internal']
base_is_url_external = config['webapp']['base_is_url_external']
base_testcase_url    = config['webapp']['base_testcase_url']
redirect_uri         = config['webapp']['redirect_uri']

authorization_uri = f'{base_is_url_external}/authorize'
token_uri = f'{base_is_url_internal}/token'

@app.route("/")
def getRoot():
    """Step 1: User Authorization.

    Redirect the user/resource owner to the OAuth provider (i.e. WSO2)
    using an URL with a few key OAuth parameters.
    """
    wso2 = OAuth2Session(client_id, redirect_uri=redirect_uri, scope=['email'])
    authorization_url, state = wso2.authorization_url(authorization_uri)

    # State is used to prevent CSRF, keep this for later.
    session['oauth_state'] = state
    return redirect(authorization_url)


# Step 2: User authorization, this happens on the provider.

@app.route("/callback", methods=["GET"])
def callback():
    """ Step 3: Retrieving an access token.

    The user has been redirected back from the provider to your registered
    callback URL. With this redirection comes an authorization code included
    in the redirect URL. We will use that to obtain an access token.
    """

    wso2 = OAuth2Session(client_id, state=session['oauth_state'], redirect_uri=redirect_uri, scope=['email'])
    token = wso2.fetch_token(token_uri,
                             client_secret=client_secret,
                             authorization_response=request.url,
                             verify='wso2.pem')

    # At this point you can fetch protected resources but lets save
    # the token and show how this is done from a persisted token
    # in /debug.
    session['oauth_token'] = token

    return redirect(url_for('.debug'))


@app.route("/debug", methods=["GET"])
def debug():
    ## Debug JWT
    raw_jwt = decode_base64(session['oauth_token']['access_token'].split('.')[1])
    jwt = json.loads(raw_jwt.decode())

    return jsonify({
        'token': session['oauth_token'],
        'jwt': jwt
    })

def decode_base64(data, altchars=b'+/'):
    """Decode base64, padding being optional.

    :param data: Base64 data as an ASCII byte string
    :returns: The decoded byte string.
    """
    data = data.encode()
    data = re.sub(rb'[^a-zA-Z0-9%s]+' % altchars, b'', data)  # normalize
    missing_padding = len(data) % 4
    if missing_padding:
        data += b'='* (4 - missing_padding)
    return base64.b64decode(data, altchars)

if __name__ == "__main__":
    # This allows us to use a plain HTTP callback
    os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = "1"

    app.secret_key = os.urandom(24)
    app.run(debug=True, host='0.0.0.0')
