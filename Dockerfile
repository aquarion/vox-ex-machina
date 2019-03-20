FROM python:3
ENV PYTHONUNBUFFERED 1
RUN mkdir /code
WORKDIR /code
COPY requirements.pip /code/
RUN pip install -r requirements.pip
COPY . /code/