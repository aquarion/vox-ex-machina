from .base import *

DEBUG = True


ALLOWED_HOSTS = ['127.0.0.1', 'vox.melior.istic.net', 'localhost']

DEFAULT_FROM_EMAIL="vox Admin DEV <support@istic.net>"
DEFAULT_FROM_DOMAIN='vox.melior.istic.net:8080'

import os 
os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'

POSTMARK = {
    'TOKEN': os.environ.get('POSTMARK_API', ''),
    'TEST_MODE': False,
    'VERBOSITY': 1,
    'TRACK_OPENS': False
}

DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': 'postgres',
        'USER': 'postgres',
        'HOST': 'db',
        'PORT': 5432,
    }
}
# DATABASES = {
#     'default': {
#         'ENGINE': 'django.db.backends.sqlite3',
#         'NAME': os.path.join(BASE_DIR, 'db.sqlite3'),
#     }
# }