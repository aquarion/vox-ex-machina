from django.db import models
from django.contrib.auth.models import User

class GoogleAccount(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)

    description = models.TextField(max_length=500, blank=True)

    token_json = models.TextField(max_length=1024, blank=True)
