from django.db import models
from django.contrib.auth.models import User

class GoogleAccount(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    description = models.TextField(blank=True, max_length=2048)
    token_json = models.CharField(blank=True, max_length=2048)

