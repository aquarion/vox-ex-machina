from .base import *

DEBUG = True


ALLOWED_HOSTS = ['127.0.0.1', 'vox.melior.istic.net']

DEFAULT_FROM_EMAIL="vox Admin DEV <support@istic.net>"
DEFAULT_FROM_DOMAIN='vox.melior.istic.net:8080'

POSTMARK = {
    'TOKEN': os.environ.get('POSTMARK_API', ''),
    'TEST_MODE': False,
    'VERBOSITY': 1,
    'TRACK_OPENS': False
}


DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.sqlite3',
        'NAME': os.path.join(BASE_DIR, 'db.sqlite3'),
    }
}