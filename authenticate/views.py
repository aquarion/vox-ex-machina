from django.shortcuts import render
from django.contrib.sites.models import Site
from django.contrib.auth.models import User

from rest_framework import routers, serializers, viewsets
from rest_framework.decorators import list_route

from django.http import HttpResponseRedirect

import google.oauth2.credentials
import google_auth_oauthlib.flow

from authenticate.models import GoogleAccount

# Create your views here.

class GoogleExhangeViewSet(viewsets.ViewSet):
    queryset = User.objects.all()

    SCOPES = ['https://www.googleapis.com/auth/contacts.readonly']

    @list_route(methods=["GET"])
    def auth(self,request,pk=None):
        flow = google_auth_oauthlib.flow.Flow.from_client_secrets_file(
                                    'keys/client_secret.json',
                                    scopes=self.SCOPES,
                                    state="toast",
                                    redirect_uri='http://localhost:8000/api/googleAuth/complete')

        authorization_url, state = flow.authorization_url(
            access_type='offline',
            include_granted_scopes='true',
            prompt='consent'
            )
        request.session['state'] = state

        return HttpResponseRedirect(authorization_url)

    @list_route(methods=["GET"])
    def complete(self, request, pk=None):
        host = Site.objects.get_current().name
        state = request.session['state'];


        flow = google_auth_oauthlib.flow.Flow.from_client_secrets_file(
                                    'keys/client_secret.json',
                                    scopes=self.SCOPES,
                                    state=state,
                                    redirect_uri='http://localhost:8000/api/googleAuth/complete')

        flow.fetch_token(authorization_response=request.build_absolute_uri())

        credentials = flow.credentials

        request.session['credentials'] = {
            'token': credentials.token,
            'refresh_token': credentials.refresh_token,
            'token_uri': credentials.token_uri,
            'client_id': credentials.client_id,
            'client_secret': credentials.client_secret,
            'scopes': credentials.scopes
        }

        print(credentials)

        return HttpResponseRedirect("/")

