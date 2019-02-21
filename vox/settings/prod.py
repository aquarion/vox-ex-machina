from .base import *

DEBUG = False

ALLOWED_HOSTS = [‘app.project_name.com’, ]

DEFAULT_FROM_EMAIL='vox Admin System <support@istic.net>'

POSTMARK = {
    'TOKEN': os.environ.get('POSTMARK_API', ''),
    'TEST_MODE': False,
    'VERBOSITY': 0,
    'TRACK_OPENS': True
}


DATABASES = {
    # 'default': {
    #     'ENGINE': 'django.db.backends.sqlite3',
    #     'NAME': os.path.join(BASE_DIR, 'db.sqlite3'),
    # }
}