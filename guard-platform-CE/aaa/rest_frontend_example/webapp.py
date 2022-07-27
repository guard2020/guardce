from flask import Flask, request, redirect, session, url_for
from flask.json import jsonify
from requests_oauthlib import OAuth2Session

import configparser
import os

# Author: Giovanni Grieco <giovanni.grieco@poliba.it>

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
    wso2 = OAuth2Session(client_id, redirect_uri=redirect_uri, scope=['openid'])
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

    wso2 = OAuth2Session(client_id, state=session['oauth_state'], redirect_uri=redirect_uri, scope=['openid'])
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
    return retrieve_protected_resource("debug")

@app.route("/user", methods=["GET"])
def user():
    return retrieve_protected_resource("user")

@app.route("/admin", methods=["GET"])
def admin():
    return retrieve_protected_resource("admin")

def retrieve_protected_resource(res_name):
    """
    Fetching a protected resource using an OAuth 2 token.
    """
    wso2 = OAuth2Session(client_id, token=session['oauth_token'])

    r = wso2.get(f'{base_testcase_url}/{res_name}')

    return jsonify({
        'status_code': r.status_code,
        'response': r.text
    })

if __name__ == "__main__":
    # This allows us to use a plain HTTP callback
    os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = "1"

    app.secret_key = os.urandom(24)
    app.run(debug=True, host='0.0.0.0')
