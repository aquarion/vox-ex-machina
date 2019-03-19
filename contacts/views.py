from django.shortcuts import render
from django.http import HttpResponse

from pygments.formatters import HtmlFormatter
from pygments.lexers import JsonLexer
from pygments import highlight

from pprint import pformat, pprint

from simplejson import dumps as json_encode 

from contacts.models import GoogleContacts

# Create your views here.


import google.oauth2.credentials 

def index(request):

    service = GoogleContacts(request.session['credentials'])

    pprint(request.session['credentials'])

    contacts = service.all_contacts()


    html = "<html><style>{}</style><body><p>{}</p><p>{}</p></body></html>".format(
        HtmlFormatter().get_style_defs(''),

        highlight(json_encode(request.session['credentials'], indent=2), JsonLexer(), HtmlFormatter(lineseparator="<br>")), 

        highlight(json_encode(contacts, indent=2), JsonLexer(), HtmlFormatter()), 

        ) 

    return HttpResponse(html)