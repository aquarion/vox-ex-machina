"""vox URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/2.1/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.urls import path
from django.conf.urls import include, url

from rest_framework import routers, serializers, viewsets

from information import views as infoviews
from authenticate.views import GoogleExhangeViewSet

from contacts import views as contact_views

api_router = routers.DefaultRouter()
api_router.register(r'googleAuth', GoogleExhangeViewSet)

urlpatterns = [
	path('', infoviews.index, name='index'),
    path('admin/', admin.site.urls),
    # <django-registration>
    url(r'^accounts/', include('django_registration.backends.activation.urls')),
    url(r'^accounts/', include('django.contrib.auth.urls')),
    # </django-registration>

    path('contacts/', contact_views.index),

    url(r'api/', include(api_router.urls)),
    
]
