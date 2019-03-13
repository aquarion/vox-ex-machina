from django.shortcuts import render
from django.http import HttpResponse
from pygments import highlight, lexers, formatters

from pygments.formatters import HtmlFormatter
from pygments.lexers import JsonLexer
from pygments import highlight

from pprint import pformat, pprint

from google.auth.transport.requests import AuthorizedSession

from apiclient.discovery import build

from simplejson import dumps as json_encode 


# Create your views here.


import google.oauth2.credentials 

def index(request):

    pprint(request.session['credentials'])

    credentials = google.oauth2.credentials.Credentials(**request.session['credentials'])

    authed_session = AuthorizedSession(credentials)

    url = 'https://people.googleapis.com/v1/people/me/connections?personFields=names,emailAddresses,nicknames,phoneNumbers,organizations'

    response = authed_session.request('GET', url)

    html = "<html><style>{}</style><body><pre>{}</pre><pre>{}</pre></body></html>".format(
        HtmlFormatter().get_style_defs(''),

        highlight(json_encode(request.session['credentials']), JsonLexer(), HtmlFormatter()), 

        highlight(response.text, JsonLexer(), HtmlFormatter())
        ) 

    return HttpResponse(html)