import os

env = os.environ.get('ENVIRONMENT', 'dev')

if env == 'production':
	from .prod import *
elif env == 'development':
	from .dev import *