# -*- coding: utf-8 -*-
from __future__ import division, unicode_literals
import math, operator
from textblob import TextBlob
from scipy import spatial

def tf(word, blob):
    return blob.words.count(word) / len(blob.words)

def n_containing(word, bloblist):
    return sum(1 for blob in bloblist if word in blob.words)

def idf(word, bloblist):
    return math.log(len(bloblist) / (1 + n_containing(word, bloblist)))

def tfidf(word, blob, bloblist):
    return tf(word, blob) * idf(word, bloblist) 

def preprocess(bloblist, stopwords):
    bloblist = [blob.lower() for blob in bloblist]
    for blob in bloblist:
        list_temp = [word for word in blob.words if word not in stopwords]
        blob = TextBlob(' '.join(list_temp))
    return bloblist

stopwords = ['href', 'class', 'title', 'blue', 'div', 'http']

def get_similar_articles(ds):    
    content_list = [TextBlob(repr(ds['content'][idx])) for idx, row in ds.iterrows()]
    # Remove stopwords from articles
    bloblist = preprocess(content_list, stopwords)
    # Get word frequency in each article using tfidf
    scores_list = []
    for i, blob in enumerate(bloblist):
        scores = {word: tfidf(word, blob, bloblist) for word in blob.words}
        sorted_words = sorted(scores.items(), key=lambda x: x[1], reverse=True)
        scores_list.append(scores)
    # Get similarity between articles using cosine similarity
    distances_list = []
    for i,scores in enumerate(scores_list):
        distances = {}
        for j,scores_tmp in enumerate(scores_list):
            distances[j+1] = 1 - spatial.distance.cosine(scores.values()[:13],scores_tmp.values()[:13]) 
        distances_touple = sorted(distances.items(), key=operator.itemgetter(1), reverse=True)
        distances_list.append([(y,x) for x,y in distances_touple])
    return [sum(distances[1:],()) for distances in distances_list]
