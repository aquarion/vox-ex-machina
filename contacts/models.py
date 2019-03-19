from django.db import models

from google.auth.transport.requests import AuthorizedSession
import google.oauth2.credentials 

class GoogleContacts:
    credentials = False

    def __init__(self, credentials):

        print(credentials)
        self.credentials = google.oauth2.credentials.Credentials(**credentials)


    def all_contacts(self):
        authed_session = AuthorizedSession(self.credentials)

        url = 'https://people.googleapis.com/v1/people/me/connections?personFields=names,emailAddresses,nicknames,phoneNumbers,organizations'

        response = authed_session.request('GET', url)

        return response.json()